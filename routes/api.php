<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\SensorRecordController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\UserSiteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); */

Route::prefix('auth')
        ->as('auth.')
        ->group(function(){
            Route::post('login', [AuthController::class, 'login'])->name('login');
            Route::post('register', [AuthController::class, 'register'])->name('register');
            Route::post('init-password-by-email', [AuthController::class, 'initPasswordByEmail'])
                    ->name('init_by_email');
            Route::post('update', [AuthController::class, 'update'])
                    ->name('update')
                    ->middleware('auth:sanctum');
            Route::post('change-password-by-user-id', [AuthController::class, 'changePassword'])
                    ->name('change')
                    ->middleware('auth:sanctum');
            Route::post('init-password-by-user-id', [AuthController::class, 'initPassword'])
                    ->name('init')
                    ->middleware('auth:sanctum');
            Route::get('login-with-token', [AuthController::class, 'loginWithToken'])
                    ->name('login_with_token')
                    ->middleware('auth:sanctum');
            Route::get('logout', [AuthController::class, 'logout'])
                    ->name('logout')
                    ->middleware('auth:sanctum');
            Route::get('get-list-user-for-auth-user', [AuthController::class, 'getListUserForAuthUser'])
                    ->middleware('auth:sanctum');
        });

Route::middleware('auth:sanctum')->prefix('sensor')
        ->as('sensor.')
        ->group(function(){
            Route::post('store', [SensorController::class, 'store']);
            Route::post('update', [SensorController::class, 'update']);
            Route::post('delete', [SensorController::class, 'delete']);
            Route::post('get-list-sensor-with-last-record-by-site-id',
                [SensorController::class, 'findListSensorsWithLastRecord']);
            Route::post('get-list-sensors-by-site-id',
                [SensorController::class, 'findListSensorsBySiteId']);
            Route::post('get-list-actif-sensors-by-site-id',
                [SensorController::class, 'findActifSensors']);
            Route::get('get-all-sensor', [SensorController::class, 'index']);
            Route::post('add-notification', [SensorController::class, 'addNotification']);
            Route::get('get-notifications', [SensorController::class, 'getNotification']);

        })->middleware('auth:sanctum');


Route::middleware('auth:sanctum')->prefix('sensor-record')
        ->as('sensor_record.')
        ->group(function(){
            Route::post('store', [SensorRecordController::class, 'storeSensorRecord']);
            Route::post('find-by-sensor-id-and-period',
                            [SensorRecordController::class, 'findListRecordBySensorIdAndPeriod']);
            Route::post('delete', [SensorRecordController::class, 'deleteSensorRecord']);

        });

Route::middleware('auth:sanctum')->prefix('site')
        ->as('site.')
        ->group(function(){
            Route::post('store', [SiteController::class, 'store']);
            Route::post('update', [SiteController::class, 'update']);
            Route::post('delete', [SiteController::class, 'delete']);
            Route::get('get-list-sites-for-user', [SiteController::class, 'getListSitesForUser']);
            Route::post('get-list-sites-by-user-id', [SiteController::class, 'getListSitesByUserId']);
            Route::post('get-list-sites-by-role-name', [SiteController::class, 'getListSitesByRoleName']);
            Route::post('add-traker-color', [SiteController::class, 'addTrakerColor']);
            Route::get('get-traker-color-by-site-id/{siteId}', [SiteController::class, 'getTrakerColorBySiteId']);
        });

Route::middleware('auth:sanctum')->prefix('user-site')
        ->as('user_site.')
        ->group(function(){
            Route::post('store', [UserSiteController::class, 'storeUserSite']);
            Route::post('get-user-site-by-user-id', [UserSiteController::class, 'getUserSiteByUserId']);
        });

Route::middleware('auth:sanctum')->prefix('role')


        ->as('role.')
        ->group(function(){
            Route::get('get-list-role-auth-user', [RoleController::class, 'getListRoleForAuthUser']);
            Route::post('assign-role-to-user', [RoleController::class, 'assignRoleToUser']);
        });





