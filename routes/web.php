<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
// Rotta pubblica — pagina iniziale
Route::get('/', function () {
    return view('welcome');
});

// Rotte protette — solo utenti autenticati
Route::middleware('auth')->group(function () {

    // Dashboard
   Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profilo utente (già creato da Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rotte solo admin
    Route::middleware('isAdmin')->group(function () {
        // Qui aggiungeremo le rotte per movimenti, soci, verbali
    });
});

require __DIR__ . '/auth.php';
