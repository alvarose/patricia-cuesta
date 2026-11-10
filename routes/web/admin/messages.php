<?php

use App\Http\Controllers\Web\Admin\Message\LinkPatientToMessageController;
use App\Http\Controllers\Web\Admin\Message\MarkMessageAsReadController;
use App\Http\Controllers\Web\Admin\Message\MessageController;
use App\Http\Controllers\Web\Admin\Message\ReplyToMessageController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('mensajes', [MessageController::class, 'index'])->name('messages.index');
    Route::post('mensajes/{message}/leido', MarkMessageAsReadController::class)->name('messages.read');
    Route::post('mensajes/{message}/respuesta', ReplyToMessageController::class)->name('messages.reply');
    Route::post('mensajes/{message}/paciente', LinkPatientToMessageController::class)->name('messages.patient');
});
