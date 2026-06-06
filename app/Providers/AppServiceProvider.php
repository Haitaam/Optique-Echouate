<?php

namespace App\Providers;

use App\Models\Notification;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useTailwind();

        if (session()->has('locale')) {
            app()->setLocale(session('locale'));
        }

        View::share('gmapsUrl', 'https://www.google.com/maps/place/Echouate+Optique/@35.3325884,-5.6681835,11.04z/data=!4m22!1m15!4m14!1m6!1m2!1s0xd0b9500169fb38b:0x25b3c78ef7764f04!2zRWNob3VhdGUgT3B0aXF1ZSwg2LHZgtmFIDE1LCDYtNin2LHYuSDYp9io2YYg2KjYt9mI2LfYqSwgQXNpbGFo!2m2!1d-6.0382968!2d35.4645512!1m6!1m2!1s0xd0b9500169fb38b:0x25b3c78ef7764f04!2zRWNob3VhdGUgT3B0aXF1ZSwg2LHZgtmFIDE1LCDYtNin2LHYuSDYp9io2YYg2KjYt9mI2LfYqSwgQXNpbGFo!2m2!1d-6.0382968!2d35.4645512!3m5!1s0xd0b9500169fb38b:0x25b3c78ef7764f04!8m2!3d35.46459!4d-6.0383287!16s%2Fg%2F11ms7gd8p3?entry=ttu&g_ep=EgoyMDI2MDUxMy4wIKXMDSoASAFQAw%3D%3D');

        View::composer('admin.*', function ($view) {
            $view->with('adminNotifications', Notification::unread()->latest()->take(10)->get());
            $view->with('adminNotificationsCount', Notification::unread()->count());
        });
    }
}
