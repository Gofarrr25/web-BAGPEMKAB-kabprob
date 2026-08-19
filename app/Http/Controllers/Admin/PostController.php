<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['user', 'category'])->latest()->paginate(10);
        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'created_at' => 'nullable|date',
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

        $cleanContent = $request->content;
        if (class_exists('\HTMLPurifier')) {
            $config = \HTMLPurifier_Config::createDefault();
            $purifier = new \HTMLPurifier($config);
            $cleanContent = $purifier->purify($request->content);
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

        Post::create($postData);

        return redirect()->route('admin.posts.index')->with('success', 'Berita berhasil ditambahkan');
    }

    public function edit(Post $post)
    {
        $categories = Category::all();
        return view('admin.posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {

        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'created_at' => 'nullable|date',
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

        $cleanContent = $request->content;
        if (class_exists('\HTMLPurifier')) {
            $config = \HTMLPurifier_Config::createDefault();
            $purifier = new \HTMLPurifier($config);
            $cleanContent = $purifier->purify($request->content);
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

        return redirect()->route('admin.posts.index')->with('success', 'Berita berhasil diperbarui');
    }

    public function destroy(Post $post)
    {
        
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'Berita berhasil dihapus');
    }
}
