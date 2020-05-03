<?php

namespace App\Providers;

use App\Services\Auth\TokenAuthGuard;
use App\Services\Auth\TokenUserProvider;
use App\Services\Vk\VkService;
use Illuminate\Auth\TokenGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(VkService::class, static function ($app) {
           return new VkService(config('vk.client_secret'));
        });

        Auth::extend('TokenAuthGuard', static function ($app) {
            return new TokenAuthGuard(
                $app->make(TokenUserProvider::class),
                $app->make('request')
            );
        });
    }
}
