<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::withCount('products')->with('parent');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status == '1');
        }

        $categories = $query->orderBy('sort_order')->orderBy('name')->paginate(20)->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = Category::whereNull('parent_id')->where('status', true)->orderBy('name')->get();
        return view('admin.categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255|unique:categories,name',
            'description'      => 'nullable|string',
            'parent_id'        => 'nullable|exists:categories,id',
            'sort_order'       => 'nullable|integer|min:0',
            'status'           => 'boolean',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:500',
        ]);

        $data = $request->only([
            'name', 'description', 'parent_id', 'sort_order',
            'meta_title', 'meta_description', 'meta_keywords',
        ]);

        $data['status']     = $request->input('status', 0) == '1';
        $data['sort_order'] = $request->input('sort_order', 0);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully!');
    }

    public function show(Category $category)
    {
        $category->load(['parent', 'children', 'products']);
        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        $parents = Category::whereNull('parent_id')
            ->where('status', true)
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();

        return view('admin.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name'             => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description'      => 'nullable|string',
            'parent_id'        => 'nullable|exists:categories,id',
            'sort_order'       => 'nullable|integer|min:0',
            'status'           => 'boolean',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:500',
        ]);

        $data = $request->only([
            'name', 'description', 'parent_id', 'sort_order',
            'meta_title', 'meta_description', 'meta_keywords',
        ]);

        $data['status']     = $request->input('status', 0) == '1';
        $data['sort_order'] = $request->input('sort_order', 0);

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully!');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Cannot delete category — it has associated products.');
        }

        if ($category->children()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Cannot delete category — it has sub-categories. Delete them first.');
        }

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully!');
    }

    public function toggleStatus(Category $category)
    {
        $category->update(['status' => !$category->status]);
        $label = $category->status ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Category {$label} successfully!");
    }
}
