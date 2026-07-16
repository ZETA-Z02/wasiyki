<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HabitacionController;
use App\Http\Controllers\InquilinoController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RecordatorioController;


// Rutas Públicas
Route::post('/login', [AuthController::class, 'login']);
Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
// Ruta pública para recibir el código del frontend
Route::post('/auth/google/pkce', [AuthController::class, 'handleGooglePKCE']);

// Rutas Protegidas (Requieren Token de Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/arrendador/me', [AuthController::class, 'me']);
    Route::put('/arrendador/update', [AuthController::class, 'update']);
    Route::post('/logout', [AuthController::class, 'logout']);
    // Rutas de Habitaciones
    Route::get('/habitaciones/disponibles', [HabitacionController::class, 'disponibles']);
    Route::apiResource('habitaciones', HabitacionController::class)->parameters([
        'habitaciones' => 'habitacion'
    ]);

    // Rutas de Inquilinos
    Route::apiResource('inquilinos', InquilinoController::class);

    // Rutas de Contratos
    Route::post('/contratos/{contrato}/terminar', [ContratoController::class, 'terminar']);
    Route::apiResource('contratos', ContratoController::class);

    // Rutas de Pagos
    Route::get('/pagos/{pago}/comprobante', [PagoController::class, 'generarComprobante']);
    Route::apiResource('pagos', PagoController::class);

    // Ruta del Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Rutas de Recordatorios
    Route::post('/contratos/{contrato}/recordatorio', [RecordatorioController::class, 'enviarManual']);
});