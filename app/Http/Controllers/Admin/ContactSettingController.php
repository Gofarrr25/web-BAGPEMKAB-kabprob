<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactSettingController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->hasAnyRole(['Admin', 'Admin OPD', 'Superadmin'])) abort(403);
        
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.contact_settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        return $this->update($request);
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->hasAnyRole(['Admin', 'Admin OPD', 'Superadmin'])) abort(403);

        $request->validate([
            'office_address' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|string',
            'facebook_url' => 'nullable|string',
            'twitter_url' => 'nullable|string',
            'instagram_url' => 'nullable|string',
            'tiktok_url' => 'nullable|string',
            'youtube_url' => 'nullable|string',
            'google_maps_iframe' => 'nullable|string',
        ]);

        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? '']
            );
        }

        return redirect()->route('admin.contact-settings.index')->with('success', 'Pengaturan Kontak berhasil diperbarui.');
    }
}
