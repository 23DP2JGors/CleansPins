<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KatalogsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GrozasController;
use App\Http\Controllers\PasutijumiController;
use App\Http\Controllers\PakomatiController;
use App\Models\Zona;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'zones' => Zona::orderBy('zonas_id')->get(),
    ]);
})->name('home');

// "Mans konts" (e-pasta verifikācija netiek izmantota, tāpēc tikai 'auth')
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

Route::get('/ka-tas-darbojas', function () {
    return Inertia::render('HowItWorks');
})->name('how-it-works');

Route::get('/katalogs', [KatalogsController::class, 'index'])->name('katalogs');

// pakomātu meklēšana (JSON), pieejama bez autorizācijas
Route::get('/pakomati', [PakomatiController::class, 'index'])->name('pakomati.index');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/groza', [GrozasController::class, 'index'])->name('groza.index');
    Route::post('/groza', [GrozasController::class, 'store'])->name('groza.store');
    Route::patch('/groza/{rinda}', [GrozasController::class, 'update'])->name('groza.update');
    Route::delete('/groza/{rinda}', [GrozasController::class, 'destroy'])->name('groza.destroy');
    Route::get('/pasutijums', [PasutijumiController::class, 'create'])->name('pasutijums.create');
    Route::post('/pasutijums', [PasutijumiController::class, 'store'])->name('pasutijums.store');
    Route::get('/pasutijums/{pasutijums}', [PasutijumiController::class, 'show'])->name('pasutijums.show');
});

require __DIR__.'/auth.php';