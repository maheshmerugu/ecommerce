@extends('admin.layouts.app')

@section('title', 'Orders Management')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Orders Management</h1>
    </div>

    <!-- Search & Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search"
                           placeholder="Order #, customer name or email..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="">All Statuses</option>
                        @foreach(['pending','processing','shipped','delivered','cancelled','refunded'] as $s)
                            <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="payment_status" class="form-control">
                        <option value="">All Payment Statuses</option>
                        @foreach(['pending','paid','failed'] as $ps)
                            <option value="{{ $ps }}" {{ request('payment_status') == $ps ? 'selected' : '' }}>{{ ucfirst($ps) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th width="140">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td><strong>{{ $order->order_number }}</strong></td>
                            <td>
                                {{ $order->customer_name }}
                                <br><small class="text-muted">{{ $order->customer_email }}</small>
                            </td>
                            <td>{{ $order->items->count() }}</td>
                            <td><strong>₹{{ number_format($order->total, 2) }}</strong></td>
                            <td>
                                @php
                                    $pColors = ['paid'=>'success','pending'=>'warning','failed'=>'danger'];
                                    $pc = $pColors[$order->payment_status] ?? 'secondary';
                                @endphp
                                <span class="badge badge-{{ $pc }}">{{ ucfirst($order->payment_status) }}</span>
                            </td>
                            <td>
                                @php
                                    $sColors = ['pending'=>'warning','processing'=>'info','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger','refunded'=>'secondary'];
                                    $sc = $sColors[$order->status] ?? 'secondary';
                                @endphp
                                <span class="badge badge-{{ $sc }}">{{ ucfirst($order->status) }}</span>
                            </td>
                            <td><small>{{ $order->created_at->format('d M Y') }}</small></td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(in_array($order->status, ['pending', 'processing', 'shipped']))
                                        <form method="POST" action="{{ route('admin.orders.deliver', $order) }}" class="d-inline"
                                              onsubmit="return confirm('Mark this order as delivered?')">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success" title="Mark Delivered">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if(in_array($order->status, ['pending', 'processing']))
                                        <form method="POST" action="{{ route('admin.orders.cancel', $order) }}" class="d-inline"
                                              onsubmit="return confirm('Cancel this order? Stock will be restored and refund will be initiated if payment was made.')">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Cancel Order">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-shopping-bag fa-3x mb-3"></i><br>No orders found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($orders->hasPages())
                <div class="d-flex justify-content-center mt-4">{{ $orders->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
