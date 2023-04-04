<?php

use App\Http\Controllers\PropertyController;
use App\Http\Controllers\UserController;
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

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('me')->controller(UserController::class)->group(function () {
        Route::get('/', 'show');
        Route::put('/', 'update');
        Route::patch('/password', 'changePassword');

        Route::prefix('properties')->controller(PropertyController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
        });
    });

    Route::prefix('properties')->controller(PropertyController::class)->group(function () {
        Route::prefix('{property}')->middleware('property.owner')->group(function () {
            Route::get('/', 'show');
            Route::get('/details', 'showDetails');
        });
    });
});
