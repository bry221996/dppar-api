<?php

use App\Http\Controllers\Personnel\AuthController as PersonnelAuthController;
use App\Http\Controllers\Personnel\CheckinController as PersonnelCheckinController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\NewPasswordController;
use App\Http\Controllers\Admin\PasswordResetController;
use App\Http\Controllers\Admin\PersonnelController;
use App\Http\Controllers\Admin\StationController;
use App\Http\Controllers\Admin\SubUnitController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Personnel\PersonnelDashboardController;
use App\Http\Controllers\Personnel\PersonnelMpinController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1'], function () {
    Route::group(['prefix' => 'personnel'], function () {
        Route::post('login', [PersonnelAuthController::class, 'login']);

        Route::group(['middleware' => 'auth:personnels'], function () {
            Route::post('/logout', [PersonnelAuthController::class, 'logout']);

            Route::get('/dashboard', [PersonnelDashboardController::class, 'index']);
            Route::get('/details', [PersonnelAuthController::class, 'details']);
            Route::post('/mpin', [PersonnelMpinController::class, 'store']);

            Route::get('/checkins', [PersonnelCheckinController::class, 'index']);
            Route::post('/checkins', [PersonnelCheckinController::class, 'store']);
        });
    });

    Route::group(['prefix' => 'admin'], function () {
        Route::post('login', [AdminAuthController::class, 'login']);
        Route::post('forgot-password', [PasswordResetController::class, 'store']);
        Route::post('reset-password', [NewPasswordController::class, 'store']);

        Route::group(['middleware' => 'auth:admins'], function () {
            Route::get('me', [AdminAuthController::class, 'getMe']);
            Route::post('logout', [AdminAuthController::class, 'logout']);

            Route::group(['middleware' => 'role:super_admin'], function () {
                Route::get('/units', [UnitController::class, 'index']);
                Route::get('/sub-units', [SubUnitController::class, 'index']);
                Route::get('/stations', [StationController::class, 'index']);

                Route::get('/users', [UserController::class, 'index']);
                Route::post('/users', [UserController::class, 'store']);
                Route::put('/users/{id}', [UserController::class, 'update']);
                Route::delete('/users/{id}', [UserController::class, 'destroy']);

                Route::get('/personnels', [PersonnelController::class, 'index']);
            });
        });
    });
});
