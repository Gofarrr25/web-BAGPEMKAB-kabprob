<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GalleryController extends Controller
{
    public function index()
    {
        $photos = Gallery::where('type', 'image')->latest()->paginate(9, ['*'], 'photo_page');
        $videos = Gallery::where('type', 'video')->latest()->paginate(9, ['*'], 'video_page');
        return view('admin.galleries.index', compact('photos', 'videos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:image,video',
            'file' => 'nullable|required_if:type,image|image|mimes:jpeg,png,jpg,webp,gif|max:10240',
            'video_url' => 'nullable|required_if:type,video|url',
            'created_at' => 'nullable|date',
        ]);

        $filePath = null;
        if ($request->filled('cropped_image') && $request->type === 'image') {
            $image_parts = explode(";base64,", $request->cropped_image);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            
            $fileName = 'galleries/' . uniqid() . '.' . $image_type;
            \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $image_base64);
            $filePath = $fileName;
        } elseif ($request->hasFile('file') && $request->type === 'image') {
            $filePath = $request->file('file')->store('galleries', 'public');
        }

        $data = [
            'user_id' => Auth::id(),
            'title' => $request->title,
            'type' => $request->type,
            'file_path' => $filePath,
            'video_url' => $request->type === 'video' ? $request->video_url : null,
        ];

        if ($request->filled('created_at')) {
            $data['created_at'] = \Carbon\Carbon::parse($request->created_at);
        }

        Gallery::create($data);

        return redirect()->route('admin.galleries.index')->with('success', 'Galeri foto/video berhasil ditambahkan.');
    }

    public function edit(Gallery $gallery)
    {
        return view('admin.galleries.edit', compact('gallery'));
    }

    public function update(Request $request, Gallery $gallery)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'created_at' => 'nullable|date',
            'video_url' => 'nullable|required_if:type,video|url',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:10240',
        ]);

        $updateData = [
            'title' => $request->title,
        ];

        if ($gallery->type === 'video') {
            $updateData['video_url'] = $request->video_url;
        }

        if ($request->filled('created_at')) {
            $updateData['created_at'] = \Carbon\Carbon::parse($request->created_at);
        }

        $filePath = $gallery->file_path;
        if ($request->filled('cropped_image') && $gallery->type === 'image') {
            $image_parts = explode(";base64,", $request->cropped_image);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            
            $fileName = 'galleries/' . uniqid() . '.' . $image_type;
            \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $image_base64);
            $filePath = $fileName;

            if ($gallery->file_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($gallery->file_path);
            }
        } elseif ($request->hasFile('file') && $gallery->type === 'image') {
            if ($gallery->file_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($gallery->file_path);
            }
            $filePath = $request->file('file')->store('galleries', 'public');
        }

        $updateData['file_path'] = $filePath;

        $gallery->update($updateData);

        return redirect()->route('admin.galleries.index')->with('success', 'Data galeri berhasil diperbarui.');
    }

    public function destroy(Gallery $gallery)
    {
        if ($gallery->file_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($gallery->file_path);
        }
        $gallery->delete();
        return redirect()->route('admin.galleries.index')->with('success', 'Galeri berhasil dihapus.');
    }
}


