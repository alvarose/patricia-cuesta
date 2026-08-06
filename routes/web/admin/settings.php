<?php

use App\Http\Controllers\Web\Admin\Settings\SettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('ajustes', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::post('ajustes', [SettingsController::class, 'update'])->name('settings.update');
});
