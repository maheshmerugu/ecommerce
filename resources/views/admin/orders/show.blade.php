@extends('admin.layouts.app')

@section('title', 'Order ' . $order->order_number)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Order {{ $order->order_number }}</h1>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Back to Orders
        </a>
    </div>

    <div class="row">
        <!-- Order Details -->
        <div class="col-md-8">
            <!-- Items -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Order Items</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th width="60">Image</th>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        @php $img = $item->product?->images?->first(); @endphp
                                        @if($img)
                                            <img src="{{ product_image_url($img->image_path) }}"
                                                 alt="{{ $item->product_name }}"
                                                 style="width:45px;height:45px;object-fit:cover;" class="img-thumbnail">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center"
                                                 style="width:45px;height:45px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $item->product_name }}</td>
                                    <td><code>{{ $item->product_sku }}</code></td>
                                    <td>₹{{ number_format($item->price, 2) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td><strong>₹{{ number_format($item->total, 2) }}</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="thead-light">
                                <tr>
                                    <td colspan="5" class="text-right font-weight-bold">Subtotal</td>
                                    <td>₹{{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-right">Shipping</td>
                                    <td>₹{{ number_format($order->shipping_amount, 2) }}</td>
                                </tr>
                                @if($order->discount_amount > 0)
                                <tr>
                                    <td colspan="5" class="text-right text-success">Discount</td>
                                    <td class="text-success">- ₹{{ number_format($order->discount_amount, 2) }}</td>
                                </tr>
                                @endif
                                @if($order->tax_amount > 0)
                                <tr>
                                    <td colspan="5" class="text-right">Tax</td>
                                    <td>₹{{ number_format($order->tax_amount, 2) }}</td>
                                </tr>
                                @endif
                                <tr class="table-active">
                                    <td colspan="5" class="text-right font-weight-bold">Total</td>
                                    <td><strong>₹{{ number_format($order->total, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Addresses -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Shipping Address</h6>
                        </div>
                        <div class="card-body">
                            @php $sa = $order->shipping_address; @endphp
                            @if($sa)
                                <address class="mb-0">
                                    {{ $sa['address'] ?? '' }}<br>
                                    {{ $sa['city'] ?? '' }}, {{ $sa['state'] ?? '' }} {{ $sa['pincode'] ?? '' }}<br>
                                    {{ $sa['country'] ?? 'India' }}
                                </address>
                            @else
                                <p class="text-muted mb-0">Not provided</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Billing Address</h6>
                        </div>
                        <div class="card-body">
                            @php $ba = $order->billing_address; @endphp
                            @if($ba)
                                <address class="mb-0">
                                    {{ $ba['address'] ?? '' }}<br>
                                    {{ $ba['city'] ?? '' }}, {{ $ba['state'] ?? '' }} {{ $ba['pincode'] ?? '' }}<br>
                                    {{ $ba['country'] ?? 'India' }}
                                </address>
                            @else
                                <p class="text-muted mb-0">Same as shipping</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Update Status -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Update Order Status</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.orders.update-status', $order) }}">
                        @csrf
                        @method('PATCH')
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Status</label>
                            <select name="status" class="form-control">
                                @foreach(['pending','processing','shipped','delivered','cancelled','refunded'] as $s)
                                    <option value="{{ $s }}" {{ $order->status == $s ? 'selected' : '' }}>
                                        {{ ucfirst($s) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label>Internal Notes</label>
                            <textarea name="notes" rows="3" class="form-control"
                                      placeholder="Add notes (optional)">{{ $order->notes }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save mr-2"></i>Update Status
                        </button>
                    </form>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Customer</h6>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>{{ $order->customer_name }}</strong></p>
                    <p class="mb-1 text-muted">{{ $order->customer_email }}</p>
                    <p class="mb-0 text-muted">{{ $order->customer_phone }}</p>
                    @if($order->customer)
                        <hr>
                        <a href="{{ route('admin.customers.show', $order->customer) }}" class="btn btn-sm btn-outline-primary">
                            View Customer Profile
                        </a>
                    @endif
                </div>
            </div>

            <!-- Payment Info -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payment</h6>
                </div>
                <div class="card-body">
                    @php
                        $pColors = ['paid'=>'success','pending'=>'warning','failed'=>'danger'];
                        $pc = $pColors[$order->payment_status] ?? 'secondary';
                    @endphp
                    <p class="mb-2">
                        Status: <span class="badge badge-{{ $pc }}">{{ ucfirst($order->payment_status) }}</span>
                    </p>
                    <p class="mb-2">Method: <strong>{{ ucfirst($order->payment_method ?? 'razorpay') }}</strong></p>
                    @if($order->razorpay_payment_id)
                        <p class="mb-0 text-muted small">Payment ID: {{ $order->razorpay_payment_id }}</p>
                    @endif
                    @if($order->razorpay_order_id)
                        <p class="mb-0 text-muted small">Razorpay Order: {{ $order->razorpay_order_id }}</p>
                    @endif
                </div>
            </div>

            <!-- Timeline -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Timeline</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2 small"><i class="fas fa-plus-circle text-success mr-2"></i>Created: {{ $order->created_at->format('d M Y H:i') }}</p>
                    @if($order->shipped_at)
                        <p class="mb-2 small"><i class="fas fa-truck text-primary mr-2"></i>Shipped: {{ $order->shipped_at->format('d M Y H:i') }}</p>
                    @endif
                    @if($order->delivered_at)
                        <p class="mb-0 small"><i class="fas fa-check-circle text-success mr-2"></i>Delivered: {{ $order->delivered_at->format('d M Y H:i') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
