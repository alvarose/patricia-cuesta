<?php

use App\Http\Controllers\Web\Admin\Message\MessageController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('mensajes', [MessageController::class, 'index'])->name('messages.index');
    Route::post('mensajes/{message}/leido', [MessageController::class, 'read'])->name('messages.read');
    Route::post('mensajes/{message}/respuesta', [MessageController::class, 'reply'])->name('messages.reply');
    Route::post('mensajes/{message}/paciente', [MessageController::class, 'patient'])->name('messages.patient');
});
