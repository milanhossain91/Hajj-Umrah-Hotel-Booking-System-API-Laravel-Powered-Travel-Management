<?php

use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VisaController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\HotelPackageController;

use App\Http\Controllers\QuoteController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProfitController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\HotelRateController;
use App\Http\Controllers\QuoteItemController;
use App\Http\Controllers\TransportController;
use App\Http\Controllers\QuoteReportController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TransferRateController;
use App\Http\Controllers\Api\PermissionsController;
use App\Http\Controllers\Api\ResetPasswordController;
use App\Http\Controllers\Api\ForgotPasswordController;


// Public Routes
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

// Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
// Route::post('/reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm']);

Route::post('/send-otp', [ForgotPasswordController::class, 'sendOtp']);
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPasswordWithOtp']);

// Protected Routes
Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/loggeduser', [UserController::class, 'logged_user']);
    Route::post('/changepassword', [UserController::class, 'change_password']);



    //List
    Route::get('/role_list', [UserController::class, 'role_list']);
    Route::get('/permission_list', [UserController::class, 'permission_list']);
    Route::get('/user_list', [UserController::class, 'user_list']);
    Route::get('/all_user_list', [UserController::class, 'all_user_list']);

    Route::get('/user_show/{id}', [UserController::class, 'show']);

    //User Update
    Route::get('/user_edit/{id}', [UserController::class, 'user_edit']);
    Route::put('/user_update/{id}', [UserController::class, 'user_update']);

    Route::get('/role_wise_user', [UserController::class, 'role_wise_user']);

    //User Update
    Route::put('/user_update/{id}', [UserController::class, 'user_update']);

    //User Delete
    Route::delete('/user_delete/{id}', [UserController::class, 'user_delete']);

    //Assign Permission
    Route::put('/assign_permission/{id}', [UserController::class, 'assign_permission']);

    //role & permission
    Route::resource('roles', RolesController::class);
    Route::resource('permissions', PermissionsController::class);

    // Milan Api

    Route::apiResource('hotel_packages', HotelPackageController::class);
    Route::apiResource('hotels', HotelController::class);
    Route::apiResource('hotel-rates', HotelRateController::class);

    Route::apiResource('transport_packages', TransferController::class);
    Route::apiResource('transfer-rates', TransferRateController::class);

    Route::apiResource('visas', VisaController::class);

    Route::apiResource('suppliers', SupplierController::class);
    Route::apiResource('quotes', QuoteController::class);
    Route::apiResource('quote-items', QuoteItemController::class);
    Route::apiResource('reservations', ReservationController::class);
    Route::apiResource('profits', ProfitController::class);

    Route::apiResource('transports', TransportController::class);

    Route::apiResource('locations', LocationController::class);

    Route::get('/quotes_report', [QuoteController::class, 'quoteReport']);

    Route::get('/customers_invoice', [QuoteController::class, 'customersInvoice']);

    Route::get('/customers_report', [QuoteController::class, 'customersReport']);

    Route::get('/search_transport_package', [TransferController::class, 'searchTransportPackage']);
    Route::get('/search_hotel_package', [HotelPackageController::class, 'searchHotelPackage']);

    Route::get('/quote_search', [QuoteController::class, 'quoteSearch']);







});



