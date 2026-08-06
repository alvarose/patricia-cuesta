<?php

use App\Http\Controllers\Web\Public\Home\HomeController;
use App\Http\Controllers\Web\Public\Legal\LegalController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('{section}', [LegalController::class, 'show'])
    ->where('section', 'aviso-legal|privacidad|cookies')
    ->name('legal');
