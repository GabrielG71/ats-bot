<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AtsController;

Route::get('/', [AtsController::class, 'index'])->name('home');
Route::post('/analisar', [AtsController::class, 'analisar'])->name('analisar');
