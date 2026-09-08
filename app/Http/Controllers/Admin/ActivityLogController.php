<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;
use App\Models\Post;
use App\Models\Page;
use App\Models\Document;
use App\Models\Banner;
use App\Models\HomeWidget;
use App\Models\Category;
use App\Models\RelatedLink;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with('causer')->latest();

        // Search
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
                      Post::class, 
                      Page::class,
                      Document::class,
                      Banner::class,
                      HomeWidget::class
                  ], function($subjectQ) use ($search) {
                      $subjectQ->where('title', 'like', "%{$search}%");
                  })
                  ->orWhereHasMorph('subject', [Category::class, RelatedLink::class], function($subjectQ) use ($search) {
                      $subjectQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filters
        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->user_id);
        }

        if ($request->filled('role')) {
            $query->where('properties->role', $request->role);
        }

        if ($request->filled('type')) {
            $query->where('properties->type', $request->type);
        }

        if ($request->filled('module')) {
            $query->where('properties->module', $request->module);
        }

        if ($request->filled('ip')) {
            $query->where('properties->ip', 'like', '%' . $request->ip . '%');
        }

        if ($request->filled('status')) {
            $query->where('properties->status', $request->status);
        }

        if ($request->filled('level')) {
            $query->where('properties->level', $request->level);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(20)->withQueryString();
        
        $users = User::orderBy('name')->get();

        // Ambil list modul, tipe, dan role unik dari tabel logs jika memungkinkan
        $modules = Activity::selectRaw("JSON_UNQUOTE(JSON_EXTRACT(properties, '$.module')) as module")->distinct()->pluck('module')->filter();
        $types = Activity::selectRaw("JSON_UNQUOTE(JSON_EXTRACT(properties, '$.type')) as type")->distinct()->pluck('type')->filter();
        $roles = Activity::selectRaw("JSON_UNQUOTE(JSON_EXTRACT(properties, '$.role')) as role")->distinct()->pluck('role')->filter();
        $levels = ['INFO', 'WARNING', 'CRITICAL'];

        // Hitung jumlah log yang usianya lebih dari 3 bulan
        $oldLogsCount = Activity::where('created_at', '<', \Carbon\Carbon::now()->subMonths(3))->count();

        return view('admin.activity_logs.index', compact('logs', 'users', 'modules', 'types', 'roles', 'levels', 'oldLogsCount'));
    }

    public function show(string $id)
    {
        $log = Activity::with('causer')->findOrFail($id);
        
        return view('admin.activity_logs.show', compact('log'));
    }

    public function purgeOld(Request $request)
    {
        $cutoffDate = \Carbon\Carbon::now()->subMonths(3);
        $count = Activity::where('created_at', '<', $cutoffDate)->count();
        
        if ($count > 0) {
            Activity::where('created_at', '<', $cutoffDate)->delete();
            return redirect()->route('admin.activity-logs.index')->with('success', "Berhasil menghapus {$count} log aktivitas yang lebih dari 3 bulan.");
        }
        
        return redirect()->route('admin.activity-logs.index')->with('info', 'Tidak ada log aktivitas yang berusia lebih dari 3 bulan.');
    }
}
