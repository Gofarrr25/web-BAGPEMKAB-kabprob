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
        // Auto-deactivate expired banners
        $expiredBanners = Banner::where('is_active', true)->whereNotNull('end_date')->where('end_date', '<', now())->get();
        foreach ($expiredBanners as $banner) {
            $banner->update(['is_active' => false]);
        }

        $banners = Banner::orderBy('order_index', 'asc')->latest()->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
            'cropped_image' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'order_index' => 'required|integer|min:0',
        ], [
            'image.mimes' => '⚠️ Format file tidak sesuai. Field ini hanya menerima JPG, JPEG, PNG, atau WEBP.',
            'image.max' => '⚠️ Ukuran gambar banner maksimal 10 MB.',
            'end_date.after_or_equal' => '⚠️ Tanggal akhir tayang harus setelah atau sama dengan tanggal mulai.',
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
            'is_published' => $request->has('is_published'),
            'is_active' => true, // default active
            'start_date' => $request->start_date ? \Carbon\Carbon::parse($request->start_date) : null,
            'end_date' => $request->end_date ? \Carbon\Carbon::parse($request->end_date) : null,
            'order_index' => $request->order_index ?? 0,
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
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
            'cropped_image' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'order_index' => 'required|integer|min:0',
        ], [
            'image.mimes' => '⚠️ Format file tidak sesuai. Field ini hanya menerima JPG, JPEG, PNG, atau WEBP.',
            'image.max' => '⚠️ Ukuran gambar banner maksimal 10 MB.',
            'end_date.after_or_equal' => '⚠️ Tanggal akhir tayang harus setelah atau sama dengan tanggal mulai.',
        ]);

        $data = [
            'title' => $request->title,
            'is_published' => $request->has('is_published'),
            'start_date' => $request->start_date ? \Carbon\Carbon::parse($request->start_date) : null,
            'end_date' => $request->end_date ? \Carbon\Carbon::parse($request->end_date) : null,
            'order_index' => $request->order_index ?? 0,
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
        if ($banner->image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($banner->image_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($banner->image_path);
        }
        
        $banner->delete();
        return redirect()->route('admin.banners.index')->with('success', 'Banner dihapus');
    }

    public function toggleActive(Banner $banner)
    {
        $banner->update(['is_active' => !$banner->is_active]);
        return redirect()->route('admin.banners.index')->with('success', 'Status penayangan banner berhasil diubah.');
    }

}

