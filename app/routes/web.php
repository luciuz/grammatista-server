<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', static function () {
    return 'Welcome to Grammatista API';
});

Route::get('/user/auth', '\App\Api\Controllers\UserController@actionAuth');

Route::get('/lesson/search', '\App\Api\Controllers\LessonController@actionSearch');
