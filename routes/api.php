<?php

use App\Http\Controllers\AdmDivisionController;
use App\Http\Controllers\CharacteristicController;
use App\Http\Controllers\ListController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\TagController;
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

        Route::prefix('statistics')->controller(StatisticsController::class)->group(function () {
            Route::get('/', 'statistics');
        });

        Route::prefix('lists')->controller(ListController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('/sidebar', 'indexSidebar');
            Route::get('/all', 'indexAll');
            Route::post('/', 'store');
            Route::delete('/', 'destroyMultiple');

            Route::prefix('{propertyList}')->middleware('propertylist.owner')->group(function () {
                Route::get('/', 'show');
                Route::put('/', 'update');
                Route::delete('/', 'destroy');
                Route::prefix('properties')->group(function () {
                    Route::post('/', 'addMultipleProperties');
                    Route::delete('/{property}', 'removeProperty')->middleware('property.owner');
                });
            });
        });

        Route::prefix('tags')->controller(TagController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('/sidebar', 'indexSidebar');
            Route::get('/all', 'indexAll');
            Route::post('/', 'create')->middleware('throttle:20,1');
            Route::delete('/{tag}', 'destroy');
            Route::delete('/', 'destroyMultiple');
        });

        Route::prefix('characteristics')->controller(CharacteristicController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('/paginated', 'indexPaginated');
            Route::post('/', 'store');

            Route::delete('/', 'destroyMultiple');

            Route::prefix('{characteristic}')->middleware('characteristic.owner')->group(function () {
                Route::put('/', 'update');
                Route::delete('/', 'destroy');
            });
        });

        Route::prefix('properties')->controller(PropertyController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('/titles', 'indexTitles');
            Route::post('/', 'store')->middleware('throttle:10,1');

            Route::get('/polygon', 'indexPropertiesInPolygon');

            Route::get('/trashed', 'trashed');
            Route::delete('/trashed', 'emptyTrash');
            Route::patch('/trashed/restore', 'restoreAll');

            Route::prefix('{property}')->middleware('property.owner')->group(function () {
                Route::get('/', 'show');
                Route::get('/details', 'showDetails');

                Route::put('/', 'update')->middleware('throttle:10,1');

                Route::put('/tags', 'updateTags')->middleware('throttle:20,1');
                Route::delete('/tags/{tag}', 'removeTag');

                Route::patch('/cover', 'updateCover');
                Route::delete('/cover', 'deleteCover');

                Route::patch('/rating', 'updateRating');

                // Delete and restore
                Route::delete('/', 'destroy');
            });

            Route::prefix('{trashedProperty}')->middleware('property.owner')->group(function () {
                Route::delete('/permanent', 'permanentDestroy');
                Route::patch('/restore', 'restore');
            });
        });

        Route::prefix('offers')->controller(OfferController::class)->group(function () {
            Route::delete('/{offer}', 'destroy');
        });
    });

    Route::prefix('administrative-divisions')->controller(AdmDivisionController::class)->group(function () {
        //Route::get('/', 'index');
        Route::get('/level/{level}', 'level')->where('level', '[0-9]+');
        Route::get('/', 'all');
    });
});
