<?php

namespace App\Providers;

use App\Service\CSContainersService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
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
        Schema::defaultStringLength(191);

        //Apparemment il arrive à construire le __construct tout seul
        $this->app->singleton(CSContainersService::class/*, function ($app) {
            return new CSContainersService($app->make(Http::class));
        }*/);
    }
}
