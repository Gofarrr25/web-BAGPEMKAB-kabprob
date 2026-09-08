<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class OrganizationMemberController extends Controller
{
    public function index()
    {
        $photo = Setting::where('key', 'org_structure_photo')->value('value');
        return view('admin.organization_members.index', compact('photo'));
    }

    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'image.mimes' => '⚠️ Format file tidak sesuai. Field ini hanya menerima JPG, JPEG, PNG, atau WEBP.',
            'image.image' => 'File harus berupa gambar.',
        ]);

        $filePath = null;
        if ($request->filled('cropped_image')) {
            $image_parts = explode(";base64,", $request->cropped_image);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            
            $fileName = 'settings/org_' . time() . '.' . $image_type;
            Storage::disk('public')->put($fileName, $image_base64);
            $filePath = $fileName;
        } elseif ($request->hasFile('image')) {
            $filePath = $request->file('image')->store('settings', 'public');
        }

        if ($filePath) {
            $setting = Setting::firstOrCreate(['key' => 'org_structure_photo']);
            // Delete old photo if exists
            if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                Storage::disk('public')->delete($setting->value);
            }
            
            $setting->update(['value' => $filePath]);
            
            /** @var \App\Models\User $user */
            $user = Auth::user();

            activity('admin_log')
                ->causedBy($user)
                ->withProperties([
                    'module' => 'Struktur Organisasi',
                    'type' => 'Tambah Data',
                    'role' => $user->roles->pluck('name')->first() ?? 'User',
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ])
                ->log('Mengunggah foto Struktur Organisasi baru');
        }

        return redirect()->route('admin.organization-members.index')->with('success', 'Gambar Struktur Organisasi berhasil diperbarui.');
    }

    public function deletePhoto()
    {
        $setting = Setting::where('key', 'org_structure_photo')->first();
        if ($setting && $setting->value) {
            Storage::disk('public')->delete($setting->value);
            $setting->update(['value' => null]);
            
            /** @var \App\Models\User $user */
            $user = Auth::user();

            activity('admin_log')
                ->causedBy($user)
                ->withProperties([
                    'module' => 'Struktur Organisasi',
                    'type' => 'Hapus Data',
                    'role' => $user->roles->pluck('name')->first() ?? 'User',
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ])
                ->log('Menghapus foto Struktur Organisasi');
        }

        return redirect()->route('admin.organization-members.index')->with('success', 'Gambar Struktur Organisasi berhasil dihapus.');
    }
}
