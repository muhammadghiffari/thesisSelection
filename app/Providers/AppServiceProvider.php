<?php

namespace App\Providers;

use Illuminate\Contracts\Http\Kernel;
use App\Http\Middleware\LogRequests;
use Illuminate\Support\ServiceProvider;

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
        $kernel = app(Kernel::class);
        $kernel->pushMiddleware(LogRequests::class);
    }
}
