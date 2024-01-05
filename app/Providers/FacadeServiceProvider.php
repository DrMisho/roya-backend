<?php

namespace App\Providers;

use App\Services\CourseService;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;

class FacadeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind('UserService', function () {
            return new UserService();
        });
        $this->app->bind('CourseService', function () {
            return new CourseService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
