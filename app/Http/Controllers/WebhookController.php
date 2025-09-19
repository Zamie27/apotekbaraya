<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class WebhookController extends Controller
{
    /**
     * Handle Midtrans payment notification webhook
     */
    public function midtransNotification(Request $request)
    {
        try {
            // Get notification data
            $notification = $request->all();
            
            Log::info('Midtrans webhook received', $notification);
            
            // Verify signature
            if (!$this->verifyMidtransSignature($notification)) {
                Log::warning('Invalid Midtrans signature', $notification);
                return response('Invalid signature', 403);
            }
            
            // Extract transaction data
            $orderId = $notification['order_id'] ?? null;
            $transactionStatus = $notification['transaction_status'] ?? null;
            $fraudStatus = $notification['fraud_status'] ?? null;
            $paymentType = $notification['payment_type'] ?? null;
            $grossAmount = $notification['gross_amount'] ?? null;
            
            if (!$orderId) {
                Log::error('Order ID not found in notification', $notification);
                return response('Order ID required', 400);
            }
            
            // Find order by order number
            $order = Order::where('order_number', $orderId)
                ->with('payment')
                ->first();
                
            if (!$order) {
                Log::error('Order not found', ['order_id' => $orderId]);
                return response('Order not found', 404);
            }
            
            // Process payment status
            $this->processPaymentStatus($order, $notification);
            
            return response('OK', 200);
            
        } catch (\Exception $e) {
            Log::error('Error processing Midtrans webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'notification' => $request->all()
            ]);
            
            return response('Internal Server Error', 500);
        }
    }
    
    /**
     * Verify Midtrans signature for security
     */
    private function verifyMidtransSignature(array $notification): bool
    {
        $serverKey = config('services.midtrans.server_key');
        $orderId = $notification['order_id'] ?? '';
        $statusCode = $notification['status_code'] ?? '';
        $grossAmount = $notification['gross_amount'] ?? '';
        
        $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        $receivedSignature = $notification['signature_key'] ?? '';
        
        return hash_equals($signatureKey, $receivedSignature);
    }
    
    /**
     * Process payment status and update order accordingly
     */
    private function processPaymentStatus(Order $order, array $notification): void
    {
        $transactionStatus = $notification['transaction_status'];
        $fraudStatus = $notification['fraud_status'] ?? null;
        $paymentType = $notification['payment_type'] ?? null;
        $transactionId = $notification['transaction_id'] ?? null;
        $transactionTime = $notification['transaction_time'] ?? null;
        
        DB::beginTransaction();
        
        try {
            // Update payment record
            if ($order->payment) {
                $paymentData = [
                    'transaction_id' => $transactionId,
                    'payment_type' => $paymentType,
                    'transaction_time' => $transactionTime ? date('Y-m-d H:i:s', strtotime($transactionTime)) : null,
                    'updated_at' => now()
                ];
                
                // Determine payment and order status based on transaction status
                switch ($transactionStatus) {
                    case 'capture':
                        if ($fraudStatus === 'accept') {
                            $paymentData['status'] = 'paid';
                            $paymentData['paid_at'] = now();
                            $this->updateOrderStatus($order, 'waiting_confirmation');
                        } else {
                            $paymentData['status'] = 'pending';
                        }
                        break;
                        
                    case 'settlement':
                        $paymentData['status'] = 'paid';
                        $paymentData['paid_at'] = now();
                        $this->updateOrderStatus($order, 'waiting_confirmation');
                        break;
                        
                    case 'pending':
                        $paymentData['status'] = 'pending';
                        break;
                        
                    case 'deny':
                    case 'expire':
                        $paymentData['status'] = 'failed';
                        $paymentData['failed_at'] = now();
                        $this->updateOrderStatus($order, 'cancelled');
                        break;
                        
                    case 'cancel':
                        $paymentData['status'] = 'cancelled';
                        $paymentData['failed_at'] = now();
                        $this->updateOrderStatus($order, 'cancelled');
                        break;
                        
                    case 'failure':
                        $paymentData['status'] = 'failed';
                        $paymentData['failed_at'] = now();
                        break;
                        
                    default:
                        Log::warning('Unknown transaction status', [
                            'order_id' => $order->order_id,
                            'transaction_status' => $transactionStatus
                        ]);
                        break;
                }
                
                $order->payment->update($paymentData);
            }
            
            // Log the webhook processing
            Log::info('Midtrans webhook processed successfully', [
                'order_id' => $order->order_id,
                'order_number' => $order->order_number,
                'transaction_status' => $transactionStatus,
                'payment_status_value' => $order->payment->status ?? null,
                'order_status' => $order->status
            ]);
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Update order status with proper logging
     */
    private function updateOrderStatus(Order $order, string $newStatus): void
    {
        $oldStatus = $order->status;
        
        $updateData = ['status' => $newStatus];
        
        // Add timestamp for specific statuses
        switch ($newStatus) {
            case 'waiting_confirmation':
                $updateData['confirmed_at'] = now();
                break;
            case 'cancelled':
                $updateData['cancelled_at'] = now();
                $updateData['cancellation_reason'] = 'Pembayaran gagal atau dibatalkan';
                break;
        }
        
        $order->update($updateData);
        
        Log::info('Order status updated via webhook', [
            'order_id' => $order->order_id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus
        ]);
    }
}