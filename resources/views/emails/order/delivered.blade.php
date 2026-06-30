<x-mail::message>
# Order Delivered Successfully!

Hi {{ $order->customer_name }},

Great news! Your order **#{{ $order->order_number }}** has been delivered successfully.

**Order Details:**
- Order Number: #{{ $order->order_number }}
- Total Amount: ₹{{ number_format($order->total, 2) }}
- Payment Method: {{ ucfirst($order->payment_method ?? 'Razorpay') }}

@if($order->items && $order->items->count() > 0)
**Items Delivered:**

@foreach($order->items as $item)
- {{ $item->product_name }} (Qty: {{ $item->quantity }}) — ₹{{ number_format($item->total, 2) }}
@endforeach
@endif

We hope you love your purchase! If you have any questions or concerns, please don't hesitate to reach out.

<x-mail::button :url="config('app.url')">
Continue Shopping
</x-mail::button>

Thank you for shopping with us!

Regards,<br>
{{ $appName }}
</x-mail::message>
