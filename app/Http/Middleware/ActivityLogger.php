<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Capture user BEFORE request for logout scenarios
        $userBeforeRequest = Auth::user();

        $response = $next($request);

        // Hanya log untuk rute admin atau otentikasi
        if ($request->is('admin*') || $request->is('login') || $request->is('logout')) {
            try {
                $this->logActivity($request, $response, $userBeforeRequest);
            } catch (\Exception $e) {
                // Jangan gagalkan request jika log gagal
                Log::error('Activity Log Error: ' . $e->getMessage());
            }
        }

        return $response;
    }

    /**
     * Memproses log aktivitas ke dalam database.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Symfony\Component\HttpFoundation\Response|mixed $response
     * @param \App\Models\User|null $userBeforeRequest
     * @return void
     */
    protected function logActivity(Request $request, $response, $userBeforeRequest)
    {
        $userAgent = $request->userAgent() ?? '';
        
        // Lightweight Browser Detection
        $browserStr = 'Unknown Browser';
        if (preg_match('/Edg/i', $userAgent)) $browserStr = 'Edge';
        elseif (preg_match('/Firefox/i', $userAgent)) $browserStr = 'Firefox';
        elseif (preg_match('/OPR/i', $userAgent) || preg_match('/Opera/i', $userAgent)) $browserStr = 'Opera';
        elseif (preg_match('/Chrome/i', $userAgent)) $browserStr = 'Chrome';
        elseif (preg_match('/Safari/i', $userAgent)) $browserStr = 'Safari';

        // Lightweight OS Detection
        $osStr = 'Unknown OS';
        if (preg_match('/Windows NT 11/i', $userAgent)) $osStr = 'Windows 11';
        elseif (preg_match('/Windows NT 10/i', $userAgent)) $osStr = 'Windows 10';
        elseif (preg_match('/Mac OS X/i', $userAgent)) $osStr = 'Mac OS';
        elseif (preg_match('/Linux/i', $userAgent)) $osStr = 'Linux';
        elseif (preg_match('/Android/i', $userAgent)) $osStr = 'Android';
        elseif (preg_match('/iPhone|iPad|iPod/i', $userAgent)) $osStr = 'iOS';

        $method = $request->method();
        $url = $request->fullUrl();
        $statusCode = $response->status();
        
        /** @var \App\Models\User|null $user */
        $user = Auth::user() ?? $userBeforeRequest;

        $activityType = 'Melihat Data';
        $description = 'Mengakses halaman ' . $request->path();

        // Coba tentukan modul dari URL
        $segments = $request->segments();
        $module = 'Sistem';
        if (isset($segments[1]) && $request->is('admin*')) {
            $module = ucfirst(str_replace('-', ' ', $segments[1]));
        } elseif ($request->is('login')) {
            $module = 'Authentication';
        } elseif ($request->is('logout')) {
            $module = 'Authentication';
        }

        // Keamanan: Tangkap akses ditolak
        if ($statusCode == 403 || $statusCode == 401) {
            $activityType = 'Akses Ditolak';
            $description = 'Mencoba mengakses fitur/halaman tanpa izin yang cukup pada ' . $request->path();
        } elseif ($statusCode == 404) {
            $activityType = 'Halaman Tidak Ditemukan';
            $description = 'Mencoba mengakses URL yang tidak valid: ' . $request->path();
        } elseif ($statusCode >= 500) {
            if ($request->is('admin/activity-logs*')) return; 
            $activityType = 'Error Server';
            $description = 'Terjadi error internal saat mengakses ' . $request->path();
        } else {
            // Untuk HTTP Success (200-399) atau Redirect (302)
            if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                if (session()->has('errors')) {
                    $activityType = 'Validasi Gagal';
                    $description = 'Gagal menyimpan data pada ' . $request->path() . ' (Kesalahan Validasi).';
                } else {
                    // Skip POST, PUT, PATCH, DELETE yang sukses karena sudah ditangani oleh trait Spatie LogsActivity di setiap Model
                    return;
                }
            } else {
                // Skip pencatatan GET request (Melihat Data, Membuka halaman, dll) sesuai permintaan user
                return;
            }
        }

        // Tentukan Level Aktivitas
        $level = 'INFO';
        if (in_array($statusCode, [401, 403, 404, 500]) || $activityType === 'Login Gagal') {
            $level = 'WARNING';
        }
        
        // Cek Critical: Hapus user, ubah permission, ubah setting
        if ($method === 'DELETE' && $module === 'Users') {
            $level = 'CRITICAL';
        } elseif (($method === 'PUT' || $method === 'PATCH' || $method === 'POST') && in_array($module, ['Settings', 'Roles', 'Permissions'])) {
            $level = 'CRITICAL';
        }

        // Eksekusi Log menggunakan Spatie
        $log = activity('admin_log');
        
        if ($user) {
            $log->causedBy($user);
            $role = $user->roles->pluck('name')->first() ?? 'User';
            $username = $user->name;
        } else {
            $role = 'Guest';
            $username = 'Guest / Unauthenticated';
        }

        // Bersihkan password atau data sensitif dari request payload
        $payload = $request->except([
            'password', 
            'password_confirmation', 
            'current_password', 
            'new_password', 
            'new_password_confirmation',
            '_token', 
            '_method',
            'captcha'
        ]);

        $log->withProperties([
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'browser' => $browserStr,
            'os' => $osStr,
            'method' => $method,
            'url' => $url,
            'status' => $statusCode,
            'module' => $module,
            'role' => $role,
            'username' => $username,
            'type' => $activityType,
            'level' => $level,
            'payload' => $payload, // Data Target/Request
        ])->log($description);
    }
}
