<?php

use App\Admin\Controllers\CourseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

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
Route::group(['namespace'=>'Api'], function() {
    Route::post('/login', 'UserController@createUser');
    Route::group(['middleware'=>['auth:sanctum']], function() {
        Route::any('/courseList', 'CourseController@courseList');
        Route::any('/courseDetail', 'CourseController@courseDetail');
        Route::any('/lessonList', 'LessonController@lessonList');
        Route::any('/lessonDetail', 'LessonController@lessonDetail');
    });
});

// Route::post('/login', [UserController::class, 'login']);