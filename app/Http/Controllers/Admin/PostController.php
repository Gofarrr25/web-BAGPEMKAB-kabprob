<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Traits\ProcessesBase64;

class PostController extends Controller
{
    use ProcessesBase64;

    private function checkOwnership(Post $model)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user && $user->hasRole('Staf') && $model->user_id !== $user->id) {
            abort(403, 'Akses Ditolak: Anda tidak diizinkan mengubah/menghapus konten milik orang lain.');
        }
    }

    public function index()
    {
        $posts = Post::with(['user', 'category'])->latest()->paginate(10);
        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'content' => 'required|string',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:1024',
            'album_images.*' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:1024',
            'created_at' => 'nullable|date',
        ], [
            'image.mimes' => '⚠️ Format file tidak sesuai. Field ini hanya menerima JPG, JPEG, PNG, atau WEBP.',
            'image.max' => '⚠️ Ukuran foto utama maksimal 1 MB.',
            'album_images.*.mimes' => '⚠️ Format file tidak sesuai. Field ini hanya menerima JPG, JPEG, PNG, atau WEBP.',
            'album_images.*.max' => '⚠️ Ukuran foto album maksimal 1 MB.',
        ]);

        $imagePath = null;
        if ($request->filled('cropped_image')) {
            $image_parts = explode(";base64,", $request->cropped_image);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            
            $fileName = 'posts/' . uniqid() . '.' . $image_type;
            \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $image_base64);
            $imagePath = $fileName;
        } elseif ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        $cleanContent = $this->processBase64Images($request->content);
        if (class_exists('\HTMLPurifier')) {
            $config = \HTMLPurifier_Config::createDefault();
            $purifier = new \HTMLPurifier($config);
            $cleanContent = $purifier->purify($cleanContent);
        }

        $postData = [
            'user_id' => Auth::id(),
            'category_id' => $request->category_id,
            'title' => $request->title,
            'slug' => Str::slug($request->title . '-' . time()),
            'content' => $cleanContent,
            'image' => $imagePath,
            'is_published' => $request->has('is_published'),
        ];

        if ($request->filled('created_at')) {
            $postData['created_at'] = \Carbon\Carbon::parse($request->created_at);
        }

        $post = Post::create($postData);

        if ($request->hasFile('album_images')) {
            $index = 0;
            foreach ($request->file('album_images') as $albumFile) {
                if ($albumFile->isValid()) {
                    $path = $albumFile->store('post_albums', 'public');
                    $post->postImages()->create([
                        'image_path' => $path,
                        'order_index' => $index++,
                    ]);
                }
            }
        }

        return redirect()->route('admin.posts.index')->with('success', 'Berita berhasil ditambahkan');
    }

    public function edit(Post $post)
    {
        $this->checkOwnership($post);
        $categories = Category::where('is_active', true)
            ->orWhere('id', $post->category_id)
            ->get();
        return view('admin.posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        $this->checkOwnership($post);
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'content' => 'required|string',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:1024',
            'album_images.*' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:1024',
            'created_at' => 'nullable|date',
        ], [
            'image.mimes' => '⚠️ Format file tidak sesuai. Field ini hanya menerima JPG, JPEG, PNG, atau WEBP.',
            'image.max' => '⚠️ Ukuran foto utama maksimal 1 MB.',
            'album_images.*.mimes' => '⚠️ Format file tidak sesuai. Field ini hanya menerima JPG, JPEG, PNG, atau WEBP.',
            'album_images.*.max' => '⚠️ Ukuran foto album maksimal 1 MB.',
        ]);

        $imagePath = $post->image;
        if ($request->filled('cropped_image')) {
            $image_parts = explode(";base64,", $request->cropped_image);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            
            $fileName = 'posts/' . uniqid() . '.' . $image_type;
            \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $image_base64);
            $imagePath = $fileName;

            if ($post->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($post->image);
            }
        } elseif ($request->hasFile('image')) {
            if ($post->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($post->image);
            }
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        $cleanContent = $this->processBase64Images($request->content);
        if (class_exists('\HTMLPurifier')) {
            $config = \HTMLPurifier_Config::createDefault();
            $purifier = new \HTMLPurifier($config);
            $cleanContent = $purifier->purify($cleanContent);
        }

        $updateData = [
            'category_id' => $request->category_id,
            'title' => $request->title,
            'slug' => Str::slug($request->title . '-' . time()),
            'content' => $cleanContent,
            'image' => $imagePath,
            'is_published' => $request->has('is_published'),
        ];

        if ($request->filled('created_at')) {
            $updateData['created_at'] = \Carbon\Carbon::parse($request->created_at);
        }

        $post->update($updateData);

        if ($request->hasFile('album_images')) {
            $maxIndex = $post->postImages()->max('order_index') ?? -1;
            foreach ($request->file('album_images') as $albumFile) {
                if ($albumFile->isValid()) {
                    $path = $albumFile->store('post_albums', 'public');
                    $maxIndex++;
                    $post->postImages()->create([
                        'image_path' => $path,
                        'order_index' => $maxIndex,
                    ]);
                }
            }
        }

        return redirect()->route('admin.posts.index')->with('success', 'Berita berhasil diperbarui');
    }

    public function deleteImage(Post $post, int $imageId)
    {
        $this->checkOwnership($post);
        $image = $post->postImages()->findOrFail($imageId);
        \Illuminate\Support\Facades\Storage::disk('public')->delete($image->image_path);
        $image->delete();
        return back()->with('success', 'Foto album berhasil dihapus');
    }

    public function reorderImages(Request $request, Post $post)
    {
        $this->checkOwnership($post);
        $orders = $request->input('orders');
        if (is_array($orders)) {
            foreach ($orders as $order) {
                $post->postImages()->where('id', $order['id'])->update(['order_index' => $order['order_index']]);
            }
        }
        return response()->json(['success' => true]);
    }

    public function destroy(Post $post)
    {
        $this->checkOwnership($post);
        
        // Hapus thumbnail image jika ada
        if ($post->image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($post->image);
        }

        // Hapus album images jika ada
        foreach ($post->postImages as $albumImage) {
            if ($albumImage->image_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($albumImage->image_path);
            }
        }
        
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'Berita berhasil dihapus');
    }

    public function toggleStatus(Post $post)
    {
        $this->checkOwnership($post);
        $post->update([
            'is_active' => !$post->is_active
        ]);

        $status = $post->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.posts.index')->with('success', "Berita berhasil {$status}.");
    }
}
