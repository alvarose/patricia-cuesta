<?php

use App\Http\Controllers\Web\Public\Booking\BookingController;
use App\Http\Controllers\Web\Public\Booking\SlotController;
use App\Http\Controllers\Web\Public\Contact\ContactController;
use Illuminate\Support\Facades\Route;

Route::get('reservas/slots', [SlotController::class, 'index'])
    ->middleware('throttle:60,1')
    ->name('booking.slots');

Route::post('reservas', [BookingController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('booking.store');

Route::post('contacto', [ContactController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('contact.store');
