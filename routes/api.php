
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/v1/health', function () {
    return response()->json([
        'status' => 'ok',
        'app' => 'contac-api',
    ]);
});

// Listar los contactos del usuario autenticado
Route::get('/v1/contacts', function (Request $request) {
    return response()->json(
        $request->user()->contacts()->get()
    );
})->middleware('auth:sanctum');

// Crear un contacto
Route::post('/v1/contacts', function (Request $request) {
    $datos = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'phone_number' => ['required', 'string', 'max:255'],
        'address' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255'],
        'avatar' => ['nullable', 'string', 'max:2048'],
    ]);

    $contacto = $request->user()->contacts()->create($datos);

    return response()->json($contacto, 201);
})->middleware('auth:sanctum');

// Consultar un contacto propio
Route::get('/v1/contacts/{contact}', function (
    Request $request,
    string $contact
) {
    $contacto = $request->user()->contacts()->findOrFail($contact);

    return response()->json($contacto);
})->middleware('auth:sanctum');

// Actualizar un contacto propio
Route::patch('/v1/contacts/{contact}', function (
    Request $request,
    string $contact
) {
    $contacto = $request->user()->contacts()->findOrFail($contact);

    $datos = $request->validate([
        'name' => ['sometimes', 'required', 'string', 'max:255'],
        'phone_number' => ['sometimes', 'required', 'string', 'max:255'],
        'address' => ['sometimes', 'required', 'string', 'max:255'],
        'email' => ['sometimes', 'required', 'email', 'max:255'],
        'avatar' => ['sometimes', 'nullable', 'string', 'max:2048'],
    ]);

    $contacto->update($datos);

    return response()->json($contacto->fresh());
})->middleware('auth:sanctum');

// Eliminar un contacto propio
Route::delete('/v1/contacts/{contact}', function (
    Request $request,
    string $contact
) {
    $contacto = $request->user()->contacts()->findOrFail($contact);

    $contacto->delete();

    return response()->noContent();
})->middleware('auth:sanctum');

// Consultar el usuario autenticado
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
