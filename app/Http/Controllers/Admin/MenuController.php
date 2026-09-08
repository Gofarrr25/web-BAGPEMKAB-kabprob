<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    /**
     * Halaman 1: Tampilkan hanya daftar Menu Utama (parent_id IS NULL)
     */
    public function index(Request $request)
    {
        $query = Menu::whereNull('parent_id')->withCount('children')->with('page');

        if ($request->filled('trashed') && $request->trashed == '1') {
            $query->onlyTrashed();
        }



        if ($request->filled('status')) {
            $query->where('is_active', $request->status == 'active');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }

        $menus = $query->orderBy('order_index')->paginate(15)->withQueryString();
        $pages = Page::where('is_active', true)->orderBy('title')->get();

        return view('admin.menus.index', compact('menus', 'pages'));
    }

    /**
     * Store Menu Utama atau Submenu baru.
     * Jika Halaman dan URL kosong, buat Halaman secara Otomatis!
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:menus,id',
            'page_id' => 'nullable|exists:pages,id',
            'url' => 'nullable|string|max:500',
            'target' => 'nullable|in:_self,_blank',
            'order_index' => 'nullable|integer',
        ]);

        $pageId = $request->page_id;
        $url = $request->url;
        $moduleType = 'page'; // Selalu paksa menjadi 'page' karena tidak ada modul lagi untuk navigasi

        // Coba resolve page_id dari URL secara otomatis jika url mengarah ke halaman
        if (empty($pageId) && !empty($url) && (Str::startsWith($url, '/page/') || Str::startsWith($url, '/halaman/'))) {
            $slug = str_replace(['/page/', '/halaman/'], '', parse_url($url, PHP_URL_PATH));
            $existingPage = Page::where('slug', $slug)->first();
            if ($existingPage) {
                $pageId = $existingPage->id;
            }
        }

        // Otomatis Buat Halaman Statis Jika Halaman & URL kosong
        if ($request->filled('parent_id') && empty($pageId) && empty($url)) {
            $title = trim($request->title);
            $baseSlug = Str::slug($title);
            $slug = $baseSlug;
            $count = 1;

            while (Page::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $count;
                $count++;
            }

            $page = Page::create([
                'title' => $title,
                'slug' => $slug,
                'content' => '<p>Konten halaman <strong>' . e($title) . '</strong> belum diisi. Silakan gunakan tombol <strong>Kelola Konten</strong> untuk melengkapi teks, gambar, dan dokumen.</p>',
                'status' => 'publish',
                'is_active' => true,
                'is_in_menu' => true,
            ]);

            $pageId = $page->id;
            $url = '/page/' . $page->slug;
        }

        Menu::create([
            'title' => trim($request->title),
            'parent_id' => $request->parent_id,
            'page_id' => $pageId,
            'url' => $url,
            'module_type' => $moduleType,
            'target' => $request->target ?? '_self',
            'position' => 'navbar', // Force navbar default
            'order_index' => $request->order_index ?? 0,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        if ($request->filled('parent_id')) {
            return redirect()->route('admin.menus.submenus', $request->parent_id)
                ->with('success', 'Submenu baru berhasil ditambahkan dan halaman otomatis dibuat.');
        }

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu Utama baru berhasil ditambahkan.');
    }

    /**
     * Update Menu Utama / Submenu
     */
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:menus,id',
            'page_id' => 'nullable|exists:pages,id',
            'url' => 'nullable|string|max:500',
            'target' => 'nullable|in:_self,_blank',
            'order_index' => 'nullable|integer',
        ]);

        $parentId = $request->parent_id;
        if ($parentId == $menu->id) {
            $parentId = null;
        }

        $pageId = $request->has('page_id') ? $request->page_id : $menu->page_id;
        $url = $request->has('url') ? $request->url : $menu->url;
        $target = $request->has('target') ? $request->target : $menu->target;
        
        $moduleType = 'page'; // Selalu paksa menjadi 'page'

        // Coba resolve page_id dari URL secara otomatis jika url mengarah ke halaman
        if (empty($pageId) && !empty($url) && (Str::startsWith($url, '/page/') || Str::startsWith($url, '/halaman/'))) {
            $slug = str_replace(['/page/', '/halaman/'], '', parse_url($url, PHP_URL_PATH));
            $existingPage = Page::where('slug', $slug)->first();
            if ($existingPage) {
                $pageId = $existingPage->id;
            }
        }

        // Jika halaman & url tetap kosong dan tidak ada relasi page sebelumnya (hanya untuk submenu)
        if ($parentId && empty($pageId) && empty($url) && !$menu->page_id) {
            $title = trim($request->title);
            $baseSlug = Str::slug($title);
            $slug = $baseSlug;
            $count = 1;

            while (Page::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $count;
                $count++;
            }

            $page = Page::create([
                'title' => $title,
                'slug' => $slug,
                'content' => '<p>Konten halaman <strong>' . e($title) . '</strong> belum diisi.</p>',
                'status' => 'publish',
                'is_active' => true,
                'is_in_menu' => true,
            ]);

            $pageId = $page->id;
            $url = '/page/' . $page->slug;
        }

        $oldTitle = $menu->title;
        $newTitle = trim($request->title);

        $menu->update([
            'title' => $newTitle,
            'parent_id' => $parentId,
            'page_id' => $pageId,
            'url' => $url,
            'module_type' => $moduleType,
            'target' => $target ?? '_self',
            'position' => 'navbar', // Force navbar default
            'order_index' => $request->order_index ?? $menu->order_index,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        if ($oldTitle !== $newTitle) {
            \App\Models\Document::where('category', $oldTitle)->update(['category' => $newTitle]);
        }

        if ($menu->parent_id) {
            return redirect()->route('admin.menus.submenus', $menu->parent_id)
                ->with('success', 'Submenu berhasil diperbarui.');
        }

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu Utama berhasil diperbarui.');
    }

    /**
     * Halaman 2: Khusus Pengelolaan Submenu dari Menu Utama tertentu
     */
    public function submenus(Request $request, Menu $menu)
    {
        $submenus = Menu::where('parent_id', $menu->id)
            ->with(['children.page', 'page'])
            ->orderBy('order_index')
            ->get();

        $pages = Page::where('is_active', true)->orderBy('title')->get();
        $parentMenus = Menu::whereNull('parent_id')->orderBy('order_index')->get();

        return view('admin.menus.submenus', compact('menu', 'submenus', 'pages', 'parentMenus'));
    }

    /**
     * Toggle Aktif / Nonaktif Menu
     */
    public function toggle(Menu $menu)
    {
        $menu->update([
            'is_active' => !$menu->is_active
        ]);

        return back()->with('success', 'Status aktif menu "' . $menu->title . '" berhasil diubah.');
    }

    /**
     * Buat konten otomatis untuk menu yang belum memiliki halaman (dipanggil dari Daftar Halaman)
     */
    public function createContent(Menu $menu)
    {
        if ($menu->page_id) {
            return redirect()->route('admin.pages.edit', $menu->page_id);
        }

        $title = trim($menu->title);
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $count = 1;

        while (Page::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $count;
            $count++;
        }

        $page = Page::create([
            'title' => $title,
            'slug' => $slug,
            'content' => '<p>Konten halaman <strong>' . e($title) . '</strong> belum diisi. Silakan gunakan Editor untuk melengkapi teks, gambar, dan dokumen.</p>',
            'status' => 'publish',
            'is_active' => true,
            'is_in_menu' => true,
        ]);

        $menu->update([
            'page_id' => $page->id,
            'url' => '/page/' . $page->slug,
            'module_type' => 'page',
        ]);

        return redirect()->route('admin.pages.edit', $page->id)->with('success', 'Halaman berhasil dibuat secara otomatis, silakan lengkapi konten.');
    }

    /**
     * Reorder Urutan Menu
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:menus,id',
            'orders.*.order_index' => 'required|integer',
        ]);

        foreach ($request->orders as $item) {
            Menu::where('id', $item['id'])->update(['order_index' => $item['order_index']]);
        }

        return response()->json(['success' => true, 'message' => 'Urutan menu berhasil disimpan.']);
    }

    /**
     * Hapus (Soft Delete)
     */
    public function destroy(Menu $menu)
    {
        $title = $menu->title;
        $parentId = $menu->parent_id;

        $menu->delete();

        if ($parentId) {
            return redirect()->route('admin.menus.submenus', $parentId)
                ->with('success', 'Submenu "' . $title . '" berhasil dihapus.');
        }

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu Utama "' . $title . '" beserta submenunya berhasil dihapus.');
    }

    /**
     * Restore Soft Deleted Menu
     */
    public function restore(int $id)
    {
        $menu = Menu::onlyTrashed()->findOrFail($id);
        $menu->restore();

        return back()->with('success', 'Menu "' . $menu->title . '" berhasil dipulihkan dari tempat sampah.');
    }
}
