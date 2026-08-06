<?php

use App\Http\Controllers\Web\Admin\Patient\PatientController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('pacientes', [PatientController::class, 'index'])->name('patients.index');
    Route::post('pacientes', [PatientController::class, 'store'])->name('patients.store');
    Route::patch('pacientes/{patient}', [PatientController::class, 'update'])->name('patients.update');
    Route::delete('pacientes/{patient}', [PatientController::class, 'destroy'])->name('patients.destroy');
});
