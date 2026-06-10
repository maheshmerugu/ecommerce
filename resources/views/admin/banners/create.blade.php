@extends('admin.layouts.app')

@section('title', 'Add Banner')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Add Banner</h1>
        <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Title</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}"
                                   placeholder="Banner headline (optional)">
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Caption / Subtitle</label>
                            <textarea name="caption" rows="3" class="form-control"
                                      placeholder="Short description or call-to-action text">{{ old('caption') }}</textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Link URL</label>
                            <input type="url" name="link" class="form-control" value="{{ old('link') }}"
                                   placeholder="https://example.com/products">
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Banner Image <span class="text-danger">*</span></label>
                            <input type="file" name="image" class="form-control-file @error('image') is-invalid @enderror"
                                   accept="image/*" required onchange="previewBanner(this)">
                            @error('image')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            <small class="text-muted">Recommended: 1920×600 px. Max 4MB.</small>
                            <div id="bannerPreview" class="mt-2" style="display:none;">
                                <img id="previewImg" src="" alt="Preview" class="img-fluid" style="max-height:200px;">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Display Position</label>
                            <input type="number" name="position" class="form-control" value="{{ old('position', 0) }}" min="0">
                            <small class="text-muted">Lower numbers appear first.</small>
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Status</label>
                            <select name="is_active" class="form-control">
                                <option value="1" selected>Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <hr>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-2"></i>Create Banner
                </button>
                <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary ml-2">Cancel</a>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
function previewBanner(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('bannerPreview').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
@endsection
