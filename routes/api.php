<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Ruta de prueba
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'app' => 'contac-api',
        ]);
    });

    // Autenticación
    Route::post('/auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:login');

    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // Listar contactos del usuario autenticado
        Route::get('/contacts', function (Request $request) {
            return response()->json([
                'data' => $request->user()->contacts,
            ]);
        });

        // Crear contacto
        Route::post('/contacts', function (Request $request) {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'phone_number' => 'required|string|max:30',
                'address' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'avatar' => 'nullable|string|max:255',
            ]);

            $contact = $request->user()
                ->contacts()
                ->create($validated);

            return response()->json([
                'data' => $contact,
            ], 201);
        });

        // Consultar un contacto
        Route::get('/contacts/{contact}', function (
            Request $request,
            Contact $contact
        ) {
            $contact = $request->user()
                ->contacts()
                ->findOrFail($contact->id);

            return response()->json([
                'data' => $contact,
            ]);
        });

        // Actualizar contacto
        Route::patch('/contacts/{contact}', function (
            Request $request,
            Contact $contact
        ) {
            $contact = $request->user()
                ->contacts()
                ->findOrFail($contact->id);

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'phone_number' => 'sometimes|required|string|max:30',
                'address' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|max:255',
                'avatar' => 'nullable|string|max:255',
            ]);

            $contact->update($validated);

            return response()->json([
                'data' => $contact,
            ]);
        });

        // Eliminar contacto
        Route::delete('/contacts/{contact}', function (
            Request $request,
            Contact $contact
        ) {
            $contact = $request->user()
                ->contacts()
                ->findOrFail($contact->id);

            $contact->delete();

            return response()->noContent();
        });
    });
});
