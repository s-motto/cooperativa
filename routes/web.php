<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MovimentoController;
use App\Http\Controllers\SocioController;
use App\Http\Controllers\VerbaleController;
use Illuminate\Support\Facades\Route;


// Rotta pubblica
Route::get('/', function () {
    return view('welcome');
});

// Rotte protette — solo utenti autenticati
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Profilo utente
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rotte solo admin
    Route::middleware('isAdmin')->group(function () {

        Route::get('movimenti/export', [MovimentoController::class, 'export'])
            ->name('movimenti.export');

        Route::resource('movimenti', MovimentoController::class)
            ->parameters(['movimenti' => 'movimento']);

        Route::resource('soci', SocioController::class)
            ->parameters(['soci' => 'socio']);

        Route::resource('verbali', VerbaleController::class)
            ->parameters(['verbali' => 'verbale']);

        Route::post('soci/{socio}/quote', [SocioController::class, 'storeQuota'])
            ->name('soci.quote.store');

        Route::get('utenti/create', [App\Http\Controllers\UtenteController::class, 'create'])
            ->name('utenti.create');

        Route::post('utenti', [App\Http\Controllers\UtenteController::class, 'store'])
            ->name('utenti.store');

    });

});

require __DIR__.'/auth.php';

// Registrazione pubblica disabilitata
Route::get('register', function() {
    abort(403, 'Registrazione non disponibile.');
});
Route::post('register', function() {
    abort(403, 'Registrazione non disponibile.');
});