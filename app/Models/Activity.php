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

            // Parse User Agent properly using Jenssegers/Agent
            if (!isset($properties['browser']) || !isset($properties['os'])) {
                $agent = new \Jenssegers\Agent\Agent();
                $userAgent = $properties['user_agent'] ?? Request::userAgent();
                $agent->setUserAgent($userAgent);
                
                $browser = $agent->browser();
                $version = $agent->version($browser);
                $os = $agent->platform();
                $osVersion = $agent->version($os);
                
                $properties['browser'] = $browser ? $browser . ' ' . $version : 'Unknown Browser';
                $properties['os'] = $os ? $os . ' ' . $osVersion : 'Unknown OS';
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
