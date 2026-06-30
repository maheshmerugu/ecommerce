<x-mail::message>
# Order Cancelled

Hi {{ $order->customer_name }},

We're writing to inform you that your order **#{{ $order->order_number }}** has been cancelled.

**Order Details:**
- Order Number: #{{ $order->order_number }}
- Total Amount: ₹{{ number_format($order->total, 2) }}
- Payment Method: {{ ucfirst($order->payment_method ?? 'Razorpay') }}

@if($refundInitiated)
## Refund Information

A refund of **₹{{ number_format($order->total, 2) }}** has been initiated. Please allow 5-7 business days for the amount to reflect in your account.

If your refund doesn't arrive within the expected time, please contact our support team.
@endif

@if($order->items && $order->items->count() > 0)
**Cancelled Items:**

@foreach($order->items as $item)
- {{ $item->product_name }} (Qty: {{ $item->quantity }}) — ₹{{ number_format($item->total, 2) }}
@endforeach
@endif

If you have any questions about this cancellation, please contact our support team.

<x-mail::button :url="config('app.url')">
Visit Our Store
</x-mail::button>

Regards,<br>
{{ $appName }}
</x-mail::message>
