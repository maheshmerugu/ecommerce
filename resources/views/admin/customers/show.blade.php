@extends('admin.layouts.app')

@section('title', 'Customer: ' . $customer->full_name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ $customer->full_name }}</h1>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Back to Customers
        </a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <!-- Profile Card -->
            <div class="card shadow mb-4">
                <div class="card-body text-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                         style="width:80px;height:80px;">
                        <span class="text-white font-weight-bold" style="font-size:2rem;">
                            {{ strtoupper(substr($customer->first_name, 0, 1)) }}
                        </span>
                    </div>
                    <h5 class="font-weight-bold">{{ $customer->full_name }}</h5>
                    <p class="text-muted mb-1">{{ $customer->email }}</p>
                    <p class="text-muted mb-3">{{ $customer->phone ?? 'No phone' }}</p>
                    <span class="badge badge-{{ $customer->is_active ? 'success' : 'danger' }} badge-lg">
                        {{ $customer->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="card-footer">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="text-primary font-weight-bold">{{ $customer->orders->count() }}</div>
                            <small class="text-muted">Orders</small>
                        </div>
                        <div class="col-4">
                            <div class="text-success font-weight-bold">₹{{ number_format($totalSpent, 0) }}</div>
                            <small class="text-muted">Spent</small>
                        </div>
                        <div class="col-4">
                            <div class="text-info font-weight-bold">{{ $customer->addresses->count() }}</div>
                            <small class="text-muted">Addresses</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Details -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Account Details</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Gender</td>
                            <td>{{ ucfirst($customer->gender ?? '—') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Date of Birth</td>
                            <td>{{ $customer->date_of_birth ? $customer->date_of_birth->format('d M Y') : '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Joined</td>
                            <td>{{ $customer->created_at->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Last Login</td>
                            <td>{{ $customer->last_login_at ? $customer->last_login_at->diffForHumans() : 'Never' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Toggle Status -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.customers.toggle-status', $customer) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="btn btn-{{ $customer->is_active ? 'warning' : 'success' }} w-100"
                                onclick="return confirm('{{ $customer->is_active ? 'Deactivate' : 'Activate' }} this customer?')">
                            <i class="fas fa-{{ $customer->is_active ? 'ban' : 'check' }} mr-2"></i>
                            {{ $customer->is_active ? 'Deactivate Account' : 'Activate Account' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Recent Orders -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Order History</h6>
                </div>
                <div class="card-body p-0">
                    @if($customer->orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Order #</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Payment</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customer->orders->take(10) as $order)
                                    <tr>
                                        <td><strong>{{ $order->order_number }}</strong></td>
                                        <td>{{ $order->items->count() }}</td>
                                        <td>₹{{ number_format($order->total, 2) }}</td>
                                        <td>
                                            @php $pColors = ['paid'=>'success','pending'=>'warning','failed'=>'danger']; @endphp
                                            <span class="badge badge-{{ $pColors[$order->payment_status] ?? 'secondary' }}">
                                                {{ ucfirst($order->payment_status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @php $sColors = ['pending'=>'warning','processing'=>'info','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger','refunded'=>'secondary']; @endphp
                                            <span class="badge badge-{{ $sColors[$order->status] ?? 'secondary' }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td><small>{{ $order->created_at->format('d M Y') }}</small></td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-xs btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 text-center text-muted">No orders yet.</div>
                    @endif
                </div>
            </div>

            <!-- Saved Addresses -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Saved Addresses</h6>
                </div>
                <div class="card-body">
                    @if($customer->addresses->count() > 0)
                        <div class="row">
                            @foreach($customer->addresses as $address)
                            <div class="col-md-6 mb-3">
                                <div class="border rounded p-3">
                                    @if($address->is_default)
                                        <span class="badge badge-primary mb-2">Default</span>
                                    @endif
                                    <p class="mb-1 font-weight-bold">{{ $address->full_name }}</p>
                                    <p class="mb-1 small text-muted">{{ $address->address_line_1 }}</p>
                                    @if($address->address_line_2)
                                        <p class="mb-1 small text-muted">{{ $address->address_line_2 }}</p>
                                    @endif
                                    <p class="mb-1 small text-muted">{{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}</p>
                                    <p class="mb-0 small text-muted">{{ $address->phone }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No saved addresses.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
