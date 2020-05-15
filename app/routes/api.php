<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/user/auth', '\App\Api\Controllers\UserController@actionAuth');

Route::middleware('auth:api2')->group(static function () {
    Route::post('/lesson/get', '\App\Api\Controllers\LessonController@actionGet');
    Route::post('/lesson/search', '\App\Api\Controllers\LessonController@actionSearch');

    Route::post('/variant/create', '\App\Api\Controllers\VariantController@actionCreate');
    Route::post('/variant/get', '\App\Api\Controllers\VariantController@actionGet');
    Route::post('/variant/finish', '\App\Api\Controllers\VariantController@actionFinish');

    Route::post('/bookmark/set', '\App\Api\Controllers\BookmarkController@actionSet');
    Route::post('/bookmark/delete', '\App\Api\Controllers\BookmarkController@actionDelete');
});
