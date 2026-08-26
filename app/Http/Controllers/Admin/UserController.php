<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->hasRole('Superadmin')) abort(403);

        $users = User::with('roles')->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->hasRole('Superadmin')) abort(403);
        
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
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
        
        $min = min($width, $height);
        $x = ($width - $min) / 2;
        $y = ($height - $min) / 2;
        
        $dst = imagecreatetruecolor(500, 500);
        if ($mime == 'image/png' || $mime == 'image/webp') {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
            imagefilledrectangle($dst, 0, 0, 500, 500, $transparent);
        }
        
        imagecopyresampled($dst, $src, 0, 0, $x, $y, 500, 500, $min, $min);
        imagejpeg($dst, $path, 90);
        
        imagedestroy($src);
        imagedestroy($dst);
        
        return 'profile_photos/' . $filename;
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->hasRole('Superadmin')) abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|string|exists:roles,name',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
        ], [
            'profile_photo.mimes' => 'Format file tidak valid. Gunakan JPG, PNG, GIF, atau WebP.',
        ]);

        $photoPath = null;
        if ($request->filled('cropped_photo')) {
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
                $photoPath = 'profile_photos/' . $filename;
            }
        } elseif ($request->hasFile('profile_photo')) {
            $photoPath = $this->processProfilePhoto($request->file('profile_photo'), $request->username);
        }

        $newUser = User::create([
            'name' => trim($request->name),
            'username' => trim($request->username),
            'email' => trim($request->email),
            'password' => Hash::make($request->password),
            'is_active' => true,
            'profile_photo_path' => $photoPath,
        ]);

        $newUser->assignRole($request->role);

        return redirect()->route('admin.users.index')->with('success', 'Admin baru berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();
        if (!$authUser || !$authUser->hasRole('Superadmin')) abort(403);

        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        Log::info('--- PROFILE PHOTO UPDATE DEBUG START ---');
        Log::info('Request Method: ' . $request->method());
        Log::info('Has cropped_photo: ' . ($request->filled('cropped_photo') ? 'YES' : 'NO'));
        if ($request->filled('cropped_photo')) {
            Log::info('Length of cropped_photo: ' . strlen($request->input('cropped_photo')));
        }
        Log::info('Has profile_photo: ' . ($request->hasFile('profile_photo') ? 'YES' : 'NO'));
        
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();
        if (!$authUser || !$authUser->hasRole('Superadmin')) {
            Log::error('Auth failed or not Superadmin');
            abort(403);
        }

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users,username,' . $user->id,
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
                'role' => 'required|string|exists:roles,name',
                'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
            ], [
                'profile_photo.mimes' => 'Format file tidak valid. Gunakan JPG, PNG, GIF, atau WebP.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Failed: ', $e->errors());
            throw $e;
        }

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
            
            activity('admin_log')
                ->causedBy($authUser)
                ->performedOn($user)
                ->withProperties([
                    'module' => 'Users',
                    'type' => 'Perubahan Password',
                    'role' => $authUser->roles->pluck('name')->first() ?? 'User',
                    'username' => $authUser->name,
                    'ip' => $request->ip(),
                    'browser' => $request->userAgent() ?? 'Unknown',
                    'os' => 'Unknown',
                    'status' => 200,
                    'level' => 'WARNING',
                ])
                ->log('Superadmin mengubah password untuk akun: ' . $user->username);
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
                $bytes = file_put_contents($path, $imageBase64);
                Log::info('Bytes written: ' . $bytes);
                Log::info('Path saved: ' . $path);
                $data['profile_photo_path'] = 'profile_photos/' . $filename;
            } else {
                Log::error('Failed to explode base64');
            }
        } elseif ($request->hasFile('profile_photo')) {
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
        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users.index')->with('success', 'Data admin berhasil diperbarui.');
    }

    public function toggleActive(User $user)
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();
        if (!$authUser || !$authUser->hasRole('Superadmin')) abort(403);
        
        if ($user->id === $authUser->id) {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Status akun berhasil diubah.');
    }

    public function destroy(User $user)
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();
        if (!$authUser || !$authUser->hasRole('Superadmin')) abort(403);
        
        if ($user->id === $authUser->id) {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }
        
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Akun admin berhasil dihapus.');
    }
}
