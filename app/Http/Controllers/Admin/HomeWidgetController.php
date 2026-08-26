<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeWidget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeWidgetController extends Controller
{
    public function index()
    {
        $widgets = HomeWidget::orderBy('order_index')->get();
        return view('admin.home_widgets.index', compact('widgets'));
    }

    public function create()
    {
        return view('admin.home_widgets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
            'link_url' => 'nullable|string|max:255',
            'order_index' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ], [
            'image.mimes' => 'Format file tidak valid. Gunakan JPG, PNG, GIF, atau WebP.',
        ]);

        $data = $request->except('image');
        $data['is_active'] = $request->has('is_active');
        $data['order_index'] = $request->order_index ?? 0;

        if ($request->filled('cropped_image')) {
            $image_parts = explode(";base64,", $request->cropped_image);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            
            $fileName = 'widgets/' . uniqid() . '.' . $image_type;
            \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $image_base64);
            $data['image_path'] = $fileName;
        } elseif ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('widgets', 'public');
        }

        HomeWidget::create($data);

        return redirect()->route('admin.settings.index')->with('success', 'Widget berhasil ditambahkan!');
    }

    public function edit(HomeWidget $home_widget)
    {
        return view('admin.home_widgets.edit', compact('home_widget'));
    }

    public function update(Request $request, HomeWidget $home_widget)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
            'link_url' => 'nullable|string|max:255',
            'order_index' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ], [
            'image.mimes' => 'Format file tidak valid. Gunakan JPG, PNG, GIF, atau WebP.',
        ]);

        $data = $request->except('image');
        $data['is_active'] = $request->has('is_active');
        $data['order_index'] = $request->order_index ?? 0;

        if ($request->filled('cropped_image')) {
            $image_parts = explode(";base64,", $request->cropped_image);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            
            $fileName = 'widgets/' . uniqid() . '.' . $image_type;
            \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $image_base64);
            $data['image_path'] = $fileName;

            if ($home_widget->image_path) {
                Storage::disk('public')->delete($home_widget->image_path);
            }
        } elseif ($request->hasFile('image')) {
            if ($home_widget->image_path) {
                Storage::disk('public')->delete($home_widget->image_path);
            }
            $data['image_path'] = $request->file('image')->store('widgets', 'public');
        }

        $home_widget->update($data);

        return redirect()->route('admin.settings.index')->with('success', 'Widget berhasil diperbarui!');
    }

    public function destroy(HomeWidget $home_widget)
    {
        if ($home_widget->image_path) {
            Storage::disk('public')->delete($home_widget->image_path);
        }
        
        $home_widget->delete();

        return redirect()->route('admin.settings.index')->with('success', 'Widget berhasil dihapus!');
    }
}
