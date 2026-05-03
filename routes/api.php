<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\JugadorController;
use App\Http\Controllers\LigaController;
use App\Http\Controllers\PartidoController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;

Route::get('/health', function (): JsonResponse {
    return response()->json([
        'status' => 'ok',
        'service' => 'liga-deportiva-backend',
        'framework' => app()->version(),
    ]);
});

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// apiResource crea automaticamente las rutas REST tipicas:
// index, store, show, update y destroy.
Route::apiResource('clubs', ClubController::class)->only(['index', 'show']);
Route::apiResource('jugadores', JugadorController::class)
    ->parameters(['jugadores' => 'jugador'])
    ->only(['index', 'show']);
Route::apiResource('ligas', LigaController::class)->only(['index', 'show']);
Route::apiResource('partidos', PartidoController::class)->only(['index', 'show']);

// Solo un administrador puede crear, editar o borrar registros.
Route::middleware('admin')->group(function (): void {
    Route::apiResource('clubs', ClubController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('jugadores', JugadorController::class)
        ->parameters(['jugadores' => 'jugador'])
        ->only(['store', 'update', 'destroy']);
    Route::apiResource('ligas', LigaController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('partidos', PartidoController::class)->only(['store', 'update', 'destroy']);
});
