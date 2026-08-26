<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->hasRole('Superadmin')) abort(403);
        
        $settings = Setting::all()->pluck('value', 'key');
        $homeWidgets = \App\Models\HomeWidget::orderBy('order_index')->get();
        $relatedLinks = \App\Models\RelatedLink::orderBy('order')->get();
        return view('admin.settings.index', compact('settings', 'homeWidgets', 'relatedLinks'));
    }

    public function store(Request $request)
    {
        return $this->update($request);
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->hasRole('Superadmin')) abort(403);

        $request->validate([
            'site_name' => 'nullable|string|max:255',
            'site_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
            'footer_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
            'survey_qr_image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
            'office_address' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|string',
            'facebook_url' => 'nullable|string',
            'instagram_url' => 'nullable|string',
            'tiktok_url' => 'nullable|string',
            'youtube_url' => 'nullable|string',
            'footer_description' => 'nullable|string',
            'footer_copyright' => 'nullable|string',
            'survey_title' => 'nullable|string',
            'survey_link' => 'nullable|string',
            'org_structure_mode' => 'nullable|in:dynamic,photo',
            'org_structure_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
        ], [
            'site_logo.mimes' => 'Format file tidak valid. Gunakan JPG, PNG, GIF, atau WebP.',
            'footer_logo.mimes' => 'Format file tidak valid. Gunakan JPG, PNG, GIF, atau WebP.',
            'survey_qr_image.mimes' => 'Format file tidak valid. Gunakan JPG, PNG, GIF, atau WebP.',
            'org_structure_photo.mimes' => 'Format file tidak valid. Gunakan JPG, PNG, GIF, atau WebP.',
        ]);

        $imageFields = ['site_logo', 'footer_logo', 'survey_qr_image', 'org_structure_photo'];
        $targetDir = storage_path('app/public/settings');
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        foreach ($imageFields as $imgField) {
            if ($request->hasFile($imgField)) {
                $file = $request->file($imgField);
                $filename = $imgField . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move($targetDir, $filename);
                
                Setting::updateOrCreate(
                    ['key' => $imgField],
                    ['value' => 'settings/' . $filename]
                );
            }
        }

        $data = $request->except(array_merge(['_token', '_method'], $imageFields));

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? '']
            );
        }

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan Website & Footer berhasil diperbarui.');
    }
}
