<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\EventsController;
use App\Http\Controllers\API\GroupsController;
use App\Http\Controllers\API\UsersController;
use Illuminate\Support\Facades\Artisan;
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

Route::get('/', function () {
    $clearcache = Artisan::call('cache:clear');
    $clearview = Artisan::call('view:clear');
    $clearconfig = Artisan::call('config:cache');
    return 'Lamp Test API <br/>' . app()->version();
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [UsersController::class, 'store']);
    Route::middleware('jwt.verify')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});



// Route without jwt.verify middleware
Route::group(['prefix' => 'group'], function () {
    Route::get('/', [GroupsController::class, 'get']);
});


Route::middleware('jwt.verify')->group(function () {
    Route::group(['prefix' => 'event'], function () {
        Route::post('/create-event', [EventsController::class, 'store']);
        Route::get('/{id}', [EventsController::class, 'show']);
        Route::put('/{id}', [EventsController::class, 'update']);
        Route::delete('/{id}', [EventsController::class, 'destroy']);
        Route::group(['prefix' => 'detail'], function () {
            Route::get('/{id}', [EventsController::class, 'showDetail']);
        });
    });
    Route::get('/history', [EventsController::class, 'history']);
    Route::group(['prefix' => 'attendence'], function () {
        Route::post('/', [EventsController::class, 'attendence']);
    });
    Route::group(['prefix' => 'users'], function () {
        Route::get('/{id}', [UsersController::class, 'show']);
        Route::put('/{id}', [UsersController::class, 'update']);
        Route::delete('/{id}', [UsersController::class, 'destroy']);
    });
});
