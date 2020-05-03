<?php

namespace App\Providers;

use App\Lib\VkApi\VkHelper;
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
    }
}
