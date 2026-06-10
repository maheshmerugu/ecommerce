<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('position')->orderBy('created_at')->paginate(20);
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'     => 'nullable|string|max:255',
            'caption'   => 'nullable|string|max:500',
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'link'      => 'nullable|max:500',
            'position'  => 'nullable|integer|min:0',
            'is_active' => 'nullable',
        ]);

        Banner::create([
            'title'     => $request->title,
            'caption'   => $request->caption,
            'image'     => $request->file('image')->store('banners', 'public'),
            'link'      => $request->link,
            'position'  => $request->input('position', 0),
            'is_active' => $request->input('is_active', 0) == '1',
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner created successfully!');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'title'     => 'nullable|string|max:255',
            'caption'   => 'nullable|string|max:500',
            'image'     => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'link'      => 'nullable|max:500',
            'position'  => 'nullable|integer|min:0',
            'is_active' => 'nullable',
        ]);

        $data = [
            'title'     => $request->title,
            'caption'   => $request->caption,
            'link'      => $request->link,
            'position'  => $request->input('position', $banner->position),
            'is_active' => $request->input('is_active', 0) == '1',
        ];

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($banner->image);
            $data['image'] = $request->file('image')->store('banners', 'public');
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully!');
    }

    public function destroy(Banner $banner)
    {
        Storage::disk('public')->delete($banner->image);
        $banner->delete();
        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted successfully!');
    }

    public function toggleStatus(Banner $banner)
    {
        $banner->update(['is_active' => !$banner->is_active]);
        $label = $banner->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Banner {$label} successfully!");
    }
}
