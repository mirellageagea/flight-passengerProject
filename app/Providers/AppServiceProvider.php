<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use OwenIt\Auditing\Auditor;
use Illuminate\Support\Facades\Auth;


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
        $this->app->singleton(Auditor::class, function ($app) {
            $auditor = new Auditor($app);
            $auditor->resolveUserUsing(function () {
                return Auth::guard('sanctum')->user() ?: Auth::user(); // or just Auth::user()
            });
            return $auditor;
        });
    }
}
