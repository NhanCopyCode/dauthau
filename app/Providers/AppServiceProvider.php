<?php

namespace App\Providers;

use App\Services\Category\Strategies\WorkTypeStrategy;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(\App\Services\CategoryService::class, function ($app) {
            return new \App\Services\CategoryService([
                new WorkTypeStrategy(),
            ]);
        });
    }
     
    public function boot(): void
    {
        //
    }
}
