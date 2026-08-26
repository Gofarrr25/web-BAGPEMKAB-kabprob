<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ContentActivityController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->check() && auth()->user()->hasRole('Superadmin')) {
            abort(403, 'Akses Ditolak: Superadmin menggunakan menu Log Aktivitas Sistem.');
        }

        // Daftar model yang secara ketat diklasifikasikan sebagai "Konten Website"
        // Login, Logout, User, Role, Permission otomatis tidak akan masuk ke sini.
        $contentModels = [
            \App\Models\Post::class,
            \App\Models\Page::class,
            \App\Models\Document::class,
            \App\Models\Category::class,
            \App\Models\Banner::class,
            \App\Models\Gallery::class,
            \App\Models\Agenda::class,
            \App\Models\Announcement::class,
            \App\Models\HomeWidget::class,
            \App\Models\RelatedLink::class,
            \App\Models\InstagramPost::class,
            \App\Models\OrganizationMember::class,
        ];

        $query = Activity::with('causer')
            ->whereIn('subject_type', $contentModels)
            ->whereHas('causer.roles', function($q) {
                $q->where('name', '!=', 'Superadmin');
            })
            ->latest();

        // Pencarian sederhana
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('properties->url', 'like', "%{$search}%")
                  ->orWhere('properties->ip', 'like', "%{$search}%")
                  ->orWhere('properties->username', 'like', "%{$search}%")
                  ->orWhereHas('causer', function($causerQ) use ($search) {
                      $causerQ->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHasMorph('subject', [
                      \App\Models\Post::class, 
                      \App\Models\Page::class,
                      \App\Models\Document::class,
                      \App\Models\Banner::class,
                      \App\Models\HomeWidget::class
                  ], function($subjectQ) use ($search) {
                      $subjectQ->where('title', 'like', "%{$search}%");
                  })
                  ->orWhereHasMorph('subject', [\App\Models\Category::class, \App\Models\RelatedLink::class], function($subjectQ) use ($search) {
                      $subjectQ->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter Tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter Modul
        if ($request->filled('module')) {
            $query->where('properties->module', $request->module);
        }

        // Filter Jenis Aktivitas
        if ($request->filled('type')) {
            $query->where('properties->type', $request->type);
        }

        $logs = $query->paginate(15)->withQueryString();
        
        $baseFilterQuery = Activity::whereIn('subject_type', $contentModels)
            ->whereHas('causer.roles', function($q) {
                $q->where('name', '!=', 'Superadmin');
            });

        $types = (clone $baseFilterQuery)->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(properties, '$.type')) as type")->distinct()->pluck('type')->filter();
        $modules = (clone $baseFilterQuery)->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(properties, '$.module')) as module")->distinct()->pluck('module')->filter();

        return view('admin.content_activities.index', compact('logs', 'types', 'modules'));
    }

    public function show($id)
    {
        if (auth()->check() && auth()->user()->hasRole('Superadmin')) {
            abort(403, 'Akses Ditolak: Superadmin menggunakan menu Log Aktivitas Sistem.');
        }

        $log = Activity::with('causer')->findOrFail($id);
        
        // Pastikan admin tidak membuka detail dari restricted models
        $contentModels = [
            \App\Models\Post::class,
            \App\Models\Page::class,
            \App\Models\Document::class,
            \App\Models\Category::class,
            \App\Models\Banner::class,
            \App\Models\Gallery::class,
            \App\Models\Agenda::class,
            \App\Models\Announcement::class,
            \App\Models\HomeWidget::class,
            \App\Models\RelatedLink::class,
            \App\Models\InstagramPost::class,
            \App\Models\OrganizationMember::class,
        ];

        if (!in_array($log->subject_type, $contentModels)) {
            abort(403, 'Anda tidak memiliki izin untuk melihat detail aktivitas ini (Hanya untuk konten).');
        }

        return view('admin.activity_logs.show', compact('log'));
    }
}


