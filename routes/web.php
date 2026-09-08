<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\DashboardController;
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
use App\Http\Controllers\Admin\SurveySettingController;
use App\Models\Banner;
use App\Models\InstagramPost;
use App\Models\Post;
use App\Models\Gallery;
use App\Models\HomeWidget;
use App\Models\RelatedLink;
use App\Models\Category;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Menu;
use App\Models\Document;
use App\Http\Controllers\Admin\RelatedLinkController;
use App\Http\Controllers\Admin\ContentActivityController;
use App\Http\Controllers\Admin\OrganizationMemberController;
use App\Http\Controllers\Admin\InstagramPostController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\ExternalLinkSettingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

// Homepage
Route::get('/', function () {
    $now = now();

    // Auto-deactivate expired banners
    $expiredBanners = Banner::where('is_active', true)->whereNotNull('end_date')->where('end_date', '<', $now)->get();
    foreach ($expiredBanners as $banner) {
        $banner->update(['is_active' => false]);
    }

    $banners = Banner::where('is_published', true)
        ->where('is_active', true)
        ->where(function ($q) use ($now) {
            $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
        })
        ->where(function ($q) use ($now) {
            $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
        })
        ->orderBy('order_index', 'asc')
        ->latest()
        ->get();
    $dbIgPosts = InstagramPost::where('is_active', true)->orderBy('order_index', 'asc')->latest()->take(6)->get();
    $latestPosts = Post::with('category')->where('is_published', true)->where('is_active', true)->latest()->take(4)->get();
    $videos = Gallery::where('type', 'video')->where('is_active', true)->where(function($q) { $q->whereNotNull('video_url')->orHas('galleryItems'); })->latest()->take(3)->get();
    $homeWidgets = HomeWidget::where('is_active', true)->orderBy('order_index')->get();
    $relatedLinks = RelatedLink::where('is_active', true)->orderBy('order')->get();
    return view('frontend.home', compact('banners', 'dbIgPosts', 'latestPosts', 'videos', 'homeWidgets', 'relatedLinks'));
});

// === ROUTES BERITA & ARTIKEL PUBLIK ===
Route::get('/informasi', function (Request $request) {
    $query = Post::with(['category', 'user'])->where('is_published', true)->where('is_active', true);
    
    if ($request->filled('q')) {
        $query->where('title', 'like', '%' . $request->q . '%');
    }
    
    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }
    
    $posts = $query->latest()->paginate(9)->withQueryString();
    $categories = Category::all();
    
    return view('frontend.posts', compact('posts', 'categories'));
});

Route::get('/berita', function (Request $request) {
    $query = Post::with(['category', 'user'])->where('is_published', true)->where('is_active', true);
    
    if ($request->filled('q')) {
        $query->where('title', 'like', '%' . $request->q . '%');
    }
    
    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }
    
    $posts = $query->latest()->paginate(9)->withQueryString();
    $categories = Category::all();
    
    return view('frontend.posts', compact('posts', 'categories'));
});

Route::get('/posts', function (Request $request) {
    $query = Post::with(['category', 'user'])->where('is_published', true)->where('is_active', true);
    
    if ($request->filled('q')) {
        $query->where('title', 'like', '%' . $request->q . '%');
    }
    
    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }
    
    $posts = $query->latest()->paginate(9)->withQueryString();
    $categories = Category::all();
    
    return view('frontend.posts', compact('posts', 'categories'));
});

Route::get('/informasi/{slug}', function ($slug) {
    $post = Post::with(['category', 'user', 'postImages' => function($q) { $q->orderBy('order_index'); }])->where('slug', $slug)->first();
    
    if (!$post) {
        $post = Post::with(['category', 'user', 'postImages' => function($q) { $q->orderBy('order_index'); }])->where('id', $slug)->orWhere('slug', 'like', $slug . '%')->first();
    }
    
    if (!$post) {
        abort(404);
    }
    
    $latestPosts = Post::with('category')->where('id', '!=', $post->id)->where('is_published', true)->where('is_active', true)->latest()->take(5)->get();
    
    return view('frontend.post-detail', compact('post', 'latestPosts'));
});

Route::get('/berita/{slug}', function ($slug) {
    $post = Post::with(['category', 'user', 'postImages' => function($q) { $q->orderBy('order_index'); }])->where('slug', $slug)->first();
    
    if (!$post) {
        $post = Post::with(['category', 'user', 'postImages' => function($q) { $q->orderBy('order_index'); }])->where('id', $slug)->orWhere('slug', 'like', $slug . '%')->first();
    }
    
    if (!$post) {
        abort(404);
    }
    
    $latestPosts = Post::with('category')->where('id', '!=', $post->id)->where('is_published', true)->where('is_active', true)->latest()->take(5)->get();
    
    return view('frontend.post-detail', compact('post', 'latestPosts'));
});

Route::get('/posts/{slug}', function ($slug) {
    $post = Post::with(['category', 'user', 'postImages' => function($q) { $q->orderBy('order_index'); }])->where('slug', $slug)->first();
    
    if (!$post) {
        $post = Post::with(['category', 'user', 'postImages' => function($q) { $q->orderBy('order_index'); }])->where('id', $slug)->orWhere('slug', 'like', $slug . '%')->first();
    }
    
    if (!$post) {
        abort(404);
    }
    
    $latestPosts = Post::with('category')->where('id', '!=', $post->id)->where('is_published', true)->where('is_active', true)->latest()->take(5)->get();
    
    return view('frontend.post-detail', compact('post', 'latestPosts'));
});

// === ROUTES FRONTEND SPESIFIK SESUAI DESAIN ===

// 1. Struktur Organisasi
Route::get('/page/struktur-organisasi', function () {
    $page = Page::where('slug', 'struktur-organisasi')->first();
    $photo = Setting::where('key', 'org_structure_photo')->value('value');
    return view('frontend.struktur', compact('page', 'photo'));
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
Route::post('/kontak', function (Request $request) {
    $request->validate([
        'nama' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'subjek' => 'required|string|max:255',
        'pesan' => 'required|string',
    ]);
    
    return back()->with('success', 'Terima kasih, pesan Anda telah berhasil dikirim ke Bagian Pemerintahan Kabupaten Probolinggo.');
});

// === GALERI FOTO & GALERI VIDEO PUBLIK ===
Route::get('/galeri-foto', function (\Illuminate\Http\Request $request) {
    $query = Gallery::where('type', 'image')->where('is_active', true)->where(function($q) {
        $q->whereNotNull('file_path')->orHas('galleryItems');
    });
    if ($request->filled('q')) {
        $query->where('title', 'like', '%' . $request->q . '%');
    }
    $photos = $query->latest()->paginate(12)->withQueryString();
    return view('frontend.foto', compact('photos'));
});
Route::get('/galeri-foto/{gallery}', function (Gallery $gallery) {
    if ($gallery->type !== 'image' || !$gallery->is_active) abort(404);
    $gallery->load(['galleryItems' => function($q) { $q->orderBy('order_index'); }]);
    return view('frontend.foto-detail', compact('gallery'));
})->name('frontend.foto.detail');

Route::get('/page/galeri-foto', function () {
    $photos = Gallery::where('type', 'image')->where('is_active', true)->where(function($q) { $q->whereNotNull('file_path'); })->latest()->paginate(12);
    return view('frontend.foto', compact('photos'));
});
Route::get('/galeri/foto', function () {
    $photos = Gallery::where('type', 'image')->where('is_active', true)->where(function($q) { $q->whereNotNull('file_path'); })->latest()->paginate(12);
    return view('frontend.foto', compact('photos'));
});

Route::get('/galeri-video', function (\Illuminate\Http\Request $request) {
    $query = Gallery::where('type', 'video')->where('is_active', true)->where(function($q) {
        $q->whereNotNull('video_url')->orHas('galleryItems');
    });
    if ($request->filled('q')) {
        $query->where('title', 'like', '%' . $request->q . '%');
    }
    $videos = $query->latest()->paginate(12)->withQueryString();
    return view('frontend.video', compact('videos'));
});
Route::get('/galeri-video/{gallery}', function (Gallery $gallery) {
    if ($gallery->type !== 'video' || !$gallery->is_active) abort(404);
    $gallery->load(['galleryItems' => function($q) { $q->orderBy('order_index'); }]);
    return view('frontend.video-detail', compact('gallery'));
})->name('frontend.video.detail');
Route::get('/page/video', function () {
    $videos = Gallery::where('type', 'video')->where('is_active', true)->where(function($q) { $q->whereNotNull('video_url'); })->latest()->paginate(12);
    return view('frontend.video', compact('videos'));
});
Route::get('/page/galeri-video', function () {
    $videos = Gallery::where('type', 'video')->where('is_active', true)->where(function($q) { $q->whereNotNull('video_url'); })->latest()->paginate(12);
    return view('frontend.video', compact('videos'));
});
Route::get('/galeri/video', function () {
    $videos = Gallery::where('type', 'video')->where('is_active', true)->where(function($q) { $q->whereNotNull('video_url'); })->latest()->paginate(12);
    return view('frontend.video', compact('videos'));
});

Route::get('/galeri', function () {
    $photos = Gallery::where('type', 'image')->where('is_active', true)->where(function($q) { $q->whereNotNull('file_path'); })->latest()->paginate(12);
    return view('frontend.foto', compact('photos'));
});
Route::get('/galleries', function () {
    $photos = Gallery::where('type', 'image')->where('is_active', true)->where(function($q) { $q->whereNotNull('file_path'); })->latest()->paginate(12);
    return view('frontend.foto', compact('photos'));
});

// 3. Document List (Otomatis dari Menu)

// 4. PDF Viewer Terintegrasi (Standar Pelayanan Publik, dll)
Route::get('/halaman/{slug}', function ($slug) {
    $page = Page::where('slug', $slug)->first();
    
    if (!$page) {
        $title = ucwords(str_replace('-', ' ', $slug));
        return view('frontend.pdf-viewer', compact('title', 'slug', 'page'));
    }
    
    $title = $page->title;
    return view('frontend.pdf-viewer', compact('title', 'slug', 'page'));
});

Route::get('/struktur-organisasi', function () {
    $photo = Setting::where('key', 'org_structure_photo')->value('value');
    return view('frontend.struktur', compact('photo'));
});

Route::get('/dokumen/{kategori}', function ($kategori) {
    // Cari menu dengan tipe dokumen yang sesuai dengan slug kategori
    $menus = Menu::where('module_type', 'documents')->get();
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
    
    $query = Document::where('category', $categoryName);
    
    if (request()->filled('year')) {
        $query->whereYear(DB::raw('COALESCE(document_date, created_at)'), request('year'));
    }
    
    $documents = $query->latest()->get();
    
    // Ambil tahun yang tersedia untuk kategori ini (tanpa terpengaruh filter year saat ini)
    $availableYears = Document::where('category', $categoryName)
        ->selectRaw('YEAR(COALESCE(document_date, created_at)) as year')
        ->distinct()
        ->pluck('year')
        ->filter()
        ->sort()
        ->values()
        ->toArray();
    
    // Fallback view jika frontend.dokumen tidak ada, pakai frontend.document-list (tergantung tema)
    if (view()->exists('frontend.dokumen')) {
        return view('frontend.dokumen', ['documents' => $documents, 'kategori' => $categoryName, 'availableYears' => $availableYears]);
    }
    return view('frontend.document-list', ['documents' => $documents, 'categoryName' => $categoryName, 'availableYears' => $availableYears]);
});

// Generic Page Fallback
Route::get('/page/{slug}', function ($slug) {
    $page = Page::where('slug', $slug)->first();
    
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
})->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.post')->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email')->middleware('throttle:6,1');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update')->middleware('throttle:5,1');
});


// === ROUTES BACKEND ADMIN PANEL ===
Route::middleware(['auth', 'prevent-back-history'])->prefix('admin')->name('admin.')->group(function () {
    
    // CKEditor 5 Upload Endpoint (Images, PDF, Word, Excel, ZIP)
    Route::post('/ckeditor/upload', function (Request $request) {
        $validator = Validator::make($request->all(), [
            'upload' => 'required|file|mimes:jpg,jpeg,png,webp,gif,pdf,doc,docx,xls,xlsx,zip|max:51200'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'uploaded' => false,
                'error' => ['message' => '⚠️ Format file tidak diizinkan. Hanya menerima Gambar, PDF, Word, Excel, dan ZIP.']
            ]);
        }

        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = strtolower($file->getClientOriginalExtension());
            $safeName = Str::slug($originalName) . '_' . time() . '.' . $extension;
            
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

    // Modules CRUD Routes (Superadmin, Admin OPD, Staf)
    Route::middleware(['role:Superadmin|Admin OPD|Staf'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        Route::patch('posts/{post}/toggle', [PostController::class, 'toggleStatus'])->name('posts.toggle');
        Route::delete('posts/{post}/delete-image/{imageId}', [PostController::class, 'deleteImage'])->name('posts.delete-image');
        Route::post('posts/{post}/reorder-images', [PostController::class, 'reorderImages'])->name('posts.reorder-images');
        Route::resource('posts', PostController::class);
        
        Route::delete('galleries/{gallery}/delete-item/{itemId}', [GalleryController::class, 'deleteItem'])->name('galleries.delete-item');
        Route::post('galleries/{gallery}/reorder-items', [GalleryController::class, 'reorderItems'])->name('galleries.reorder-items');
        Route::patch('galleries/{gallery}/toggle', [GalleryController::class, 'toggleStatus'])->name('galleries.toggle');
        Route::resource('galleries', GalleryController::class);
        
        Route::resource('documents', DocumentController::class);
        Route::resource('content-activities', ContentActivityController::class)->only(['index', 'show']);
    });

    // Modules CRUD Routes (Hanya Admin OPD & Superadmin)
    Route::middleware(['role:Superadmin|Admin OPD'])->group(function () {
        Route::patch('categories/{category}/toggle', [CategoryController::class, 'toggleStatus'])->name('categories.toggle');
        Route::resource('categories', CategoryController::class);
        Route::patch('banners/{banner}/toggle-active', [BannerController::class, 'toggleActive'])->name('banners.toggle-active');
        Route::resource('banners', BannerController::class);
        Route::resource('home-widgets', HomeWidgetController::class)->except(['show']);
        Route::patch('related-links/{related_link}/toggle', [RelatedLinkController::class, 'toggleStatus'])->name('related-links.toggle');
        Route::resource('related-links', RelatedLinkController::class)->except(['show']);
        Route::resource('survey-settings', SurveySettingController::class)->only(['index', 'store']);
        Route::resource('external-links', ExternalLinkSettingController::class)->only(['index', 'store']);
        Route::resource('pages', PageController::class);
        Route::post('organization-members/upload-photo', [OrganizationMemberController::class, 'uploadPhoto'])->name('organization-members.upload-photo');
        Route::post('organization-members/delete-photo', [OrganizationMemberController::class, 'deletePhoto'])->name('organization-members.delete-photo');
        Route::get('organization-members', [OrganizationMemberController::class, 'index'])->name('organization-members.index');
        Route::get('/instagram', [InstagramPostController::class, 'index'])->name('instagram.index');
        Route::post('/instagram/profile', [InstagramPostController::class, 'updateProfile'])->name('instagram.profile');
        
        // Admin & Superadmin Setting Routes
        Route::resource('contact-settings', ContactSettingController::class)->only(['index', 'store']);
    });

    // Admin Profile (Profil Saya)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Superadmin Exclusive Routes (Khusus Struktur Navigasi Menu, Settings, & Manajemen User)
    Route::middleware(['role:Superadmin'])->group(function () {

        Route::get('/menus/{menu}/submenus', [MenuController::class, 'submenus'])->name('menus.submenus');
        Route::get('/menus/{menu}/content', [MenuController::class, 'createContent'])->name('menus.content');
        Route::post('/menus/{menu}/toggle', [MenuController::class, 'toggle'])->name('menus.toggle');
        Route::post('/menus/{id}/restore', [MenuController::class, 'restore'])->name('menus.restore');
        Route::post('/menus/reorder', [MenuController::class, 'reorder'])->name('menus.reorder');
        Route::resource('menus', MenuController::class);
        Route::resource('settings', SettingController::class)->only(['index', 'store']);
        Route::resource('users', UserController::class);
        Route::delete('activity-logs/purge-old', [ActivityLogController::class, 'purgeOld'])->name('activity-logs.purge-old');
        Route::resource('activity-logs', ActivityLogController::class)->only(['index', 'show']);
        
        // Custom route for user toggle active
        Route::post('/users/{user}/toggle', [UserController::class, 'toggleActive'])->name('users.toggle');
    });
});


