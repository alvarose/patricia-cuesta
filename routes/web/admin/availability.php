<?php

use App\Http\Controllers\Web\Admin\Absence\AbsenceController;
use App\Http\Controllers\Web\Admin\Availability\AvailabilityController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('disponibilidad', [AvailabilityController::class, 'index'])->name('availability.index');
    Route::put('disponibilidad', [AvailabilityController::class, 'update'])->name('availability.update');

    Route::post('ausencias', [AbsenceController::class, 'store'])->name('absences.store');
    Route::delete('ausencias/{absence}', [AbsenceController::class, 'destroy'])->name('absences.destroy');
});
