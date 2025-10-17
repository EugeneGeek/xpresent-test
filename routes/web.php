<?php

use Illuminate\Support\Facades\Route;


Route::get('/', [\App\Http\Controllers\ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{service}', [\App\Http\Controllers\ServiceController::class, 'show'])->name('services.show');
Route::post('/bookings', [\App\Http\Controllers\BookingController::class, 'store'])->name('bookings.store');
Route::get('/bookings/by-date/{service}/{date}', [\App\Http\Controllers\BookingController::class, 'byDate'])
    ->name('bookings.byDate');


