<?php

namespace App\Providers;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('settings', function(){
            return SystemSetting::all()->first();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $settings = SystemSetting::all()->first();
        View::share('settings', $settings);
    }
}
