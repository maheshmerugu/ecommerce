@extends('admin.layouts.app')

@section('title', 'Product Details - ' . $product->name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Product Details</h1>
        <div>
            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning mr-2">
                <i class="fas fa-edit mr-2"></i>Edit Product
            </a>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Products
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Basic Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="200">Product Name:</th>
                            <td>{{ $product->name }}</td>
                        </tr>
                        <tr>
                            <th>SKU:</th>
                            <td>
                                <code>{{ $product->sku }}</code>
                            </td>
                        </tr>
                        <tr>
                            <th>Short Description:</th>
                            <td>{{ $product->short_description ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td>
                                @if($product->description)
                                    <div style="max-height: 200px; overflow-y: auto;">
                                        {!! nl2br(e($product->description)) !!}
                                    </div>
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Pricing & Inventory -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Pricing & Inventory</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Price:</th>
                                    <td class="h5 text-primary">${{ number_format($product->price, 2) }}</td>
                                </tr>
                                @if($product->special_price)
                                <tr>
                                    <th>Special Price:</th>
                                    <td class="h5 text-danger">${{ number_format($product->special_price, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Special Price Period:</th>
                                    <td>
                                        @if($product->special_price_from && $product->special_price_to)
                                            {{ $product->special_price_from->format('M d, Y') }} - 
                                            {{ $product->special_price_to->format('M d, Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Weight:</th>
                                    <td>{{ $product->weight ? $product->weight . ' kg' : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Quantity:</th>
                                    <td>
                                        <span class="badge badge-{{ $product->quantity > 0 ? 'success' : 'danger' }} badge-lg">
                                            {{ $product->quantity }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Min Quantity:</th>
                                    <td>{{ $product->min_quantity ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Track Quantity:</th>
                                    <td>
                                        <span class="badge badge-{{ $product->track_quantity ? 'success' : 'secondary' }}">
                                            {{ $product->track_quantity ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Stock Status:</th>
                                    <td>
                                        @if($product->quantity > 0)
                                            <span class="badge badge-success">In Stock</span>
                                        @elseif($product->quantity === 0)
                                            <span class="badge badge-warning">Out of Stock</span>
                                        @else
                                            <span class="badge badge-danger">Discontinued</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEO Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">SEO Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="150">Meta Title:</th>
                            <td>{{ $product->meta_title ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Meta Description:</th>
                            <td>{{ $product->meta_description ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Meta Keywords:</th>
                            <td>{{ $product->meta_keywords ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Categories -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Categories</h5>
                </div>
                <div class="card-body">
                    @if($product->categories->count() > 0)
                        @foreach($product->categories as $category)
                            <span class="badge badge-info badge-lg mr-2">{{ $category->name }}</span>
                        @endforeach
                    @else
                        <p class="text-muted">No categories assigned</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Status & Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Status & Settings</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <strong>Status:</strong>
                        </div>
                        <div class="col-6">
                            <form action="{{ route('admin.products.toggle-status', $product) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-{{ $product->status ? 'success' : 'danger' }}">
                                    {{ $product->status ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <strong>Featured:</strong>
                        </div>
                        <div class="col-6">
                            <form action="{{ route('admin.products.toggle-featured', $product) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-{{ $product->featured ? 'warning' : 'secondary' }}">
                                    {{ $product->featured ? 'Yes' : 'No' }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <strong>Created:</strong>
                        </div>
                        <div class="col-6">
                            <small>{{ $product->created_at->format('M d, Y') }}</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <strong>Updated:</strong>
                        </div>
                        <div class="col-6">
                            <small>{{ $product->updated_at->format('M d, Y') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Images -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Product Images</h5>
                </div>
                <div class="card-body">
                    @if($product->images->count() > 0)
                        <div class="row">
                            @foreach($product->images as $image)
                                <div class="col-6 mb-3">
                                    <div class="position-relative">
                                        <img src="{{ asset('storage/' . $image->image_path) }}" 
                                             alt="{{ $image->alt_text }}" 
                                             class="img-fluid img-thumbnail" 
                                             style="height: 120px; width: 100%; object-fit: cover;">
                                        @if($image->is_main)
                                            <span class="badge badge-primary position-absolute" 
                                                  style="top: 5px; left: 5px;">Main</span>
                                        @endif
                                        <div class="position-absolute" style="bottom: 5px; right: 5px;">
                                            <small class="badge badge-dark">
                                                {{ $image->sort_order ?? 0 }}
                                            </small>
                                        </div>
                                    </div>
                                    @if($image->alt_text)
                                        <small class="text-muted d-block mt-1">{{ $image->alt_text }}</small>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No images uploaded</p>
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus mr-1"></i>Add Images
                        </a>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning btn-block mb-2">
                        <i class="fas fa-edit mr-2"></i>Edit Product
                    </a>
                    
                    <a href="{{ route('products.show', $product->slug) }}" target="_blank" class="btn btn-info btn-block mb-2">
                        <i class="fas fa-eye mr-2"></i>View on Website
                    </a>
                    
                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="fas fa-trash mr-2"></i>Delete Product
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Auto-refresh status badges after toggle
document.querySelectorAll('form[action*="toggle"]').forEach(form => {
    form.addEventListener('submit', function() {
        setTimeout(() => {
            window.location.reload();
        }, 500);
    });
});
</script>
@endsection