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

            $headerNavMenus = Menu::whereNull('parent_id')
                ->where('is_active', true)
                ->where(function($q) {
                    $q->where('position', 'navbar')->orWhereNull('position');
                })
                ->with([
                    'children' => function($q) {
                        $q->where('is_active', true)->orderBy('order_index');
                    },
                    'children.children' => function($q) {
                        $q->where('is_active', true)->orderBy('order_index');
                    }
                ])
                ->orderBy('order_index')
                ->get();

            $footerNavMenus = Menu::whereNull('parent_id')
                ->where('is_active', true)
                ->where('position', 'footer')
                ->with([
                    'children' => function($q) {
                        $q->where('is_active', true)->orderBy('order_index');
                    }
                ])
                ->orderBy('order_index')
                ->get();

            $view->with('siteSettings', $cachedSettings);
            $view->with('headerNavMenus', $headerNavMenus);
            $view->with('footerNavMenus', $footerNavMenus);
        });
    }
}
