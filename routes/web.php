<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ContactSettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\HomeWidgetController;

// Homepage
Route::get('/', function () {
    $banners = \App\Models\Banner::where('is_active', true)->get();
    $dbIgPosts = \App\Models\InstagramPost::where('is_active', true)->orderBy('order_index', 'asc')->latest()->take(6)->get();
    $latestPosts = \App\Models\Post::with('category')->where('is_published', true)->latest()->take(4)->get();
    $videos = \App\Models\Gallery::where('type', 'video')->orWhereNotNull('video_url')->latest()->take(3)->get();
    $homeWidgets = \App\Models\HomeWidget::where('is_active', true)->orderBy('order_index')->get();
    $relatedLinks = \App\Models\RelatedLink::where('is_active', true)->orderBy('order')->get();
    return view('frontend.home', compact('banners', 'dbIgPosts', 'latestPosts', 'videos', 'homeWidgets', 'relatedLinks'));
});

// === ROUTES BERITA & ARTIKEL PUBLIK ===
Route::get('/informasi', function (\Illuminate\Http\Request $request) {
    $query = \App\Models\Post::with(['category', 'user'])->where('is_published', true);
    
    if ($request->filled('q')) {
        $query->where('title', 'like', '%' . $request->q . '%');
    }
    
    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }
    
    $posts = $query->latest()->paginate(9)->withQueryString();
    $categories = \App\Models\Category::all();
    
    return view('frontend.posts', compact('posts', 'categories'));
});

Route::get('/berita', function (\Illuminate\Http\Request $request) {
    $query = \App\Models\Post::with(['category', 'user'])->where('is_published', true);
    
    if ($request->filled('q')) {
        $query->where('title', 'like', '%' . $request->q . '%');
    }
    
    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }
    
    $posts = $query->latest()->paginate(9)->withQueryString();
    $categories = \App\Models\Category::all();
    
    return view('frontend.posts', compact('posts', 'categories'));
});

Route::get('/posts', function (\Illuminate\Http\Request $request) {
    $query = \App\Models\Post::with(['category', 'user'])->where('is_published', true);
    
    if ($request->filled('q')) {
        $query->where('title', 'like', '%' . $request->q . '%');
    }
    
    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }
    
    $posts = $query->latest()->paginate(9)->withQueryString();
    $categories = \App\Models\Category::all();
    
    return view('frontend.posts', compact('posts', 'categories'));
});

Route::get('/informasi/{slug}', function ($slug) {
    $post = \App\Models\Post::with(['category', 'user'])->where('slug', $slug)->first();
    
    if (!$post) {
        $post = \App\Models\Post::with(['category', 'user'])->where('id', $slug)->orWhere('slug', 'like', $slug . '%')->first();
    }
    
    if (!$post) {
        abort(404);
    }
    
    $latestPosts = \App\Models\Post::with('category')->where('id', '!=', $post->id)->where('is_published', true)->latest()->take(5)->get();
    
    return view('frontend.post-detail', compact('post', 'latestPosts'));
});

Route::get('/berita/{slug}', function ($slug) {
    $post = \App\Models\Post::with(['category', 'user'])->where('slug', $slug)->first();
    
    if (!$post) {
        $post = \App\Models\Post::with(['category', 'user'])->where('id', $slug)->orWhere('slug', 'like', $slug . '%')->first();
    }
    
    if (!$post) {
        abort(404);
    }
    
    $latestPosts = \App\Models\Post::with('category')->where('id', '!=', $post->id)->where('is_published', true)->latest()->take(5)->get();
    
    return view('frontend.post-detail', compact('post', 'latestPosts'));
});

Route::get('/posts/{slug}', function ($slug) {
    $post = \App\Models\Post::with(['category', 'user'])->where('slug', $slug)->first();
    
    if (!$post) {
        $post = \App\Models\Post::with(['category', 'user'])->where('id', $slug)->orWhere('slug', 'like', $slug . '%')->first();
    }
    
    if (!$post) {
        abort(404);
    }
    
    $latestPosts = \App\Models\Post::with('category')->where('id', '!=', $post->id)->where('is_published', true)->latest()->take(5)->get();
    
    return view('frontend.post-detail', compact('post', 'latestPosts'));
});

// === ROUTES FRONTEND SPESIFIK SESUAI DESAIN ===

// 1. Struktur Organisasi
Route::get('/page/struktur-organisasi', function () {
    $page = \App\Models\Page::where('slug', 'struktur-organisasi')->first();
    $members = \App\Models\OrganizationMember::with('children')->whereNull('parent_id')->orderBy('order_index')->get();
    $mode = \App\Models\Setting::where('key', 'org_structure_mode')->value('value') ?? 'dynamic';
    $photo = \App\Models\Setting::where('key', 'org_structure_photo')->value('value');
    return view('frontend.struktur', compact('page', 'members', 'mode', 'photo'));
});

// 2. Kontak Resmi
Route::get('/kontak', function () {
    return view('frontend.kontak');
});
Route::get('/kontak-resmi', function () {
    return view('frontend.kontak');
});
Route::get('/page/kontak', function () {
    return view('frontend.kontak');
});
Route::get('/page/kontak-resmi', function () {
    return view('frontend.kontak');
});
Route::post('/kontak', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'nama' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'subjek' => 'required|string|max:255',
        'pesan' => 'required|string',
    ]);
    
    return back()->with('success', 'Terima kasih, pesan Anda telah berhasil dikirim ke Bagian Pemerintahan Kabupaten Probolinggo.');
});

// === GALERI FOTO & GALERI VIDEO PUBLIK ===
Route::get('/galeri-foto', function () {
    $photos = \App\Models\Gallery::where('type', 'image')->orWhereNotNull('file_path')->latest()->paginate(12);
    return view('frontend.foto', compact('photos'));
});
Route::get('/page/galeri-foto', function () {
    $photos = \App\Models\Gallery::where('type', 'image')->orWhereNotNull('file_path')->latest()->paginate(12);
    return view('frontend.foto', compact('photos'));
});
Route::get('/galeri/foto', function () {
    $photos = \App\Models\Gallery::where('type', 'image')->orWhereNotNull('file_path')->latest()->paginate(12);
    return view('frontend.foto', compact('photos'));
});

Route::get('/galeri-video', function () {
    $videos = \App\Models\Gallery::where('type', 'video')->orWhereNotNull('video_url')->latest()->paginate(12);
    return view('frontend.video', compact('videos'));
});
Route::get('/page/video', function () {
    $videos = \App\Models\Gallery::where('type', 'video')->orWhereNotNull('video_url')->latest()->paginate(12);
    return view('frontend.video', compact('videos'));
});
Route::get('/page/galeri-video', function () {
    $videos = \App\Models\Gallery::where('type', 'video')->orWhereNotNull('video_url')->latest()->paginate(12);
    return view('frontend.video', compact('videos'));
});
Route::get('/galeri/video', function () {
    $videos = \App\Models\Gallery::where('type', 'video')->orWhereNotNull('video_url')->latest()->paginate(12);
    return view('frontend.video', compact('videos'));
});

Route::get('/galeri', function () {
    $photos = \App\Models\Gallery::where('type', 'image')->orWhereNotNull('file_path')->latest()->paginate(12);
    return view('frontend.foto', compact('photos'));
});
Route::get('/galleries', function () {
    $photos = \App\Models\Gallery::where('type', 'image')->orWhereNotNull('file_path')->latest()->paginate(12);
    return view('frontend.foto', compact('photos'));
});

// 3. Document List (Otomatis dari Menu)

// 4. PDF Viewer Terintegrasi (Standar Pelayanan Publik, dll)
Route::get('/halaman/{slug}', function ($slug) {
    $page = \App\Models\Page::where('slug', $slug)->first();
    
    if (!$page) {
        $title = ucwords(str_replace('-', ' ', $slug));
        return view('frontend.pdf-viewer', compact('title', 'slug', 'page'));
    }
    
    $title = $page->title;
    return view('frontend.pdf-viewer', compact('title', 'slug', 'page'));
});

Route::get('/struktur-organisasi', function () {
    $members = \App\Models\OrganizationMember::with('children')->whereNull('parent_id')->orderBy('order_index')->get();
    $mode = \App\Models\Setting::where('key', 'org_structure_mode')->value('value') ?? 'dynamic';
    $photo = \App\Models\Setting::where('key', 'org_structure_photo')->value('value');
    return view('frontend.struktur', compact('members', 'mode', 'photo'));
});

Route::get('/dokumen/{kategori}', function ($kategori) {
    // Cari menu dengan tipe dokumen yang sesuai dengan slug kategori
    $menus = \App\Models\Menu::where('module_type', 'documents')->get();
    $matchedMenu = null;
    
    foreach ($menus as $menu) {
        $expectedSlug = str_replace(' ', '-', strtolower($menu->title));
        if ($expectedSlug === strtolower($kategori)) {
            $matchedMenu = $menu;
            break;
        }
    }
    
    // Jika tidak ditemukan, fallback ke ucwords biasa (barangkali ada dokumen lama yang belum terikat menu)
    $categoryName = $matchedMenu ? $matchedMenu->title : ucwords(str_replace('-', ' ', $kategori));
    
    $documents = \App\Models\Document::where('category', $categoryName)->latest()->get();
    
    // Fallback view jika frontend.dokumen tidak ada, pakai frontend.document-list (tergantung tema)
    if (view()->exists('frontend.dokumen')) {
        return view('frontend.dokumen', ['documents' => $documents, 'kategori' => $categoryName]);
    }
    return view('frontend.document-list', ['documents' => $documents, 'categoryName' => $categoryName]);
});

// Generic Page Fallback
Route::get('/page/{slug}', function ($slug) {
    $page = \App\Models\Page::where('slug', $slug)->first();
    
    if ($page && $page->external_url) {
        return redirect()->away($page->external_url);
    }
    
    if (!$page) {
        $title = ucwords(str_replace('-', ' ', $slug));
        return view('frontend.page', compact('title', 'slug', 'page'));
    }
    
    $title = $page->title;
    return view('frontend.page', compact('title', 'slug', 'page'));
});


// === ROUTES AUTHENTICATION ===
Route::get('/refresh_captcha', function() {
    return response()->json(['captcha' => captcha_img('flat')]);
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// === ROUTES BACKEND ADMIN PANEL ===
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // CKEditor 5 Upload Endpoint (Images, PDF, Word, Excel, ZIP)
    Route::post('/ckeditor/upload', function (\Illuminate\Http\Request $request) {
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = strtolower($file->getClientOriginalExtension());
            $safeName = \Illuminate\Support\Str::slug($originalName) . '_' . time() . '.' . $extension;
            
            $path = $file->storeAs('uploads/ckeditor', $safeName, 'public');
            $url = asset('storage/' . $path);
            
            return response()->json([
                'uploaded' => true,
                'fileName' => $safeName,
                'url' => $url
            ]);
        }
        return response()->json([
            'uploaded' => false,
            'error' => ['message' => 'Gagal mengunggah berkas. Silakan coba lagi.']
        ]);
    })->name('ckeditor.upload');

    // Modules CRUD Routes (Admin OPD & Superadmin)
    Route::resource('posts', PostController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('documents', DocumentController::class);
    Route::resource('galleries', GalleryController::class);
    Route::resource('banners', BannerController::class);
    Route::resource('pages', PageController::class);
    Route::get('/instagram', [\App\Http\Controllers\Admin\InstagramPostController::class, 'index'])->name('instagram.index');
    Route::post('/instagram/profile', [\App\Http\Controllers\Admin\InstagramPostController::class, 'updateProfile'])->name('instagram.profile');
    
    // Admin & Superadmin Setting Routes
    Route::resource('contact-settings', ContactSettingController::class)->only(['index', 'store']);
    
    // Superadmin Exclusive Routes (Khusus Struktur Navigasi Menu, Settings, & Manajemen User)
    Route::middleware(['role:Superadmin'])->group(function () {
        Route::post('organization-members/upload-photo', [\App\Http\Controllers\Admin\OrganizationMemberController::class, 'uploadPhoto'])->name('organization-members.upload-photo');
        Route::post('organization-members/delete-photo', [\App\Http\Controllers\Admin\OrganizationMemberController::class, 'deletePhoto'])->name('organization-members.delete-photo');
        Route::get('organization-members', [\App\Http\Controllers\Admin\OrganizationMemberController::class, 'index'])->name('organization-members.index');
        Route::get('/menus/{menu}/submenus', [\App\Http\Controllers\Admin\MenuController::class, 'submenus'])->name('menus.submenus');
        Route::get('/menus/{menu}/content', [\App\Http\Controllers\Admin\MenuController::class, 'createContent'])->name('menus.content');
        Route::post('/menus/{menu}/toggle', [\App\Http\Controllers\Admin\MenuController::class, 'toggle'])->name('menus.toggle');
        Route::post('/menus/{id}/restore', [\App\Http\Controllers\Admin\MenuController::class, 'restore'])->name('menus.restore');
        Route::post('/menus/reorder', [\App\Http\Controllers\Admin\MenuController::class, 'reorder'])->name('menus.reorder');
        Route::resource('menus', MenuController::class);
        Route::resource('settings', SettingController::class)->only(['index', 'store']);
        Route::resource('home-widgets', HomeWidgetController::class)->except(['show']);
        Route::resource('related-links', \App\Http\Controllers\Admin\RelatedLinkController::class)->except(['show', 'index']);
        Route::resource('users', UserController::class);
        Route::delete('activity-logs/purge-old', [\App\Http\Controllers\Admin\ActivityLogController::class, 'purgeOld'])->name('activity-logs.purge-old');
        Route::resource('activity-logs', \App\Http\Controllers\Admin\ActivityLogController::class)->only(['index', 'show']);
        
        // Custom route for user toggle active
        Route::post('/users/{user}/toggle', [UserController::class, 'toggleActive'])->name('users.toggle');
    });
});
