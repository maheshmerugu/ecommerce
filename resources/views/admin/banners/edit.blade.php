@extends('admin.layouts.app')

@section('title', 'Edit Banner')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Edit Banner</h1>
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
            <form method="POST" action="{{ route('admin.banners.update', $banner) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-8">

                        <!-- Banner Type -->
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Banner Type <span class="text-danger">*</span></label>
                            <div class="mb-2">
                                <div class="custom-control custom-radio mb-2">
                                    <input type="radio" id="type_hero" name="type" value="hero"
                                           class="custom-control-input"
                                           {{ old('type', $banner->type ?? 'hero') === 'hero' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="type_hero">
                                        <span class="badge badge-primary mr-1">HERO</span>
                                        <strong>Super Sale / Main Carousel</strong>
                                        <small class="text-muted d-block">Full-width top carousel. Upload car images or sale banners here.</small>
                                    </label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="type_promo" name="type" value="promo"
                                           class="custom-control-input"
                                           {{ old('type', $banner->type ?? 'hero') === 'promo' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="type_promo">
                                        <span class="badge badge-warning mr-1">PROMO</span>
                                        <strong>Promotional / Offers Banner</strong>
                                        <small class="text-muted d-block">Displayed below categories as offer/discount banners.</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Title / Headline</label>
                            <input type="text" name="title" class="form-control"
                                   value="{{ old('title', $banner->title) }}"
                                   placeholder="e.g. Super Sale — Up to 40% Off on Cars">
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Caption / Subtitle</label>
                            <textarea name="caption" rows="2" class="form-control"
                                      placeholder="Short promo text">{{ old('caption', $banner->caption) }}</textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Button Link URL</label>
                            <input type="url" name="link" class="form-control"
                                   value="{{ old('link', $banner->link) }}"
                                   placeholder="https://yoursite.com/products">
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Banner Image</label>
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $banner->image) }}"
                                     alt="{{ $banner->title }}"
                                     class="img-fluid rounded" style="max-height:180px;">
                                <p class="text-muted small mt-1">Current image — leave file input blank to keep it.</p>
                            </div>
                            <input type="file" name="image"
                                   class="form-control-file @error('image') is-invalid @enderror"
                                   accept="image/*" onchange="previewBanner(this)">
                            @error('image')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            <small class="text-muted">Hero recommended: 1920×600 px &nbsp;|&nbsp; Promo recommended: 600×300 px. Max 4MB.</small>
                            <div id="bannerPreview" class="mt-2" style="display:none;">
                                <img id="previewImg" src="" alt="Preview" class="img-fluid rounded" style="max-height:220px;">
                            </div>
                        </div>

                    </div>

                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Display Position</label>
                            <input type="number" name="position" class="form-control"
                                   value="{{ old('position', $banner->position) }}" min="0">
                            <small class="text-muted">Lower = appears first in carousel.</small>
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Status</label>
                            <select name="is_active" class="form-control">
                                <option value="1" {{ old('is_active', $banner->is_active ? '1' : '0') == '1' ? 'selected' : '' }}>Active (visible on site)</option>
                                <option value="0" {{ old('is_active', $banner->is_active ? '1' : '0') == '0' ? 'selected' : '' }}>Inactive (hidden)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <hr>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-2"></i>Update Banner
                </button>
                <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary ml-2">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection

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
