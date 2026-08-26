<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class SurveySettingController extends Controller
{
    public function index()
    {
        $settings = Setting::whereIn('key', ['survey_title', 'survey_link', 'survey_qr_image'])
            ->pluck('value', 'key');
            
        return view('admin.survey_settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'survey_title' => 'nullable|string',
            'survey_link' => 'nullable|string',
            'survey_qr_image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
        ], [
            'survey_qr_image.mimes' => 'Format file tidak valid. Gunakan JPG, PNG, GIF, atau WebP.',
        ]);

        if ($request->hasFile('survey_qr_image')) {
            $file = $request->file('survey_qr_image');
            $targetDir = storage_path('app/public/settings');
            
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            
            $filename = 'survey_qr_image_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($targetDir, $filename);
            
            Setting::updateOrCreate(
                ['key' => 'survey_qr_image'],
                ['value' => 'settings/' . $filename]
            );
        }

        $data = $request->only(['survey_title', 'survey_link']);

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
                'module' => 'Link Survey / QR SKM',
                'type' => 'Edit Data',
                'role' => $user->roles->pluck('name')->first() ?? 'User',
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ])
            ->log('Memperbarui Link Survey / QR SKM');

        return redirect()->route('admin.survey-settings.index')->with('success', 'Link Survey / QR SKM berhasil diperbarui.');
    }
}
