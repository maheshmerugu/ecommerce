<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        // Restore inventory when order is cancelled
        if ($request->status === 'cancelled' && $order->status !== 'cancelled') {
            try {
                DB::beginTransaction();
                $order->update($updates);
                foreach ($order->items as $item) {
                    if ($item->product && $item->product->track_quantity) {
                        $item->product->increment('quantity', $item->quantity);
                    }
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Order cancellation failed: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Failed to cancel order.');
            }
        } else {
            $order->update($updates);
        }

        return redirect()->back()->with('success', 'Order status updated successfully!');
    }
}
