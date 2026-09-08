<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('admin.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        Log::info('--- PROFILE EDIT UPDATE ---');
        Log::info('Has cropped_photo: ' . ($request->filled('cropped_photo') ? 'YES' : 'NO'));
        
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'profile_photo' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'profile_photo.mimes' => '⚠️ Format file tidak sesuai. Field ini hanya menerima JPG, JPEG, PNG, atau WEBP.',
        ]);

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
            
            activity('admin_log')
                ->causedBy($user)
                ->performedOn($user)
                ->withProperties([
                    'module' => 'Users',
                    'type' => 'Perubahan Password',
                    'role' => $user->roles->pluck('name')->first() ?? 'User',
                    'username' => $user->name,
                    'ip' => $request->ip(),
                    'browser' => $request->userAgent() ?? 'Unknown',
                    'os' => 'Unknown',
                    'status' => 200,
                    'level' => 'INFO',
                ])
                ->log('User mengubah password profilnya sendiri');
        }

        if ($request->filled('cropped_photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $base64Image = $request->input('cropped_photo');
            $imageParts = explode(";base64,", $base64Image);
            if (count($imageParts) == 2) {
                $imageBase64 = base64_decode($imageParts[1]);
                $filename = 'profile_' . $request->username . '_' . time() . '.jpg';
                $path = storage_path('app/public/profile_photos/' . $filename);
                if (!file_exists(storage_path('app/public/profile_photos'))) {
                    mkdir(storage_path('app/public/profile_photos'), 0755, true);
                }
                file_put_contents($path, $imageBase64);
                $data['profile_photo_path'] = 'profile_photos/' . $filename;
            }
        } elseif ($request->hasFile('profile_photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $data['profile_photo_path'] = $this->processProfilePhoto($request->file('profile_photo'), $request->username);
        }

        $user->update($data);

        return redirect()->route('admin.profile.edit')->with('success', 'Profil Anda berhasil diperbarui.');
    }

    private function processProfilePhoto(\Illuminate\Http\UploadedFile $file, string $username): ?string
    {
        $filename = 'profile_' . $username . '_' . time() . '.jpg';
        $path = storage_path('app/public/profile_photos/' . $filename);
        
        if (!file_exists(storage_path('app/public/profile_photos'))) {
            mkdir(storage_path('app/public/profile_photos'), 0755, true);
        }
        
        $info = getimagesize($file->getRealPath());
        if (!$info) return null;
        
        $mime = $info['mime'];
        switch ($mime) {
            case 'image/jpeg': $src = imagecreatefromjpeg($file->getRealPath()); break;
            case 'image/png': $src = imagecreatefrompng($file->getRealPath()); break;
            case 'image/webp': $src = imagecreatefromwebp($file->getRealPath()); break;
            default: return null;
        }
        
        if (!$src) return null;
        
        $width = imagesx($src);
        $height = imagesy($src);
        $size = min($width, $height);
        
        $dst = imagecreatetruecolor(500, 500);
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefill($dst, 0, 0, $white);
        
        imagecopyresampled(
            $dst, $src,
            0, 0,
            ($width - $size) / 2, ($height - $size) / 2,
            500, 500,
            $size, $size
        );
        
        imagejpeg($dst, $path, 90);
        
        imagedestroy($src);
        imagedestroy($dst);
        
        return 'profile_photos/' . $filename;
    }
}
