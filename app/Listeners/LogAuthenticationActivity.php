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
}
