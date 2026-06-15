<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\RentalController as AdminRentalController;
use App\Http\Controllers\Client\ClientController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Ruta genérica "dashboard" — redirige según el rol del usuario autenticado
Route::middleware('auth')->get('/dashboard', function () {
    return auth()->user()->isAdmin()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('client.dashboard');
})->name('dashboard');

// =====================================================
// Rutas Admin
// =====================================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Categorías (CRUD sin "show", no se necesita vista de detalle)
    Route::resource('categories', CategoryController::class)->except(['show']);

    // Equipos (CRUD completo)
    Route::resource('equipment', EquipmentController::class);

    // Alquileres
    Route::get('/rentals', [AdminRentalController::class, 'index'])->name('rentals.index');
    Route::get('/rentals/{rental}', [AdminRentalController::class, 'show'])->name('rentals.show');
    Route::patch('/rentals/{rental}/approve', [AdminRentalController::class, 'approve'])->name('rentals.approve');
    Route::patch('/rentals/{rental}/return', [AdminRentalController::class, 'returnRental'])->name('rentals.return');
});

// =====================================================
// Rutas Cliente
// =====================================================
Route::middleware(['auth', 'role:client'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', [ClientController::class, 'dashboard'])->name('dashboard');

    // Catálogo de equipos
    Route::get('/catalog', [ClientController::class, 'catalog'])->name('catalog');
    Route::get('/equipment/{equipment}', [ClientController::class, 'showEquipment'])->name('equipment.detail');

    // Alquileres del cliente
    Route::get('/my-rentals', [ClientController::class, 'myRentals'])->name('my-rentals');
    Route::get('/rentals/create', [ClientController::class, 'createRental'])->name('rentals.create');
    Route::post('/rentals', [ClientController::class, 'storeRental'])->name('rentals.store');
    Route::patch('/rentals/{rental}/cancel', [ClientController::class, 'cancelRental'])->name('rentals.cancel');
});

// =====================================================
// Perfil de usuario (cualquier usuario autenticado)
// =====================================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
