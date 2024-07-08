<?php

use App\Http\Controllers\ClinicController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('admin/clinics', [ClinicController::class, 'store']);
Route::put('admin/clinics/{clinic}', [ClinicController::class, 'update']);
Route::get('clinics/availability', [ClinicController::class, 'availability']);

Route::post('clinics/book', [ClinicController::class, 'book']);
Route::put('admin/bookings/{booking}', [ClinicController::class, 'update']);
