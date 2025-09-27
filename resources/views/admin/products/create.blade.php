@extends('admin.layouts.app')

@section('title', 'Add New Product')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add New Product</h1>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Back to Products
        </a>
    </div>

    <!-- General Error Alert -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="name">Product Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sku">SKU <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                           id="sku" name="sku" value="{{ old('sku') }}" required>
                                    @error('sku')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="short_description">Short Description</label>
                            <textarea class="form-control @error('short_description') is-invalid @enderror" 
                                      id="short_description" name="short_description" rows="3">{{ old('short_description') }}</textarea>
                            @error('short_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Full Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="8">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
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
                                <div class="form-group">
                                    <label for="price">Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                                               id="price" name="price" value="{{ old('price') }}" required>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="special_price">Special Price</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" step="0.01" class="form-control @error('special_price') is-invalid @enderror" 
                                               id="special_price" name="special_price" value="{{ old('special_price') }}">
                                        @error('special_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="special_price_from">Special Price From</label>
                                    <input type="date" class="form-control @error('special_price_from') is-invalid @enderror" 
                                           id="special_price_from" name="special_price_from" value="{{ old('special_price_from') }}">
                                    @error('special_price_from')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="special_price_to">Special Price To</label>
                                    <input type="date" class="form-control @error('special_price_to') is-invalid @enderror" 
                                           id="special_price_to" name="special_price_to" value="{{ old('special_price_to') }}">
                                    @error('special_price_to')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="quantity">Quantity <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                           id="quantity" name="quantity" value="{{ old('quantity', 0) }}" min="0" required>
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="min_quantity">Minimum Quantity</label>
                                    <input type="number" class="form-control @error('min_quantity') is-invalid @enderror" 
                                           id="min_quantity" name="min_quantity" value="{{ old('min_quantity', 1) }}" min="1">
                                    @error('min_quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="weight">Weight (kg)</label>
                            <input type="number" step="0.01" class="form-control @error('weight') is-invalid @enderror" 
                                   id="weight" name="weight" value="{{ old('weight') }}" min="0">
                            @error('weight')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check">
                            <input type="hidden" name="track_quantity" value="0">
                            <input type="checkbox" class="form-check-input" id="track_quantity" name="track_quantity" 
                                   value="1" {{ old('track_quantity', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="track_quantity">
                                Track Quantity
                            </label>
                        </div>
                    </div>
                </div>

                <!-- SEO -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">SEO Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="meta_title">Meta Title</label>
                            <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                                   id="meta_title" name="meta_title" value="{{ old('meta_title') }}">
                            @error('meta_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="meta_description">Meta Description</label>
                            <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                      id="meta_description" name="meta_description" rows="3">{{ old('meta_description') }}</textarea>
                            @error('meta_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="meta_keywords">Meta Keywords</label>
                            <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror" 
                                   id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords') }}">
                            @error('meta_keywords')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
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
                        <div class="form-check mb-3">
                            <input type="hidden" name="status" value="0">
                            <input type="checkbox" class="form-check-input" id="status" name="status" 
                                   value="1" {{ old('status', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="status">
                                <strong>Active</strong>
                                <br><small class="text-muted">Product will be visible on the website</small>
                            </label>
                        </div>

                        <div class="form-check">
                            <input type="hidden" name="featured" value="0">
                            <input type="checkbox" class="form-check-input" id="featured" name="featured" 
                                   value="1" {{ old('featured') ? 'checked' : '' }}>
                            <label class="form-check-label" for="featured">
                                <strong>Featured Product</strong>
                                <br><small class="text-muted">Show in featured products section</small>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Categories -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Categories</h5>
                    </div>
                    <div class="card-body">
                        @forelse($categories as $category)
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" 
                                       id="category_{{ $category->id }}" 
                                       name="categories[]" 
                                       value="{{ $category->id }}"
                                       {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="category_{{ $category->id }}">
                                    {{ $category->name }}
                                </label>
                            </div>
                        @empty
                            <p class="text-muted">No categories available. <a href="{{ route('admin.categories.create') }}">Create one</a></p>
                        @endforelse
                        @error('categories')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Images -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Product Images</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="main_image">Main Image</label>
                            <input type="file" class="form-control-file @error('main_image') is-invalid @enderror" 
                                   id="main_image" name="main_image" accept="image/*">
                            @error('main_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Upload main product image (Max: 2MB)</small>
                        </div>

                        <div class="form-group">
                            <label for="gallery_images">Gallery Images</label>
                            <input type="file" class="form-control-file @error('gallery_images') is-invalid @enderror" 
                                   id="gallery_images" name="gallery_images[]" accept="image/*" multiple>
                            @error('gallery_images')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Upload additional product images (Max: 2MB each)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-secondary mr-2" onclick="history.back()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i>Create Product
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.getElementById('name').addEventListener('input', function() {
    const name = this.value;
    const sku = name.toLowerCase().replace(/[^a-z0-9]/g, '-').replace(/-+/g, '-').trim('-').substring(0, 20);
    document.getElementById('sku').value = 'SKU-' + sku.toUpperCase();
});

// Auto-fill meta title from product name
document.getElementById('name').addEventListener('input', function() {
    if (!document.getElementById('meta_title').value) {
        document.getElementById('meta_title').value = this.value;
    }
});

// Auto-fill meta description from short description
document.getElementById('short_description').addEventListener('input', function() {
    if (!document.getElementById('meta_description').value) {
        document.getElementById('meta_description').value = this.value;
    }
});
</script>
@endsection