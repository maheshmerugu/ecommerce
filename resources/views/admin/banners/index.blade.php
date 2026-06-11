@extends('admin.layouts.app')

@section('title', 'Banners')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Banners / Carousel</h1>
        <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i>Add Banner
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th width="100">Image</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Caption</th>
                            <th>Position</th>
                            <th>Status</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($banners as $banner)
                        <tr>
                            <td>
                                <img src="{{ asset('storage/' . $banner->image) }}"
                                     alt="{{ $banner->title }}"
                                     style="width:90px;height:50px;object-fit:cover;" class="img-thumbnail">
                            </td>
                            <td><strong>{{ $banner->title ?? '—' }}</strong></td>
                            <td>
                                @if(($banner->type ?? 'hero') === 'hero')
                                    <span class="badge badge-primary">HERO</span>
                                    <small class="d-block text-muted">Main Carousel</small>
                                @else
                                    <span class="badge badge-warning">PROMO</span>
                                    <small class="d-block text-muted">Offers Section</small>
                                @endif
                            </td>
                            <td><small>{{ Str::limit($banner->caption, 50) }}</small></td>
                            <td>{{ $banner->position }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.banners.toggle-status', $banner) }}"
                                      style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="btn btn-sm btn-{{ $banner->is_active ? 'success' : 'secondary' }}">
                                        {{ $banner->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.banners.edit', $banner) }}"
                                       class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                    <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}"
                                          style="display:inline;"
                                          onsubmit="return confirm('Delete this banner?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="fas fa-images fa-3x mb-3 d-block"></i>
                                No banners yet.<br>
                                <a href="{{ route('admin.banners.create') }}" class="btn btn-primary mt-3">
                                    <i class="fas fa-plus mr-1"></i>Add Your First Banner
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($banners->hasPages())
                <div class="d-flex justify-content-center mt-4">{{ $banners->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
