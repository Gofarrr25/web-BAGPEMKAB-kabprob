<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Spatie\Activitylog\Facades\Activity;

class LogAuthenticationActivity
{
    public function handleLogin(Login $event)
    {
        $role = $event->user->roles->pluck('name')->first() ?? 'User';
        
        activity('Authentication')
            ->causedBy($event->user)
            ->withProperties([
                'module' => 'Authentication',
                'type' => 'Login Berhasil',
                'role' => $role,
                'status' => 200,
                'level' => 'INFO'
            ])
            ->log('User berhasil login');
    }

    public function handleLogout(Logout $event)
    {
        if ($event->user) {
            $role = $event->user->roles->pluck('name')->first() ?? 'User';
            
            activity('Authentication')
                ->causedBy($event->user)
                ->withProperties([
                    'module' => 'Authentication',
                    'type' => 'Logout',
                    'role' => $role,
                    'status' => 200,
                    'level' => 'INFO'
                ])
                ->log('User logout dari sistem');
        }
    }

    public function handleFailed(\Illuminate\Auth\Events\Failed $event)
    {
        $username = $event->credentials['username'] ?? ($event->credentials['email'] ?? 'Unknown');
        
        activity('Authentication')
            ->withProperties([
                'module' => 'Authentication',
                'type' => 'Login Gagal',
                'role' => 'Guest',
                'username' => $username,
                'ip' => request()->ip(),
                'browser' => request()->userAgent() ?? 'Unknown',
                'os' => 'Unknown',
                'status' => 401,
                'level' => 'WARNING',
            ])
            ->log('Percobaan login gagal untuk akun: ' . $username);
    }

    public function handleLockout(\Illuminate\Auth\Events\Lockout $event)
    {
        $username = $event->request->input('username') ?? 'Unknown';

        activity('Authentication')
            ->withProperties([
                'module' => 'Authentication',
                'type' => 'Login Rate Limit',
                'role' => 'Guest',
                'username' => $username,
                'ip' => $event->request->ip(),
                'browser' => $event->request->userAgent() ?? 'Unknown',
                'os' => 'Unknown',
                'status' => 429,
                'level' => 'WARNING',
            ])
            ->log('Percobaan login berulang terdeteksi dan request dibatasi sementara.');
    }
}
