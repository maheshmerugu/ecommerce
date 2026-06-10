@extends('admin.layouts.app')

@section('title', 'Customers')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Customers</h1>
    </div>

    <!-- Search & Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-6">
                    <input type="text" class="form-control" name="search"
                           placeholder="Search by name, email or phone..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Orders</th>
                            <th>Joined</th>
                            <th>Last Login</th>
                            <th>Status</th>
                            <th width="100">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                        <tr>
                            <td>
                                <strong>{{ $customer->full_name }}</strong>
                            </td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->phone ?? '—' }}</td>
                            <td><span class="badge badge-info">{{ $customer->orders_count }}</span></td>
                            <td><small>{{ $customer->created_at->format('d M Y') }}</small></td>
                            <td>
                                <small>{{ $customer->last_login_at ? $customer->last_login_at->diffForHumans() : 'Never' }}</small>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.customers.toggle-status', $customer) }}"
                                      style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="btn btn-sm btn-{{ $customer->is_active ? 'success' : 'secondary' }}">
                                        {{ $customer->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>
                            <td>
                                <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-users fa-3x mb-3"></i><br>No customers found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($customers->hasPages())
                <div class="d-flex justify-content-center mt-4">{{ $customers->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
