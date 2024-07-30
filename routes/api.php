<?php

use App\Http\Controllers\Admin\AdController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\AdminAuthController as TestCon;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CityDetailController;
use App\Http\Controllers\Admin\OffersController;
use App\Http\Controllers\Admin\PropertyTypeController;
use App\Http\Controllers\Admin\PropertyTypeDetailController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\PropertyController;

use App\Http\Controllers\BookingController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
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
    //bookings &  payment
    Route::post('/bookings', [BookingController::class, 'book']);
    Route::get('/payment/callback', [BookingController::class, 'callback']);
    Route::post('/payment/webhook', [BookingController::class, 'handleWebhook']);

    //user Profile Page
    Route::get('users/{ownerId}/{ownerType}/properties', [ProfileController::class, 'userProperties']);
    Route::get('users/{ownerId}/{ownerType}/reviews', [ProfileController::class, 'userReviews']);

    // Properties and cities and Types
    Route::get('/properties', [UserPropertyController::class, 'index']);
    Route::get('/properties/{id}', [UserPropertyController::class, 'show']);
    Route::get('/cities', [UserCityController::class, 'index']);
    Route::get('/types', [UserPropertyTypeController::class, 'index']);

    // Home Page Content
    Route::get('cities-details', [UserCityDetailController::class, 'index']);
    Route::get('types-details', [UserPropertyTypeDetailController::class, 'index']);
    Route::get('/ads', [UserAdController::class, 'index']);
    Route::get('/offers', [UserOffersController::class, 'propertiesWithActiveOffers']);

    // Auth
    Route::post('login', [AuthController::class, 'sendVerificationCode']);
    Route::post('verify', [AuthController::class, 'verify']);

    Route::middleware('auth:sanctum')->group(function () {
        // User notifications
        Route::get('notifications', [NotificationController::class, 'index']);
        Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::get('notifications/read', [NotificationController::class, 'readNotifications']);
        Route::get('notifications/unread', [NotificationController::class, 'unreadNotifications']);
        Route::delete('notifications/delete/{id}', [NotificationController::class, 'deleteNotification']);
        Route::delete('notifications/delete-all', [NotificationController::class, 'deleteAllNotifications']);

        Route::post('property/add', [UserPropertyController::class, 'store']);
        Route::get('my/properties/user', [UserPropertyController::class, 'userProperties']);
        Route::get('my/properties/user/approved', [UserPropertyController::class, 'approvedProperties']);
        Route::get('my/properties/user/rejected', [UserPropertyController::class, 'rejectedProperties']);
        Route::delete('my/properties/user/rejected/{id}', [UserPropertyController::class, 'deleteRejectedProperty']);


        Route::post('/bookings', [BookingController::class, 'book']);
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

            // //Ads Management  
            // Route::apiResource('ads', AdController::class);
            // Route::post('ads/update/{add}', [AdController::class, 'updateAd']);

            // Cities in Home page
            Route::apiResource('city-details', CityDetailController::class);
            Route::post('city-details/update/{id}', [CityDetailController::class, 'update']);

            // Types in Home page
            Route::apiResource('type-details', PropertyTypeDetailController::class);
            Route::post('type-details/update/{id}', [PropertyTypeDetailController::class, 'update']);

            // Admin notifications
            Route::get('notifications', [AdminNotificationController::class, 'index']);
            Route::post('notifications/{id}/read', [AdminNotificationController::class, 'markAsRead']);
            Route::post('notifications/read-all', [AdminNotificationController::class, 'markAllAsRead']);
            Route::get('notifications/read', [AdminNotificationController::class, 'readNotifications']);
            Route::get('notifications/unread', [AdminNotificationController::class, 'unreadNotifications']);
            Route::delete('notifications/delete/{id}', [AdminNotificationController::class, 'deleteNotification']);
            Route::delete('notifications/delete-all', [AdminNotificationController::class, 'deleteAllNotifications']);

            // Management Properties Request
            Route::get('requests/properties', [PropertyController::class, 'showPendingProperties']);
            Route::post('requests/properties/{id}/approve', [PropertyController::class, 'approveProperty']);
            Route::post('requests/properties/{id}/reject', [PropertyController::class, 'rejectProperty']);

            Route::get('/profile', function (Request $request) {
                return $request->user();
            });
        });
    });







});





