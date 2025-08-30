<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    private $serverKey;
    private $clientKey;
    private $isProduction;
    private $isSanitized;
    private $is3ds;

    public function __construct()
    {
        $this->serverKey = config('services.midtrans.server_key');
        $this->clientKey = config('services.midtrans.client_key');
        $this->isProduction = config('services.midtrans.is_production');
        $this->isSanitized = config('services.midtrans.is_sanitized');
        $this->is3ds = config('services.midtrans.is_3ds');

        // Set Midtrans configuration
        Config::$serverKey = $this->serverKey;
        Config::$isProduction = $this->isProduction;
        Config::$isSanitized = $this->isSanitized;
        Config::$is3ds = $this->is3ds;
    }

    /**
     * Create SNAP token for payment (deprecated - use createPaymentLink instead)
     */
    public function createSnapToken($order)
    {
        try {
            $params = [
                'transaction_details' => [
                    'order_id' => $order->order_number,
                    'gross_amount' => (int) $order->total_price,
                ],
                'customer_details' => $this->getCustomerDetails($order->user),
                'item_details' => $this->getItemDetails($order),
                'callbacks' => [
                    'finish' => url('/payment/finish'),
                    'notification' => url('/payment/notification'),
                ]
            ];

            $snapToken = Snap::getSnapToken($params);
            
            return [
                'success' => true,
                'snap_token' => $snapToken
            ];
        } catch (\Exception $e) {
            \Log::error('Midtrans SNAP Token Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Create Payment Link for payment
     */
    public function createPaymentLink($order)
    {
        try {
            $url = ($this->isProduction 
                ? 'https://api.midtrans.com/v1/payment-links'
                : 'https://api.sandbox.midtrans.com/v1/payment-links');

            $params = [
                'transaction_details' => [
                    'order_id' => $order->order_number,
                    'gross_amount' => (int) $order->total_price,
                ],
                'customer_details' => [
                    'first_name' => $order->user->name,
                    'email' => $order->user->email,
                    'phone' => $order->user->phone ?? '',
                ],
                'item_details' => $this->getItemDetails($order),
                'callbacks' => [
                    'finish' => url('/payment/finish'),
                    'notification' => url('/payment/notification'),
                ],
                'expiry' => [
                    'duration' => 24,
                    'unit' => 'hours'
                ]
            ];

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':')
            ])->post($url, $params);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('Payment Link created successfully', [
                    'order_id' => $order->order_number,
                    'payment_url' => $data['payment_url'] ?? null
                ]);
                
                return [
                    'success' => true,
                    'payment_url' => $data['payment_url'],
                    'order_id' => $data['order_id'],
                    'payment_link_id' => $data['id'] ?? null
                ];
            } else {
                Log::error('Failed to create Payment Link', [
                    'order_id' => $order->order_number,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Failed to create payment link: ' . $response->body()
                ];
            }
        } catch (\Exception $e) {
            Log::error('Payment Link creation error', [
                'order_id' => $order->order_number,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get customer details with billing address for Midtrans transaction
     */
    private function getCustomerDetails($user)
    {
        // Get default address or first available address
        $defaultAddress = $user->addresses()->where('is_default', true)->first() 
                         ?? $user->addresses()->first();
        
        $customerDetails = [
            'first_name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? '',
        ];
        
        // Add billing address if available
        if ($defaultAddress) {
            $customerDetails['billing_address'] = [
                'first_name' => $defaultAddress->recipient_name ?? $user->name,
                'phone' => $defaultAddress->phone ?? $user->phone,
                'address' => $defaultAddress->detailed_address ?? $defaultAddress->address,
                'city' => $defaultAddress->regency ?? $defaultAddress->city,
                'postal_code' => $defaultAddress->postal_code ?? '',
                'country_code' => 'IDN'
            ];
        }
        
        return $customerDetails;
    }

    /**
     * Get item details for Midtrans
     */
    private function getItemDetails($order)
    {
        $items = [];
        
        foreach ($order->items as $item) {
            $items[] = [
                'id' => $item->product->product_id,
                'price' => (int) $item->price,
                'quantity' => $item->qty,
                'name' => $item->product->name,
            ];
        }
        
        // Add shipping fee if applicable
        if ($order->delivery_fee > 0) {
            $items[] = [
                'id' => 'shipping',
                'price' => (int) $order->delivery_fee,
                'quantity' => 1,
                'name' => 'Biaya Pengiriman',
            ];
        }
        
        return $items;
    }

    /**
     * Verify notification signature from Midtrans
     */
    public function verifySignature(array $notification): bool
    {
        $orderId = $notification['order_id'];
        $statusCode = $notification['status_code'];
        $grossAmount = $notification['gross_amount'];
        $serverKey = $this->serverKey;
        
        $mySignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        
        return $mySignature === $notification['signature_key'];
    }

    /**
     * Get transaction status from Midtrans
     */
    public function getTransactionStatus($orderId)
    {
        try {
            $url = $this->isProduction 
                ? "https://api.midtrans.com/v2/{$orderId}/status"
                : "https://api.sandbox.midtrans.com/v2/{$orderId}/status";
            
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':')
            ])->get($url);
            
            if ($response->successful()) {
                Log::info('Midtrans transaction status retrieved successfully', [
                    'order_id' => $orderId,
                    'response' => $response->json()
                ]);
                
                return [
                    'success' => true,
                    'message' => 'Transaction status retrieved successfully',
                    'data' => $response->json()
                ];
            } else {
                Log::error('Failed to get Midtrans transaction status', [
                    'order_id' => $orderId,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Failed to get transaction status from Midtrans',
                    'status_code' => $response->status()
                ];
            }
        } catch (\Exception $e) {
            Log::error('Error getting Midtrans transaction status', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get client key for frontend
     */
    public function getClientKey(): string
    {
        return $this->clientKey;
    }

    /**
     * Get SNAP JS URL based on environment
     */
    public function getSnapJsUrl(): string
    {
        return $this->isProduction 
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    }

    /**
     * Cancel transaction in Midtrans
     */
    public function cancelTransaction(string $orderId): array
    {
        try {
            // First, check the transaction status
            $statusResult = $this->getTransactionStatus($orderId);
            
            if (!$statusResult['success']) {
                return [
                    'success' => false,
                    'message' => 'Failed to get transaction status before cancellation'
                ];
            }
            
            $transactionStatus = $statusResult['data']['transaction_status'] ?? null;
            
            // Only cancel if transaction is in pending status
            if ($transactionStatus !== 'pending') {
                Log::info('Transaction cannot be cancelled - not in pending status', [
                    'order_id' => $orderId,
                    'current_status' => $transactionStatus
                ]);
                
                return [
                    'success' => false,
                    'message' => "Transaction cannot be cancelled. Current status: {$transactionStatus}",
                    'current_status' => $transactionStatus
                ];
            }
            
            $url = ($this->isProduction 
                ? 'https://api.midtrans.com/v2/'
                : 'https://api.sandbox.midtrans.com/v2/') . $orderId . '/cancel';

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':')
            ])->post($url);

            if ($response->successful()) {
                Log::info('Midtrans transaction cancelled successfully', [
                    'order_id' => $orderId,
                    'response' => $response->json()
                ]);
                
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
                Log::error('Failed to cancel Midtrans transaction', [
                    'order_id' => $orderId,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Failed to cancel transaction in Midtrans',
                    'status_code' => $response->status()
                ];
            }
        } catch (\Exception $e) {
            Log::error('Error cancelling Midtrans transaction', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
}