@extends('admin.layouts.app')

@section('title', 'Categories Management')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Categories Management</h1>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i>Add New Category
        </a>
    </div>

    <!-- Search & Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-6">
                    <input type="text" class="form-control" name="search" placeholder="Search categories..."
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
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th width="60">Image</th>
                            <th>Name</th>
                            <th>Parent</th>
                            <th>Products</th>
                            <th>Sort</th>
                            <th>Status</th>
                            <th width="160">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr>
                            <td>
                                @if($category->image)
                                    <img src="{{ asset('storage/' . $category->image) }}"
                                         alt="{{ $category->name }}"
                                         class="img-thumbnail"
                                         style="width:50px;height:50px;object-fit:cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center"
                                         style="width:50px;height:50px;">
                                        <i class="fas fa-tag text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $category->name }}</strong>
                                @if($category->description)
                                    <br><small class="text-muted">{{ Str::limit($category->description, 60) }}</small>
                                @endif
                            </td>
                            <td>
                                {{ $category->parent?->name ?? '<span class="text-muted">—</span>' }}
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $category->products_count }}</span>
                            </td>
                            <td>{{ $category->sort_order }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.categories.toggle-status', $category) }}"
                                      style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="btn btn-sm btn-{{ $category->status ? 'success' : 'secondary' }}">
                                        {{ $category->status ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.categories.edit', $category) }}"
                                       class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST"
                                          action="{{ route('admin.categories.destroy', $category) }}"
                                          style="display:inline;"
                                          onsubmit="return confirm('Delete this category?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-tags fa-3x mb-3"></i><br>No categories found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($categories->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
