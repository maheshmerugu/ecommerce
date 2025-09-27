<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Cart;

class UpdatePaymentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:update {order_id} {payment_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update order with payment information';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orderId = $this->argument('order_id');
        $paymentId = $this->argument('payment_id');
        
        $order = Order::find($orderId);
        
        if (!$order) {
            $this->error("Order with ID {$orderId} not found!");
            return;
        }
        
        $order->razorpay_payment_id = $paymentId;
        $order->payment_status = 'paid';
        $order->status = 'processing';  // Use valid enum value
        $order->save();
        
        $this->info("Order updated successfully!");
        $this->info("Payment Status: " . $order->payment_status);
        $this->info("Order Status: " . $order->status);
        
        // Clear the cart for the customer
        $cart = Cart::where('customer_id', $order->customer_id)->first();
        if ($cart) {
            $cart->items()->delete();
            $this->info("Cart cleared successfully!");
        }
        
        $this->info("Order success URL: http://127.0.0.1:8000/checkout/success/" . $order->id);
    }
}
