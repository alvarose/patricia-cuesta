<?php

use App\Http\Controllers\Web\Admin\Dashboard\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
});

Route::redirect('dashboard', '/admin')->middleware('auth')->name('dashboard');
