<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ContactController extends Controller
{
    // Listar todos los contactos del usuario autenticado
    public function index(Request $request): JsonResponse
    {
        $contacts = $request->user()
            ->contacts()
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Lista de contactos',
            'data' => $contacts,
        ], 200);
    }

    // Crear un contacto
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'avatar' => 'nullable|string|max:255',
        ]);

        $contact = $request->user()
            ->contacts()
            ->create($validated);

        return response()->json([
            'message' => 'Contacto creado correctamente',
            'data' => $contact,
        ], 201);
    }

    // Consultar un contacto
    public function show(Request $request, Contact $contact): JsonResponse
    {
        $contact = $request->user()
            ->contacts()
            ->findOrFail($contact->id);

        return response()->json([
            'message' => 'Contacto encontrado',
            'data' => $contact,
        ], 200);
    }

    // Actualizar un contacto
    public function update(
        Request $request,
        Contact $contact
    ): JsonResponse {
        $contact = $request->user()
            ->contacts()
            ->findOrFail($contact->id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone_number' => 'sometimes|required|string|max:20',
            'address' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255',
            'avatar' => 'sometimes|nullable|string|max:255',
        ]);

        $contact->update($validated);

        return response()->json([
            'message' => 'Contacto actualizado correctamente',
            'data' => $contact,
        ], 200);
    }

    // Eliminar un contacto
    public function destroy(
        Request $request,
        Contact $contact
    ): Response {
        $contact = $request->user()
            ->contacts()
            ->findOrFail($contact->id);

        $contact->delete();

        return response()->noContent();
    }
}
