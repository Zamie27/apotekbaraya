<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $midtransService;

    /**
     * Initialize PaymentController with MidtransService
     */
    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Handle Midtrans notification callback
     */
    public function notification(Request $request)
    {
        try {
            $notification = $request->all();
            
            Log::info('Midtrans notification received', $notification);

            // Verify signature
            if (!$this->midtransService->verifySignature($notification)) {
                Log::warning('Invalid Midtrans signature', $notification);
                return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 400);
            }

            $orderId = $notification['order_id'];
            $transactionStatus = $notification['transaction_status'];
            $fraudStatus = $notification['fraud_status'] ?? null;
            $paymentType = $notification['payment_type'];
            $grossAmount = $notification['gross_amount'];

            // Find order
            $order = Order::where('order_number', $orderId)->first();
            if (!$order) {
                Log::error('Order not found for Midtrans notification', ['order_id' => $orderId]);
                return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
            }

            // Find or create payment record
            $payment = Payment::where('order_id', $order->order_id)->first();
            if (!$payment) {
                $payment = new Payment();
                $payment->order_id = $order->order_id;
                $payment->amount = $grossAmount;
            }

            // Update payment details
            $payment->payment_method = $paymentType;
            $payment->transaction_id = $notification['transaction_id'] ?? null;
            $payment->payment_data = json_encode($notification);

            DB::transaction(function () use ($order, $payment, $transactionStatus, $fraudStatus) {
                // Handle different transaction statuses
                switch ($transactionStatus) {
                    case 'capture':
                        if ($fraudStatus == 'challenge') {
                            $payment->status = 'pending';
                            $order->status = 'pending';
                        } else if ($fraudStatus == 'accept') {
                            $payment->status = 'paid';
                            $payment->paid_at = now();
                            $order->status = 'waiting_confirmation';
                            $order->waiting_confirmation_at = now();
                        }
                        break;

                    case 'settlement':
                        $payment->status = 'paid';
                        $payment->paid_at = now();
                        $order->status = 'waiting_confirmation';
                        $order->waiting_confirmation_at = now();
                        break;

                    case 'pending':
                        $payment->status = 'pending';
                        $order->status = 'pending';
                        break;

                    case 'deny':
                        $payment->status = 'failed';
                        $order->status = 'cancelled';
                        break;

                    case 'expire':
                        $payment->status = 'failed';
                        $order->status = 'cancelled';
                        break;

                    case 'cancel':
                        $payment->status = 'cancelled';
                        $order->status = 'cancelled';
                        break;

                    case 'unsuccessful':
                        $payment->status = 'failed';
                        $order->status = 'cancelled';
                        break;

                    default:
                        $payment->status = 'failed';
                        Log::warning('Unknown transaction status from Midtrans', [
                            'order_id' => $order->order_number,
                            'status' => $transactionStatus
                        ]);
                        break;
                }

                $payment->save();
                $order->save();

                Log::info('Payment and order status updated', [
                    'order_id' => $order->order_number,
                    'payment_status_value' => $payment->status,
                    'order_status' => $order->status
                ]);
            });

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Error processing Midtrans notification', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['status' => 'error', 'message' => 'Internal server error'], 500);
        }
    }

    /**
     * Handle payment finish redirect from Midtrans
     */
    public function finish(Request $request)
    {
        $orderId = $request->get('order_id');
        $statusCode = $request->get('status_code');
        $transactionStatus = $request->get('transaction_status');

        Log::info('Payment finish redirect', [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'transaction_status' => $transactionStatus
        ]);

        // Find order
        $order = Order::where('order_number', $orderId)->first();
        if (!$order) {
            return redirect()->route('home')->with('error', 'Pesanan tidak ditemukan.');
        }

        // Redirect based on transaction status
        switch ($transactionStatus) {
            case 'capture':
            case 'settlement':
                return redirect()->route('pelanggan.orders.show', $order->order_id)
                    ->with('success', 'Pembayaran berhasil! Pesanan Anda sedang diproses.');

            case 'pending':
                return redirect()->route('pelanggan.orders.show', $order->order_id)
                    ->with('info', 'Pembayaran sedang diproses. Silakan tunggu konfirmasi.');

            case 'deny':
            case 'expire':
            case 'cancel':
                return redirect()->route('pelanggan.orders.show', $order->order_id)
                    ->with('error', 'Pembayaran gagal atau dibatalkan.');

            default:
                return redirect()->route('pelanggan.orders.show', $order->order_id)
                    ->with('info', 'Status pembayaran: ' . $transactionStatus);
        }
    }

    /**
     * Check payment status manually
     */
    public function checkStatus(Request $request)
    {
        $orderId = $request->get('order_id');
        
        if (!$orderId) {
            return response()->json(['status' => 'error', 'message' => 'Order ID required'], 400);
        }

        $result = $this->midtransService->getTransactionStatus($orderId);
        
        if ($result['success']) {
            return response()->json([
                'status' => 'success',
                'data' => $result['data']
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => $result['message']
            ], 400);
        }
    }
}
