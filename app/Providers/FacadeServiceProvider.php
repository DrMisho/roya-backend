<?php

namespace App\Providers;

use App\Services\CourseService;
use App\Services\LessonService;
use App\Services\QuestionService;
use App\Services\QuizService;
use App\Services\SubscriptionService;
use App\Services\UserService;
use App\Services\VideoService;
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
        $this->app->bind('LessonService', function () {
            return new LessonService();
        });
        $this->app->bind('QuestionService', function () {
            return new QuestionService();
        });
        $this->app->bind('QuizService', function () {
            return new QuizService();
        });
        $this->app->bind('VideoService', function () {
            return new VideoService();
        });
        $this->app->bind('SubscriptionService', function () {
            return new SubscriptionService();
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
