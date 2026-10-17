<?php

use App\Http\Controllers\KatalogsController;
use App\Http\Controllers\ProfileController;
use App\Models\Zona;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'zones' => Zona::orderBy('zonas_id')->get(),
    ]);
})->name('home');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/ka-tas-darbojas', function () {
    return Inertia::render('HowItWorks');
})->name('how-it-works');

Route::get('/katalogs', [KatalogsController::class, 'index'])->name('katalogs');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';