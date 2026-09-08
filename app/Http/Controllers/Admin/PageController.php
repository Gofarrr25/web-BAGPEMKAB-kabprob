<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Traits\ProcessesBase64;

class PageController extends Controller
{
    use ProcessesBase64;

    public function index()
    {
        // 0. Auto-sync: Jika ada menu yang punya url ke /page/slug tapi belum ada page_id, relasikan otomatis.
        // Ini untuk memastikan satu sumber data (single source of truth) dan mencegah duplikasi di Laman Standalone.
        $menusToSync = \App\Models\Menu::whereNull('page_id')
            ->whereNotNull('url')
            ->get();
            
        foreach ($menusToSync as $menu) {
            $url = $menu->url;
            if (\Illuminate\Support\Str::startsWith($url, '/page/') || \Illuminate\Support\Str::startsWith($url, '/halaman/')) {
                $slug = str_replace(['/page/', '/halaman/'], '', parse_url($url, PHP_URL_PATH));
                $page = Page::where('slug', $slug)->first();
                if ($page) {
                    $menu->update(['page_id' => $page->id, 'module_type' => 'page']);
                }
            }
        }

        // 1. Ambil hierarki menu dari database (Menu Utama dan Submenunya) beserta relasi page
        $mainMenus = \App\Models\Menu::with(['children' => function($q) {
                $q->orderBy('order_index');
            }, 'children.page', 'page'])
            ->whereNull('parent_id')
            ->orderBy('order_index')
            ->get();

        return view('admin.pages.index', compact('mainMenus'));
    }

    public function create()
    {
        $parentPages = Page::whereNull('parent_id')->orderBy('title')->get();
        $menuGroups = ['PROFIL', 'LAYANAN', 'DOKUMEN', 'INFORMASI', 'HUBUNGI'];
        return view('admin.pages.create', compact('parentPages', 'menuGroups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'menu_group' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:pages,id',
            'content' => 'nullable|string',
            'external_url' => 'nullable|string|max:500',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
            'pdf_file' => 'nullable|file|mimes:pdf|max:51200',
            'order_index' => 'nullable|integer',
            'status' => 'required|in:publish,draft',
        ], [
            'image.mimes' => '⚠️ Format file tidak sesuai. Field ini hanya menerima JPG, JPEG, PNG, atau WEBP.',
            'pdf_file.mimes' => '⚠️ Format file tidak sesuai. Field ini hanya menerima file PDF. Silakan pilih file dengan format .pdf.',
        ]);

        $imagePath = null;
        if ($request->filled('cropped_image')) {
            $image_parts = explode(";base64,", $request->cropped_image);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            
            $fileName = 'pages/' . uniqid() . '.' . $image_type;
            \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $image_base64);
            $imagePath = $fileName;
        } elseif ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('pages', 'public');
        }

        $pdfPath = null;
        if ($request->hasFile('pdf_file')) {
            $pdfPath = $request->file('pdf_file')->store('pages/files', 'public');
        }

        $baseSlug = Str::slug($request->title);
        $slug = $baseSlug ?: 'page';
        $count = 1;
        while (Page::where('slug', $slug)->exists()) {
            $slug = ($baseSlug ?: 'page') . '-' . $count;
            $count++;
        }

        $cleanContent = $this->processBase64Images($request->content);

        $data = [
            'title' => $request->title,
            'slug' => $slug,
            'content' => $cleanContent,
            'external_url' => $request->external_url,
            'image' => $imagePath,
            'pdf_file' => $pdfPath,
            'status' => $request->status ?? 'publish',
            'is_active' => true,
        ];

        Page::create($data);

        return redirect()->route('admin.pages.index')->with('success', 'Halaman & Menu berhasil dibuat.');
    }

    public function edit(Page $page)
    {
        $parentPages = Page::whereNull('parent_id')->where('id', '!=', $page->id)->orderBy('title')->get();
        $menuGroups = ['PROFIL', 'LAYANAN', 'DOKUMEN', 'INFORMASI', 'HUBUNGI'];
        return view('admin.pages.edit', compact('page', 'parentPages', 'menuGroups'));
    }

    public function update(Request $request, Page $page)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'menu_group' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:pages,id',
            'content' => 'nullable|string',
            'external_url' => 'nullable|string|max:500',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
            'pdf_file' => 'nullable|file|mimes:pdf|max:51200',
            'order_index' => 'nullable|integer',
            'status' => 'nullable|in:publish,draft',
        ], [
            'image.mimes' => '⚠️ Format file tidak sesuai. Field ini hanya menerima JPG, JPEG, PNG, atau WEBP.',
            'pdf_file.mimes' => '⚠️ Format file tidak sesuai. Field ini hanya menerima file PDF. Silakan pilih file dengan format .pdf.',
        ]);

        $baseSlug = Str::slug($request->title);
        $slug = $baseSlug ?: 'page';
        $count = 1;
        while (Page::where('slug', $slug)->where('id', '!=', $page->id)->exists()) {
            $slug = ($baseSlug ?: 'page') . '-' . $count;
            $count++;
        }

        $cleanContent = $this->processBase64Images($request->content);

        $data = [
            'title' => $request->title,
            'slug' => $slug,
            'content' => $cleanContent,
            'external_url' => $request->external_url,
            'status' => $request->status ?? 'publish',
        ];

        if ($request->filled('cropped_image')) {
            $image_parts = explode(";base64,", $request->cropped_image);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            
            $fileName = 'pages/' . uniqid() . '.' . $image_type;
            \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $image_base64);
            $data['image'] = $fileName;

            if ($page->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($page->image);
            }
        } elseif ($request->hasFile('image')) {
            if ($page->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($page->image);
            }
            $data['image'] = $request->file('image')->store('pages', 'public');
        }

        if ($request->hasFile('pdf_file')) {
            $data['pdf_file'] = $request->file('pdf_file')->store('pages/files', 'public');
        }

        $page->update($data);

        return redirect()->route('admin.pages.index')->with('success', 'Konten Halaman berhasil diperbarui.');
    }

    public function destroy(Page $page)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($page->is_in_menu && (!$user || !$user->hasRole('Superadmin'))) {
            return redirect()->route('admin.pages.index')->with('error', 'Anda tidak dapat menghapus halaman yang terhubung dengan menu utama.');
        }

        if ($page->image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($page->image_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($page->image_path);
        }

        if ($page->pdf_file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($page->pdf_file_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($page->pdf_file_path);
        }

        $page->delete();
        return redirect()->route('admin.pages.index')->with('success', 'Halaman statis berhasil dihapus.');
    }
}
