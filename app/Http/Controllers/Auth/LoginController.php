<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function authenticate(Request $request)
    {
        $throttleKey = Str::transliterate(Str::lower($request->input('username', '')).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            event(new \Illuminate\Auth\Events\Lockout($request));
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->with('error', 'Terlalu banyak percobaan login. Silakan coba lagi dalam '.$seconds.' detik.');
        }

        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
            'captcha' => ['required', 'captcha'],
        ], [
            'captcha.captcha' => 'Kode Captcha yang Anda masukkan tidak sesuai!',
            'captcha.required' => 'Silakan masukkan kode Captcha.',
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.'
        ]);

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            RateLimiter::clear($throttleKey);
            
            // Cek apakah akun aktif
            if (!Auth::user()->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->with('error', 'Akun Anda telah dinonaktifkan oleh Superadmin.');
            }

            $request->session()->regenerate();
            
            return redirect()->intended('/admin/dashboard');
        }

        RateLimiter::hit($throttleKey);
        return back()->with('error', 'Username atau password salah.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }
}
