<?php

use App\Http\Controllers\Web\Admin\Appointment\AppointmentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('citas', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::patch('citas/{appointment}/estado', [AppointmentController::class, 'update'])->name('appointments.status');
    Route::post('citas/{appointment}/paciente', [AppointmentController::class, 'patient'])->name('appointments.patient');
    Route::delete('citas/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
});
