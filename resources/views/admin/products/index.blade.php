@extends('admin.layouts.app')

@section('title', 'Products Management')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products Management</h1>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i>Add New Product
        </a>
    </div>

    <!-- Search and Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" placeholder="Search products..." 
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
                    <select name="featured" class="form-control">
                        <option value="">All Products</option>
                        <option value="1" {{ request('featured') == '1' ? 'selected' : '' }}>Featured</option>
                        <option value="0" {{ request('featured') == '0' ? 'selected' : '' }}>Not Featured</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th width="60">Image</th>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Categories</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th width="200">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td>
                                @if($product->image)
                                    <img src="{{ product_image_url($product->image) }}" 
                                         alt="{{ $product->name }}" 
                                         class="img-thumbnail" 
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" 
                                         style="width: 50px; height: 50px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $product->name }}</strong>
                                    @if($product->featured)
                                        <span class="badge badge-star ml-2">
                                            <i class="fas fa-star"></i> Featured
                                        </span>
                                    @endif
                                </div>
                                <small class="text-muted">{{ Str::limit($product->short_description, 50) }}</small>
                            </td>
                            <td>
                                <code>{{ $product->sku }}</code>
                            </td>
                            <td>
                                @forelse($product->categories as $category)
                                    <span class="badge badge-info">{{ $category->name }}</span>
                                @empty
                                    <span class="text-muted">No categories</span>
                                @endforelse
                            </td>
                            <td>
                                <div>
                                    <strong>${{ number_format($product->price, 2) }}</strong>
                                    @if($product->special_price)
                                        <br><small class="text-success">${{ number_format($product->special_price, 2) }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($product->track_quantity)
                                    <span class="badge badge-{{ $product->quantity > 0 ? 'success' : 'danger' }}">
                                        {{ $product->quantity }}
                                    </span>
                                @else
                                    <span class="badge badge-secondary">Unlimited</span>
                                @endif
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.products.toggle-status', $product) }}" 
                                      style="display: inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-{{ $product->status ? 'success' : 'secondary' }}">
                                        {{ $product->status ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.products.show', $product) }}" 
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product) }}" 
                                       class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" 
                                          style="display: inline;" 
                                          onsubmit="return confirm('Are you sure you want to delete this product?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-box-open fa-3x mb-3"></i>
                                <br>No products found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($products->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.badge-star {
    background-color: #f39c12;
    color: white;
}
</style>
@endsection