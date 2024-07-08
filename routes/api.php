<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClinicController;

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::middleware(['auth:sanctum'])->prefix('v1/secured')->group(function () {  
    Route::post('admin/clinics', [ClinicController::class, 'store']);
    Route::put('admin/clinics/{clinic}', [ClinicController::class, 'updateClinic']);

    Route::put('admin/bookings/{booking}', [ClinicController::class, 'updateBooking']);

    Route::post('logout', [AuthController::class, 'logout']);
});

Route::prefix('v1/public')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('deleteUser', [AuthController::class, 'deleteUser']);

    Route::get('clinics/view-bookings', [ClinicController::class, 'viewBookingByDate']);
    Route::get('clinics/availability', [ClinicController::class, 'availabilityByMonth']);
    Route::post('clinics/book', [ClinicController::class, 'book']);
});