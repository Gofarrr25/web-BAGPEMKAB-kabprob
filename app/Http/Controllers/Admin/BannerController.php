<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::latest()->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'cropped_image' => 'nullable|string',
            'link_url' => 'nullable|string|max:255',
        ]);

        $imagePath = null;
        
        if ($request->filled('cropped_image')) {
            $image_parts = explode(";base64,", $request->cropped_image);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1] ?? 'jpeg';
            $image_base64 = base64_decode($image_parts[1]);
            $safeName = 'banner_' . time() . '_' . uniqid() . '.' . $image_type;
            
            \Illuminate\Support\Facades\Storage::disk('public')->put('banners/' . $safeName, $image_base64);
            $imagePath = 'banners/' . $safeName;
        } elseif ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('banners', 'public');
        } else {
            return back()->withErrors(['image' => 'Gambar Banner wajib diisi.'])->withInput();
        }

        Banner::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'image_path' => $imagePath,
            'link_url' => $request->link_url,
            'is_active' => true,
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner berhasil ditambahkan');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'cropped_image' => 'nullable|string',
            'link_url' => 'nullable|string|max:255',
        ]);

        $data = [
            'title' => $request->title,
            'link_url' => $request->link_url,
        ];

        if ($request->filled('cropped_image')) {
            $image_parts = explode(";base64,", $request->cropped_image);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1] ?? 'jpeg';
            $image_base64 = base64_decode($image_parts[1]);
            $safeName = 'banner_' . time() . '_' . uniqid() . '.' . $image_type;
            
            \Illuminate\Support\Facades\Storage::disk('public')->put('banners/' . $safeName, $image_base64);
            $data['image_path'] = 'banners/' . $safeName;
            
            if ($banner->image_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($banner->image_path);
            }
        } elseif ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('banners', 'public');
            if ($banner->image_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($banner->image_path);
            }
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner berhasil diperbarui');
    }

    public function destroy(Banner $banner)
    {
        
        $banner->delete();
        return redirect()->route('admin.banners.index')->with('success', 'Banner dihapus');
    }

}

