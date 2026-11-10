
<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ContactController;
use Illuminate\Support\Facades\Route;

// Ruta para comprobar que la API funciona
Route::get('/v1/health', function () {
    return response()->json([
        'status' => 'ok',
        'app' => 'contac-api',
    ]);
});

// Rutas de autenticación
Route::prefix('v1/auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

// Rutas de contactos protegidas
Route::middleware('auth:sanctum')
    ->prefix('v1/contacts')
    ->controller(ContactController::class)
    ->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{contact}', 'show');
        Route::patch('/{contact}', 'update');
        Route::delete('/{contact}', 'destroy');
    });
