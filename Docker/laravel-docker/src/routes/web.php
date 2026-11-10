<?php

use App\Http\Controllers\PlatoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Ejemplo del Bar Baio
Route::get('/carta', [PlatoController::class, 'index'])->name('carta');
Route::post('/platos', [PlatoController::class, 'store'])->name('platos.store');
Route::get('/api/carta', [PlatoController::class, 'api'])->name('api.carta');
