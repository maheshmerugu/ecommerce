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

                        <!-- Banner Type -->
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Banner Type <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3">
                                <div class="custom-control custom-radio mr-4">
                                    <input type="radio" id="type_hero" name="type" value="hero"
                                           class="custom-control-input" checked>
                                    <label class="custom-control-label" for="type_hero">
                                        <span class="badge badge-primary mr-1">HERO</span>
                                        <strong>Super Sale / Main Carousel</strong>
                                        <br><small class="text-muted">Full-width slider at the top of homepage. Upload car images or sale banners here.</small>
                                    </label>
                                </div>
                            </div>
                            <div class="mt-2">
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="type_promo" name="type" value="promo"
                                           class="custom-control-input">
                                    <label class="custom-control-label" for="type_promo">
                                        <span class="badge badge-warning mr-1">PROMO</span>
                                        <strong>Promotional / Offers Banner</strong>
                                        <br><small class="text-muted">Displayed below categories as offer/discount banners (e.g. "50% off tyres").</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Title / Headline</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}"
                                   placeholder="e.g. Super Sale — Up to 40% Off on Cars">
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Caption / Subtitle</label>
                            <textarea name="caption" rows="2" class="form-control"
                                      placeholder="e.g. Shop the best deals on SUVs, Sedans & Accessories">{{ old('caption') }}</textarea>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Button Link URL</label>
                            <input type="url" name="link" class="form-control" value="{{ old('link') }}"
                                   placeholder="https://yoursite.com/products">
                            <small class="text-muted">Where the "Shop Now" button goes. Leave blank to hide the button.</small>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Banner Image <span class="text-danger">*</span></label>
                            <input type="file" name="image" class="form-control-file @error('image') is-invalid @enderror"
                                   accept="image/*" required onchange="previewBanner(this)">
                            @error('image')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            <div id="size-hint-hero" class="mt-1">
                                <small class="text-muted"><i class="fas fa-info-circle mr-1"></i>
                                    <strong>Hero banner:</strong> Recommended 1920×600 px (wide landscape). Car photos look great here. Max 4MB.
                                </small>
                            </div>
                            <div id="size-hint-promo" class="mt-1" style="display:none;">
                                <small class="text-muted"><i class="fas fa-info-circle mr-1"></i>
                                    <strong>Promo banner:</strong> Recommended 600×300 px (square or landscape). Max 4MB.
                                </small>
                            </div>
                            <div id="bannerPreview" class="mt-2" style="display:none;">
                                <img id="previewImg" src="" alt="Preview" class="img-fluid rounded" style="max-height:220px;">
                            </div>
                        </div>

                    </div>

                    <div class="col-md-4">
                        <div class="card border-left-primary mb-3">
                            <div class="card-body py-3">
                                <h6 class="font-weight-bold text-primary mb-2">
                                    <i class="fas fa-lightbulb mr-1"></i>Tips
                                </h6>
                                <ul class="small mb-0 pl-3">
                                    <li>Use <strong>high-quality car photos</strong> for Hero banners</li>
                                    <li>Add a compelling <strong>title + caption</strong> to drive clicks</li>
                                    <li>Set a <strong>link</strong> to send customers to the right page</li>
                                    <li>Use <strong>Position</strong> to control slide order (0 = first)</li>
                                    <li>You can have <strong>multiple hero banners</strong> — they auto-rotate</li>
                                </ul>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Display Position</label>
                            <input type="number" name="position" class="form-control"
                                   value="{{ old('position', 0) }}" min="0">
                            <small class="text-muted">Lower = appears first in carousel.</small>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Status</label>
                            <select name="is_active" class="form-control">
                                <option value="1" selected>Active (visible on site)</option>
                                <option value="0">Inactive (hidden)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save mr-2"></i>Create Banner
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

document.querySelectorAll('input[name="type"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        document.getElementById('size-hint-hero').style.display  = this.value === 'hero'  ? '' : 'none';
        document.getElementById('size-hint-promo').style.display = this.value === 'promo' ? '' : 'none';
    });
});
</script>
@endsection
