<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\EquipmentController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Admin\EquipmentController as AdminEquipmentController;
use App\Http\Controllers\Api\Admin\RentalController as AdminRentalController;
use App\Http\Controllers\Api\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\Api\Client\RentalController as ClientRentalController;
use Illuminate\Support\Facades\Route;

// =====================================================
// Autenticación (públicas)
// =====================================================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// =====================================================
// Rutas protegidas por token (Sanctum)
// =====================================================
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Perfil (cualquier usuario autenticado)
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::match(['put', 'patch'], '/profile', [ProfileController::class, 'update']);
    Route::delete('/profile', [ProfileController::class, 'destroy']);

    // Catálogo (lectura) — disponible para cualquier usuario autenticado
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);
    Route::get('/equipment', [EquipmentController::class, 'index']);
    Route::get('/equipment/{equipment}', [EquipmentController::class, 'show']);

    // =====================================================
    // Rutas Admin
    // =====================================================
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);

        Route::apiResource('categories', AdminCategoryController::class)->except(['show']);
        Route::apiResource('equipment', AdminEquipmentController::class);

        Route::get('/rentals', [AdminRentalController::class, 'index']);
        Route::get('/rentals/{rental}', [AdminRentalController::class, 'show']);
        Route::patch('/rentals/{rental}/approve', [AdminRentalController::class, 'approve']);
        Route::patch('/rentals/{rental}/return', [AdminRentalController::class, 'returnRental']);
    });

    // =====================================================
    // Rutas Cliente
    // =====================================================
    Route::middleware('role:client')->prefix('client')->group(function () {
        Route::get('/dashboard', [ClientDashboardController::class, 'index']);
    });

    // Alquileres del cliente autenticado (cualquier usuario con rol client)
    Route::middleware('role:client')->group(function () {
        Route::get('/rentals', [ClientRentalController::class, 'index']);
        Route::post('/rentals', [ClientRentalController::class, 'store']);
        Route::get('/rentals/{rental}', [ClientRentalController::class, 'show']);
        Route::patch('/rentals/{rental}/cancel', [ClientRentalController::class, 'cancel']);
    });
});
