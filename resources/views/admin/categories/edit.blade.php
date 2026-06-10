@extends('admin.layouts.app')

@section('title', 'Edit Category')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Category: {{ $category->name }}</h1>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Back to Categories
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Left column -->
                    <div class="col-md-8">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Category Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $category->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Description</label>
                            <textarea name="description" rows="4"
                                      class="form-control @error('description') is-invalid @enderror">{{ old('description', $category->description) }}</textarea>
                        </div>

                        <hr>
                        <h6 class="font-weight-bold text-muted mb-3">SEO</h6>

                        <div class="form-group mb-3">
                            <label>Meta Title</label>
                            <input type="text" name="meta_title" class="form-control"
                                   value="{{ old('meta_title', $category->meta_title) }}" maxlength="255">
                        </div>
                        <div class="form-group mb-3">
                            <label>Meta Description</label>
                            <textarea name="meta_description" rows="2" class="form-control"
                                      maxlength="500">{{ old('meta_description', $category->meta_description) }}</textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label>Meta Keywords</label>
                            <input type="text" name="meta_keywords" class="form-control"
                                   value="{{ old('meta_keywords', $category->meta_keywords) }}" maxlength="500">
                        </div>
                    </div>

                    <!-- Right column -->
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Parent Category</label>
                            <select name="parent_id" class="form-control">
                                <option value="">— None (top-level) —</option>
                                @foreach($parents as $parent)
                                    <option value="{{ $parent->id }}"
                                        {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control"
                                   value="{{ old('sort_order', $category->sort_order) }}" min="0">
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Status</label>
                            <select name="status" class="form-control">
                                <option value="1" {{ old('status', $category->status ? '1' : '0') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status', $category->status ? '1' : '0') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Category Image</label>
                            @if($category->image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $category->image) }}"
                                         alt="{{ $category->name }}"
                                         class="img-thumbnail" style="max-width:150px;">
                                    <p class="text-muted small mt-1">Current image</p>
                                </div>
                            @endif
                            <input type="file" name="image" class="form-control-file @error('image') is-invalid @enderror"
                                   accept="image/*" onchange="previewImage(this)">
                            @error('image')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            <div id="imagePreview" class="mt-2" style="display:none;">
                                <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-width:150px;">
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-2"></i>Update Category
                </button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary ml-2">Cancel</a>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
@endsection
