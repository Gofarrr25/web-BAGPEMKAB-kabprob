<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\Document;
use App\Models\Gallery;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Variabel penampung statistik
        $totalPosts = 0;
        $totalDocuments = 0;
        $totalGalleries = 0;
        $totalVideos = 0;
        $totalAdmins = 0;
        $totalStafs = 0;

        if ($user->hasRole('Superadmin') || $user->hasRole('Admin') || $user->hasRole('Admin OPD')) {
            $totalPosts = Post::count();
            $totalDocuments = Document::count();
            $totalGalleries = Gallery::where('type', 'image')->count();
            $totalVideos = Gallery::where('type', 'video')->count();

            if ($user->hasRole('Superadmin')) {
                // Superadmin bisa melihat total Admin dan Staf
                $totalAdmins = User::role('Admin OPD')->count();
                $totalStafs = User::role('Staf')->count();
            }
        } elseif ($user->hasRole('Staf')) {
            // Staf hanya melihat data miliknya (ownership)
            $userId = $user->id;
            $totalPosts = Post::where('user_id', $userId)->count();
            $totalDocuments = Document::where('user_id', $userId)->count();
            $totalGalleries = Gallery::where('type', 'image')->where('user_id', $userId)->count();
            $totalVideos = Gallery::where('type', 'video')->where('user_id', $userId)->count();
        }

        // Aktivitas Konten Terbaru
        $contentModels = [
            \App\Models\Post::class,
            \App\Models\Page::class,
            \App\Models\Document::class,
            \App\Models\Category::class,
            \App\Models\Banner::class,
            \App\Models\Gallery::class,
            \App\Models\Agenda::class,
            \App\Models\HomeWidget::class,
            \App\Models\RelatedLink::class,
            \App\Models\InstagramPost::class,
        ];

        $activityQuery = Activity::with('causer')
            ->whereIn('subject_type', $contentModels)
            ->latest();

        if ($user->hasRole('Staf')) {
            // Staf hanya melihat aktivitas miliknya
            $activityQuery->where('causer_id', $user->id);
        } elseif ($user->hasRole('Admin OPD') || $user->hasRole('Admin')) {
            // Admin tidak boleh melihat aktivitas Superadmin
            $activityQuery->where(function($q) {
                $q->whereHas('causer', function($causerQ) {
                    $causerQ->whereDoesntHave('roles', function($roleQ) {
                        $roleQ->where('name', 'Superadmin');
                    });
                })->orWhereNull('causer_id');
            });
        }
        // Superadmin tidak difilter

        $recentActivities = $activityQuery->take(5)->get();

        return view('admin.dashboard', compact(
            'totalPosts',
            'totalDocuments',
            'totalGalleries',
            'totalVideos',
            'totalAdmins',
            'totalStafs',
            'recentActivities'
        ));
    }
}
