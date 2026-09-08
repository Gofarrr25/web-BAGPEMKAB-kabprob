<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Setting;
use App\Models\Page;
use App\Models\Menu;

use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Listeners\LogAuthenticationActivity;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Auth Logging Events
        Event::listen(Login::class, [LogAuthenticationActivity::class, 'handleLogin']);
        Event::listen(Logout::class, [LogAuthenticationActivity::class, 'handleLogout']);
        Event::listen(\Illuminate\Auth\Events\Failed::class, [LogAuthenticationActivity::class, 'handleFailed']);
        Event::listen(\Illuminate\Auth\Events\Lockout::class, [LogAuthenticationActivity::class, 'handleLockout']);

        // Global Strict Password Policy: min 8 chars, mixed case, numbers, symbols
        Password::defaults(function () {
            return Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols();
        });

        View::composer('*', function ($view) {
            $cachedSettings = \Illuminate\Support\Facades\Cache::remember('site_settings', 3600, function () {
                try {
                    return Setting::all()->pluck('value', 'key')->toArray();
                } catch (\Throwable $e) {
                    return [];
                }
            });

            $headerNavMenus = \Illuminate\Support\Facades\Cache::remember('header_nav_menus', 3600, function () {
                try {
                    return Menu::whereNull('parent_id')
                        ->where('is_active', true)
                        ->with([
                            'page',
                            'children' => function($q) {
                                $q->where('is_active', true)->orderBy('order_index');
                            },
                            'children.page',
                            'children.parent',
                            'children.children' => function($q) {
                                $q->where('is_active', true)->orderBy('order_index');
                            },
                            'children.children.page',
                            'children.children.parent'
                        ])
                        ->orderBy('order_index')
                        ->get()
                        ->toArray();
                } catch (\Throwable $e) {
                    return [];
                }
            });

            $view->with('siteSettings', $cachedSettings);
            $view->with('headerNavMenus', json_decode(json_encode($headerNavMenus)));
        });
    }
}
