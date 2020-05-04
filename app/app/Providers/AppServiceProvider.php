<?php

namespace App\Providers;

use App\Lib\Vk\VkHelper;
use App\Services\Auth\TokenAuthGuard;
use App\Services\Auth\TokenUserProvider;
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
        $this->app->bind(VkHelper::class, static function ($app) {
           return new VkHelper(config('vk.client_secret'));
        });

        Auth::extend('TokenAuthGuard', static function ($app) {
            return new TokenAuthGuard(
                $app->make(TokenUserProvider::class),
                $app->make('request')
            );
        });
    }
}
