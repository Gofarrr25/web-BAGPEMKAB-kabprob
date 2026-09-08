<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExternalLinkSettingController extends Controller
{
    public function index()
    {
        $settings = Setting::whereIn('key', ['external_link_title', 'external_link_url', 'external_link_is_active'])
            ->pluck('value', 'key');
            
        return view('admin.external_link_settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'external_link_title' => 'nullable|string',
            'external_link_url' => 'nullable|url',
            'external_link_qr_image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'external_link_qr_image.mimes' => '⚠️ Format file tidak sesuai. Field ini hanya menerima JPG, JPEG, PNG, atau WEBP.',
        ]);

        if ($request->hasFile('external_link_qr_image')) {
            $file = $request->file('external_link_qr_image');
            $targetDir = storage_path('app/public/settings');
            
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            
            $filename = 'external_link_qr_image_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($targetDir, $filename);
            
            Setting::updateOrCreate(
                ['key' => 'external_link_qr_image'],
                ['value' => 'settings/' . $filename]
            );
        }

        $data = $request->only(['external_link_title', 'external_link_url']);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? '']
            );
        }
        
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        activity('admin_log')
            ->causedBy($user)
            ->withProperties([
                'module' => 'Tautan Eksternal',
                'type' => 'Edit Data',
                'role' => $user->roles->pluck('name')->first() ?? 'User',
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ])
            ->log('Memperbarui Pengaturan Tautan Eksternal');

        return redirect()->route('admin.external-links.index')->with('success', 'Tautan Eksternal berhasil diperbarui.');
    }
}
