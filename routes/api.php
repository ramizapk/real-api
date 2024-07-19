<?php

use App\Http\Controllers\Admin\AdController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CityDetailController;
use App\Http\Controllers\Admin\OffersController;
use App\Http\Controllers\Admin\PropertyTypeController;
use App\Http\Controllers\Admin\PropertyTypeDetailController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\PropertyController as UserPropertyController;
use App\Http\Controllers\CityController as UserCityController;
use App\Http\Controllers\OffersController as UserOffersController;
use App\Http\Controllers\PropertyTypeController as UserPropertyTypeController;
use App\Http\Controllers\CityDetailController as UserCityDetailController;
use App\Http\Controllers\PropertyTypeDetailController as UserPropertyTypeDetailController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\RatingController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserAdController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|----------------------------------------
| API v1 Routes
|----------------------------------------
*/
Route::prefix('v1')->group(function () {

    /*
|----------------------------------------
| User API Routes
|----------------------------------------
*/
    Route::get('/properties', [UserPropertyController::class, 'index']);
    Route::get('/properties/{id}', [UserPropertyController::class, 'show']);
    Route::get('/cities', [UserCityController::class, 'index']);
    Route::get('/types', [UserPropertyTypeController::class, 'index']);

    Route::get('cities-details', [UserCityDetailController::class, 'index']);
    Route::get('types-details', [UserPropertyTypeDetailController::class, 'index']);
    Route::get('/ads', [UserAdController::class, 'index']);
    Route::get('/offers', [UserOffersController::class, 'propertiesWithActiveOffers']);
    Route::post('login', [AuthController::class, 'sendVerificationCode']);
    Route::post('verify', [AuthController::class, 'verify']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/profile/update', [ProfileController::class, 'update']);
        Route::prefix('favorites')->group(function () {
            Route::get('/my', [FavoriteController::class, 'index']);
            Route::post('/add', [FavoriteController::class, 'store']);
            Route::delete('/delete/{id}', [FavoriteController::class, 'destroy']);

        });

        Route::post('/ratings', [RatingController::class, 'store']);
        Route::post('/reviews/{id}', [ReviewController::class, 'store']);
        Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);


        Route::get('/user', function (Request $request) {
            return $request->user();
        });
    });



    /*
    |----------------------------------------
    | Admin API Routes
    |----------------------------------------
    */

    Route::prefix('admin')->group(function () {
        Route::post('login', [AdminAuthController::class, 'login']);
        Route::post('register', [AdminAuthController::class, 'register']);

        Route::middleware(['auth:sanctum', 'ensureAdmin'])->group(function () {
            Route::apiResource('property-types', PropertyTypeController::class);
            Route::apiResource('cities', CityController::class);
            Route::apiResource('properties', PropertyController::class);
            Route::post('/properties/{id}/update-request-status', [PropertyController::class, 'updateRequestStatus']);
            Route::post('/properties/{id}/update-availability-status', [PropertyController::class, 'updateAvailabilityStatus']);
            Route::post('properties/update/{property}', [PropertyController::class, 'update']);
            // Pools endpoints
            Route::get('properties/{property}/pools', [PropertyController::class, 'showPools']);
            Route::post('properties/{propertyId}/pools', [PropertyController::class, 'storePool']);
            Route::put('properties/{property}/pools/{pool}', [PropertyController::class, 'updatePool']);
            Route::delete('properties/{property}/pools/{pool}', [PropertyController::class, 'destroyPool']);
            Route::delete('properties/{propertyId}/pools', [PropertyController::class, 'destroyAllPools']);

            // Property Images endpoints
            Route::get('properties/{property}/images', [PropertyController::class, 'showImages']);
            Route::post('properties/{propertyId}/images', [PropertyController::class, 'storeImage']);
            Route::put('properties/{property}/images/{image}', [PropertyController::class, 'updateImage']);
            Route::delete('properties/{property}/images/{image}', [PropertyController::class, 'destroyImage']);
            Route::delete('properties/{propertyId}/images', [PropertyController::class, 'destroyAllImages']);

            // Sessions endpoints
            Route::get('properties/{property}/sessions', [PropertyController::class, 'showSessions']);
            Route::post('properties/{propertyId}/sessions', [PropertyController::class, 'storeSession']);
            Route::put('properties/{property}/sessions/{session}', [PropertyController::class, 'updateSession']);
            Route::delete('properties/{property}/sessions/{session}', [PropertyController::class, 'destroySession']);
            Route::delete('properties/{propertyId}/sessions', [PropertyController::class, 'destroyAllSessions']);

            // Property Details endpoints
            Route::get('properties/{property}/details', [PropertyController::class, 'showDetail']);
            Route::post('properties/{propertyId}/details', [PropertyController::class, 'storeDetail']);
            Route::put('properties/{property}/details/{detail}', [PropertyController::class, 'updateDetail']);
            Route::delete('properties/{property}/details/{detail}', [PropertyController::class, 'destroyDetail']);

            //

            //Users Management  
            Route::apiResource('users', UserController::class);
            Route::post('users/update/{user}', [UserController::class, 'updateUser']);

            //Admins Management  
            Route::get('admins', [AdminController::class, 'index']);
            Route::post('admins', [AdminController::class, 'store']);
            Route::get('admins/{admin}', [AdminController::class, 'show']);
            Route::post('admins/update/{admin}', [AdminController::class, 'update']);
            Route::delete('admins/{admin}', [AdminController::class, 'destroy']);

            //Offers Management  
            Route::apiResource('offers', OffersController::class);

            //Ads Management  
            Route::apiResource('ads', AdController::class);
            Route::post('ads/update/{add}', [AdController::class, 'updateAd']);

            //Ads Management  
            Route::apiResource('ads', AdController::class);
            Route::post('ads/update/{add}', [AdController::class, 'updateAd']);

            Route::apiResource('city-details', CityDetailController::class);
            Route::post('city-details/update/{id}', [CityDetailController::class, 'update']);

            Route::apiResource('type-details', PropertyTypeDetailController::class);
            Route::post('type-details/update/{id}', [PropertyTypeDetailController::class, 'update']);
            Route::get('/profile', function (Request $request) {
                return $request->user();
            });
        });
    });







});




