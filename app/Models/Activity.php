<?php

namespace App\Models;

use Illuminate\Support\Facades\Request;
use Spatie\Activitylog\Models\Activity as SpatieActivity;

class Activity extends SpatieActivity
{
    /**
     * Override boot to automatically inject properties on all logs before saving.
     */
    protected static function booted()
    {
        static::creating(function ($activity) {
            $properties = $activity->properties ?? collect();
            
            if (!isset($properties['ip'])) {
                $properties['ip'] = Request::ip();
            }
            if (!isset($properties['user_agent'])) {
                $properties['user_agent'] = Request::userAgent();
            }
            if (!isset($properties['url'])) {
                $properties['url'] = Request::fullUrl();
            }
            if (!isset($properties['method'])) {
                $properties['method'] = Request::method();
            }

            // Parse User Agent properly using lightweight regex to prevent performance bottlenecks
            if (!isset($properties['browser']) || !isset($properties['os'])) {
                $userAgent = $properties['user_agent'] ?? Request::userAgent() ?? '';
                
                // Lightweight Browser Detection
                $browser = 'Unknown Browser';
                if (preg_match('/Edg/i', $userAgent)) $browser = 'Edge';
                elseif (preg_match('/Firefox/i', $userAgent)) $browser = 'Firefox';
                elseif (preg_match('/OPR/i', $userAgent) || preg_match('/Opera/i', $userAgent)) $browser = 'Opera';
                elseif (preg_match('/Chrome/i', $userAgent)) $browser = 'Chrome';
                elseif (preg_match('/Safari/i', $userAgent)) $browser = 'Safari';

                // Lightweight OS Detection
                $os = 'Unknown OS';
                if (preg_match('/Windows NT 11/i', $userAgent)) $os = 'Windows 11';
                elseif (preg_match('/Windows NT 10/i', $userAgent)) $os = 'Windows 10';
                elseif (preg_match('/Mac OS X/i', $userAgent)) $os = 'Mac OS';
                elseif (preg_match('/Linux/i', $userAgent)) $os = 'Linux';
                elseif (preg_match('/Android/i', $userAgent)) $os = 'Android';
                elseif (preg_match('/iPhone|iPad|iPod/i', $userAgent)) $os = 'iOS';

                $properties['browser'] = $browser;
                $properties['os'] = $os;
            }

            // Populate role
            if (!isset($properties['role'])) {
                $user = \Illuminate\Support\Facades\Auth::user();
                $properties['role'] = $user ? ($user->roles->pluck('name')->first() ?? 'User') : 'Guest';
            }

            // Map Spatie model events to friendly types and modules
            if (!isset($properties['type']) && $activity->event) {
                $eventMap = [
                    'created' => 'Tambah Data',
                    'updated' => 'Edit Data',
                    'deleted' => 'Hapus Data',
                ];
                $properties['type'] = $eventMap[$activity->event] ?? ucfirst($activity->event);
            }

            if (!isset($properties['module']) && $activity->subject_type) {
                $className = class_basename($activity->subject_type);
                // Simple pluralization for module names (e.g. Post -> Posts, Gallery -> Galleries)
                if (substr($className, -1) === 'y') {
                    $module = substr($className, 0, -1) . 'ies';
                } else {
                    $module = $className . 's';
                }
                $properties['module'] = $module;

                // Enhance default english description based on EVENT, not old description
                if (in_array($activity->event, ['created', 'updated', 'deleted'])) {
                    $verbMap = [
                        'created' => 'Menambahkan data baru pada',
                        'updated' => 'Mengubah data pada',
                        'deleted' => 'Menghapus data pada',
                    ];
                    $activity->description = $verbMap[$activity->event] . ' modul ' . $module;
                }
            }

            $activity->properties = $properties;
        });
    }
}
