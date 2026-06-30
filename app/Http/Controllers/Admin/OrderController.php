<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderCancelledEmail;
use App\Mail\OrderDeliveredEmail;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Razorpay\Api\Api;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'items'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->paginate(20)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'items.product.images', 'payments']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
            'notes'  => 'nullable|string|max:1000',
        ]);

        $oldStatus = $order->status;
        $updates = ['status' => $request->status];

        if ($request->status === 'shipped' && !$order->shipped_at) {
            $updates['shipped_at'] = now();
        }

        if ($request->status === 'delivered' && !$order->delivered_at) {
            $updates['delivered_at'] = now();
        }

        if ($request->filled('notes')) {
            $updates['notes'] = $request->notes;
        }

        // Restore inventory and process refund when order is cancelled
        if ($request->status === 'cancelled' && $oldStatus !== 'cancelled') {
            try {
                DB::beginTransaction();

                $order->load('items.product');
                $order->update($updates);

                foreach ($order->items as $item) {
                    if ($item->product && $item->product->track_quantity) {
                        $item->product->increment('quantity', $item->quantity);
                    }
                }

                $refundInitiated = false;

                // Attempt refund via Razorpay if payment was made
                if ($order->payment_status === 'paid' && $order->razorpay_payment_id) {
                    $refundInitiated = $this->processRefund($order);
                    if ($refundInitiated) {
                        $order->update(['payment_status' => 'refunded']);
                    }
                }

                DB::commit();

                // Send cancellation email
                $this->sendCancellationEmail($order, $refundInitiated);

                $message = 'Order cancelled successfully.';
                if ($refundInitiated) {
                    $message .= ' Refund has been initiated.';
                }
                return redirect()->back()->with('success', $message);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Order cancellation failed: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Failed to cancel order: ' . $e->getMessage());
            }
        }

        // Mark as delivered (success)
        if ($request->status === 'delivered' && $oldStatus !== 'delivered') {
            $order->update($updates);
            $this->sendDeliveredEmail($order);
            return redirect()->back()->with('success', 'Order marked as delivered! Customer has been notified.');
        }

        $order->update($updates);

        return redirect()->back()->with('success', 'Order status updated successfully!');
    }

    /**
     * Quick action: mark order as delivered from orders list.
     */
    public function markDelivered(Order $order)
    {
        if ($order->status === 'delivered') {
            return redirect()->back()->with('info', 'Order is already delivered.');
        }

        $updates = ['status' => 'delivered'];
        if (!$order->delivered_at) {
            $updates['delivered_at'] = now();
        }

        $order->update($updates);
        $this->sendDeliveredEmail($order);

        return redirect()->back()->with('success', 'Order #' . $order->order_number . ' marked as delivered! Customer notified.');
    }

    /**
     * Quick action: cancel order from orders list.
     */
    public function cancelOrder(Order $order)
    {
        if ($order->status === 'cancelled') {
            return redirect()->back()->with('info', 'Order is already cancelled.');
        }

        try {
            DB::beginTransaction();

            $order->load('items.product');
            $order->update(['status' => 'cancelled']);

            foreach ($order->items as $item) {
                if ($item->product && $item->product->track_quantity) {
                    $item->product->increment('quantity', $item->quantity);
                }
            }

            $refundInitiated = false;
            if ($order->payment_status === 'paid' && $order->razorpay_payment_id) {
                $refundInitiated = $this->processRefund($order);
                if ($refundInitiated) {
                    $order->update(['payment_status' => 'refunded']);
                }
            }

            DB::commit();

            $this->sendCancellationEmail($order, $refundInitiated);

            $message = 'Order #' . $order->order_number . ' cancelled.';
            if ($refundInitiated) {
                $message .= ' Refund initiated.';
            }
            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order cancellation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to cancel order: ' . $e->getMessage());
        }
    }

    private function processRefund(Order $order): bool
    {
        try {
            $keyId = \App\Models\Setting::get('razorpay_key_id') ?: env('RAZORPAY_KEY_ID');
            $keySecret = \App\Models\Setting::get('razorpay_key_secret') ?: env('RAZORPAY_KEY_SECRET');

            if (!$keyId || !$keySecret || $keyId === 'your_razorpay_key_id') {
                Log::warning('Razorpay not configured for refund, skipping.');
                return false;
            }

            $api = new Api($keyId, $keySecret);
            $refundAmount = (int) round($order->total * 100); // Amount in paisa

            $api->payment->fetch($order->razorpay_payment_id)->refund([
                'amount' => $refundAmount,
                'speed'  => 'normal',
                'notes'  => [
                    'order_number' => $order->order_number,
                    'reason' => 'Order cancelled by admin',
                ],
            ]);

            Log::info('Refund initiated for order ' . $order->order_number, [
                'payment_id' => $order->razorpay_payment_id,
                'amount' => $refundAmount,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Refund failed for order ' . $order->order_number . ': ' . $e->getMessage());
            return false;
        }
    }

    private function sendDeliveredEmail(Order $order): void
    {
        try {
            $order->load('items');
            if ($order->customer_email) {
                Mail::to($order->customer_email)->send(new OrderDeliveredEmail($order));
                Log::info('Delivery email sent for order ' . $order->order_number);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send delivery email: ' . $e->getMessage());
        }
    }

    private function sendCancellationEmail(Order $order, bool $refundInitiated): void
    {
        try {
            $order->load('items');
            if ($order->customer_email) {
                Mail::to($order->customer_email)->send(new OrderCancelledEmail($order, $refundInitiated));
                Log::info('Cancellation email sent for order ' . $order->order_number);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send cancellation email: ' . $e->getMessage());
        }
    }
}
