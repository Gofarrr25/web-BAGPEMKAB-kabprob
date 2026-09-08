<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\ProcessesBase64;

class GalleryController extends Controller
{
    use ProcessesBase64;

    private function checkOwnership(Gallery $model)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user && $user->hasRole('Staf') && $model->user_id !== $user->id) {
            abort(403, 'Akses Ditolak: Anda tidak diizinkan mengubah/menghapus konten milik orang lain.');
        }
    }

    public function index()
    {
        $photos = Gallery::where('type', 'image')->latest()->paginate(9, ['*'], 'photo_page');
        $videos = Gallery::where('type', 'video')->latest()->paginate(9, ['*'], 'video_page');
        return view('admin.galleries.index', compact('photos', 'videos'));
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:image,video',
            'video_url' => ['nullable', 'url', 'regex:/^(https?\:\/\/)?(www\.youtube\.com|youtu\.be|youtube\.com)\/.+$/'],
            'youtube_urls.*' => ['nullable', 'url', 'regex:/^(https?\:\/\/)?(www\.youtube\.com|youtu\.be|youtube\.com)\/.+$/'],
            'created_at' => 'nullable|date',
        ];

        if ($request->type === 'video') {
            // Local video upload completely removed as per user request
            // Video strictly uses youtube_urls
        } else {
            $rules['file'] = 'nullable|file|mimes:jpg,jpeg,png,webp|max:51200';
            $rules['album_files.*'] = 'nullable|file|mimes:jpg,jpeg,png,webp|max:51200';
        }

        $request->validate($rules, [
            'file.mimes' => '⚠️ Format file tidak sesuai. Field ini hanya menerima JPG, JPEG, PNG, atau WEBP.',
            'album_files.*.mimes' => '⚠️ Format file tidak sesuai. Field ini hanya menerima JPG, JPEG, PNG, atau WEBP.',
            'video_url.regex' => 'Tautan video harus berasal dari YouTube (youtube.com atau youtu.be).',
            'youtube_urls.*.regex' => 'Tautan video harus berasal dari YouTube (youtube.com atau youtu.be).',
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
        } elseif ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('galleries', 'public');
        }

        $data = [
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $this->processBase64Images($request->description),
            'type' => $request->type,
            'file_path' => $filePath,
            'video_url' => null, // Legacy field, all videos go to gallery_items
        ];

        if ($request->filled('created_at')) {
            $data['created_at'] = \Carbon\Carbon::parse($request->created_at);
        }

        $gallery = Gallery::create($data);

        $index = 0;
        if ($request->hasFile('album_files')) {
            foreach ($request->file('album_files') as $albumFile) {
                if ($albumFile->isValid()) {
                    $path = $albumFile->store('gallery_items', 'public');
                    $gallery->galleryItems()->create([
                        'type' => $request->type,
                        'file_path' => $path,
                        'order_index' => $index++,
                    ]);
                }
            }
        }

        if ($request->has('youtube_urls') && $request->type === 'video') {
            foreach ($request->youtube_urls as $yUrl) {
                if ($yUrl) {
                    $gallery->galleryItems()->create([
                        'type' => 'video',
                        'video_url' => $yUrl,
                        'order_index' => $index++,
                    ]);
                }
            }
        }

        return redirect()->route('admin.galleries.index')->with('success', 'Galeri foto/video berhasil ditambahkan.');
    }

    public function edit(Gallery $gallery)
    {
        $this->checkOwnership($gallery);
        return view('admin.galleries.edit', compact('gallery'));
    }

    public function update(Request $request, Gallery $gallery)
    {
        $this->checkOwnership($gallery);
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'created_at' => 'nullable|date',
            'video_url' => ['nullable', 'url', 'regex:/^(https?\:\/\/)?(www\.youtube\.com|youtu\.be|youtube\.com)\/.+$/'],
            'youtube_urls.*' => ['nullable', 'url', 'regex:/^(https?\:\/\/)?(www\.youtube\.com|youtu\.be|youtube\.com)\/.+$/'],
        ];

        if ($gallery->type === 'video') {
            // Local video upload completely removed as per user request
        } else {
            $rules['file'] = 'nullable|file|mimes:jpg,jpeg,png,webp|max:51200';
            $rules['album_files.*'] = 'nullable|file|mimes:jpg,jpeg,png,webp|max:51200';
        }

        $request->validate($rules, [
            'file.mimes' => '⚠️ Format file tidak sesuai. Field ini hanya menerima JPG, JPEG, PNG, atau WEBP.',
            'album_files.*.mimes' => '⚠️ Format file tidak sesuai. Field ini hanya menerima JPG, JPEG, PNG, atau WEBP.',
            'video_url.regex' => 'Tautan video harus berasal dari YouTube (youtube.com atau youtu.be).',
            'youtube_urls.*.regex' => 'Tautan video harus berasal dari YouTube (youtube.com atau youtu.be).',
        ]);

        $updateData = [
            'title' => $request->title,
            'description' => $this->processBase64Images($request->description),
        ];

        if ($gallery->type === 'video') {
            $updateData['video_url'] = null; // Legacy field, all videos go to gallery_items
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
        } elseif ($request->hasFile('file')) {
            if ($gallery->file_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($gallery->file_path);
            }
            $filePath = $request->file('file')->store('galleries', 'public');
        }

        $updateData['file_path'] = $filePath;

        $gallery->update($updateData);

        $maxIndex = $gallery->galleryItems()->max('order_index') ?? -1;
        if ($request->hasFile('album_files')) {
            foreach ($request->file('album_files') as $albumFile) {
                if ($albumFile->isValid()) {
                    $path = $albumFile->store('gallery_items', 'public');
                    $maxIndex++;
                    $gallery->galleryItems()->create([
                        'type' => $gallery->type,
                        'file_path' => $path,
                        'order_index' => $maxIndex,
                    ]);
                }
            }
        }

        if ($request->has('youtube_urls') && $gallery->type === 'video') {
            // Remove existing video items to sync with the new list
            $gallery->galleryItems()->where('type', 'video')->delete();
            $maxIndex = -1; // Reset index for videos since they are completely rebuilt

            foreach ($request->youtube_urls as $yUrl) {
                if ($yUrl) {
                    $maxIndex++;
                    $gallery->galleryItems()->create([
                        'type' => 'video',
                        'video_url' => $yUrl,
                        'order_index' => $maxIndex,
                    ]);
                }
            }
        }

        return redirect()->route('admin.galleries.index')->with('success', 'Data galeri berhasil diperbarui.');
    }

    public function deleteItem(Gallery $gallery, int $itemId)
    {
        $this->checkOwnership($gallery);
        $item = $gallery->galleryItems()->findOrFail($itemId);
        if ($item->file_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($item->file_path);
        }
        $item->delete();
        return back()->with('success', 'Item album berhasil dihapus');
    }

    public function reorderItems(Request $request, Gallery $gallery)
    {
        $this->checkOwnership($gallery);
        $orders = $request->input('orders');
        if (is_array($orders)) {
            foreach ($orders as $order) {
                $gallery->galleryItems()->where('id', $order['id'])->update(['order_index' => $order['order_index']]);
            }
        }
        return response()->json(['success' => true]);
    }

    public function destroy(Gallery $gallery)
    {
        $this->checkOwnership($gallery);
        
        // Hapus file sampul
        if ($gallery->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($gallery->file_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($gallery->file_path);
        }
        
        // Hapus semua file album
        foreach ($gallery->galleryItems as $item) {
            if ($item->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($item->file_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($item->file_path);
            }
        }
        
        $gallery->delete();
        return redirect()->route('admin.galleries.index')->with('success', 'Galeri berhasil dihapus.');
    }
    public function toggleStatus(Gallery $gallery)
    {
        $this->checkOwnership($gallery);
        $gallery->update([
            'is_active' => !$gallery->is_active
        ]);

        return response()->json([
            'success' => true,
            'is_active' => $gallery->is_active,
            'message' => 'Status Galeri berhasil diperbarui.'
        ]);
    }
}

