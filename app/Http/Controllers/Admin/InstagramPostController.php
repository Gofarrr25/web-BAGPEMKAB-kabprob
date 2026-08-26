<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstagramPost;
use App\Models\Setting;
use App\Services\InstagramService;
use Illuminate\Http\Request;

class InstagramPostController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        $username = $settings['instagram_username'] ?? InstagramService::extractUsername($settings['instagram_url'] ?? '');
        
        return view('admin.instagram.index', compact('settings', 'username'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'instagram_url' => 'required|string|max:500',
            'instagram_name' => 'nullable|string|max:255',
            'instagram_followers' => 'nullable|string|max:100',
            'instagram_posts_count' => 'nullable|string|max:100',
            'instagram_avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
            'instagram_embed_script' => 'nullable|string',
        ]);

        $config = InstagramService::updateAccountConfig($request->instagram_url);

        if ($request->filled('instagram_name')) {
            Setting::updateOrCreate(['key' => 'instagram_name'], ['value' => $request->instagram_name]);
        }
        if ($request->filled('instagram_followers')) {
            Setting::updateOrCreate(['key' => 'instagram_followers'], ['value' => $request->instagram_followers]);
        }
        if ($request->filled('instagram_posts_count')) {
            Setting::updateOrCreate(['key' => 'instagram_posts_count'], ['value' => $request->instagram_posts_count]);
        }
        if ($request->has('instagram_embed_script')) {
            Setting::updateOrCreate(['key' => 'instagram_embed_script'], ['value' => $request->instagram_embed_script]);
        }

        if ($request->hasFile('instagram_avatar')) {
            $targetDir = storage_path('app/public/settings');
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $file = $request->file('instagram_avatar');
            $filename = 'instagram_avatar_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($targetDir, $filename);
            Setting::updateOrCreate(['key' => 'instagram_avatar'], ['value' => 'settings/' . $filename]);
        }

        return redirect()->route('admin.instagram.index')
            ->with('success', 'Profil & Pengaturan Instagram berhasil diperbarui!');
    }
}

