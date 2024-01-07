<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\VideoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'guest'], function () {
    Route::post('/sign-up', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/tokens/refresh', [AuthController::class, 'refresh']);
});


Route::group(['middleware' => 'auth'], function() {

    Route::group(['prefix' => 'users'], function() {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::post('/{id}/edit-profile', [AuthController::class, 'update']);
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::group(['prefix' => 'courses'], function () {
        Route::get('/', [CourseController::class, 'index']);
        Route::get('/{course}', [CourseController::class, 'show']);
    });

    Route::group(['prefix' => 'lessons'], function () {
        Route::get('/{lesson}', [LessonController::class, 'show']);
        Route::get('/courses/{course}', [LessonController::class, 'index']);
    });

    Route::group(['prefix' => 'questions'], function () {
        Route::get('/{question}', [QuestionController::class, 'show']);
        Route::get('/courses/{course}', [QuestionController::class, 'index']);
    });

    Route::group(['prefix' => 'quizzes'], function () {
        Route::get('/courses/{course}', [QuizController::class, 'index']);
        Route::get('/{quiz}', [QuizController::class, 'show']);
    });

    Route::group(['prefix' => 'videos'], function () {
        Route::get('/courses/{course}', [VideoController::class, 'index']);
        Route::get('/{video}', [VideoController::class, 'show']);
    });

    Route::group(['prefix' => 'subscriptions'], function () {
        Route::post('/', [SubscriptionController::class, 'store']);
        Route::get('/my-subscriptions', [SubscriptionController::class, 'mySubscription']);
        Route::get('/{subscription}', [SubscriptionController::class, 'show']);
    });

});

Route::group(['prefix' => 'admin', 'middleware' => 'role:super-admin'], function () {

    Route::group(['prefix' => 'courses'], function() {
        Route::post('/', [CourseController::class, 'store']);
        Route::put('/{course}', [CourseController::class, 'update']);
        Route::delete('/{course}', [CourseController::class, 'destroy']);
    });

    Route::group(['prefix' => 'lessons'], function() {
        Route::post('/', [LessonController::class, 'store']);
        Route::put('/{lesson}', [LessonController::class, 'update']);
        Route::delete('/{lesson}', [LessonController::class, 'destroy']);
    });

    Route::group(['prefix' => 'questions'], function() {
        Route::post('/', [QuestionController::class, 'store']);
        Route::put('/{question}', [QuestionController::class, 'update']);
        Route::delete('/{question}', [QuestionController::class, 'destroy']);
    });

    Route::group(['prefix' => 'quizzes'], function() {
        Route::post('/', [QuizController::class, 'store']);
        Route::put('/{quiz}', [QuizController::class, 'update']);
        Route::delete('/{quiz}', [QuizController::class, 'destroy']);
    });

    Route::group(['prefix' => 'videos'], function() {
        Route::post('/', [VideoController::class, 'store']);
        Route::put('/{video}', [VideoController::class, 'update']);
        Route::delete('/{video}', [VideoController::class, 'destroy']);
    });

    Route::group(['prefix' => 'subscriptions'], function () {
        Route::get('/', [SubscriptionController::class, 'index']);
        Route::post('/{subscription}/change-status', [SubscriptionController::class, 'changeStatus']);
    });
});
