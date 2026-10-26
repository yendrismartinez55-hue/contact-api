
<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

function datosContacto(): array
{
    return [
        'name' => 'Ana Pérez',
        'phone_number' => '3001234567',
        'address' => 'Bogotá',
        'email' => 'ana.contacto@example.com',
        'avatar' => null,
    ];
}

it('impide consultar contactos sin autenticación', function () {
    $this->getJson('/api/v1/contacts')
        ->assertUnauthorized();
});

it('permite listar los contactos del usuario autenticado', function () {
    $user = User::factory()->create();

    $user->contacts()->create(datosContacto());

    Sanctum::actingAs($user);

    $this->getJson('/api/v1/contacts')
        ->assertOk()
        ->assertJsonFragment([
            'name' => 'Ana Pérez',
            'email' => 'ana.contacto@example.com',
        ]);
});

it('permite crear un contacto', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $this->postJson('/api/v1/contacts', datosContacto())
        ->assertCreated()
        ->assertJsonFragment([
            'name' => 'Ana Pérez',
            'email' => 'ana.contacto@example.com',
        ]);

    $this->assertDatabaseHas('contacts', [
        'user_id' => $user->id,
        'email' => 'ana.contacto@example.com',
    ]);
});

it('valida los campos obligatorios al crear un contacto', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $this->postJson('/api/v1/contacts', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'name',
            'phone_number',
            'address',
            'email',
        ]);
});

it('permite consultar un contacto propio', function () {
    $user = User::factory()->create();

    $contact = $user->contacts()->create(datosContacto());

    Sanctum::actingAs($user);

    $this->getJson("/api/v1/contacts/{$contact->id}")
        ->assertOk()
        ->assertJsonFragment([
            'name' => 'Ana Pérez',
        ]);
});

it('permite actualizar un contacto propio', function () {
    $user = User::factory()->create();

    $contact = $user->contacts()->create(datosContacto());

    Sanctum::actingAs($user);

    $this->patchJson("/api/v1/contacts/{$contact->id}", [
        'name' => 'Ana María Pérez',
    ])
        ->assertOk()
        ->assertJsonFragment([
            'name' => 'Ana María Pérez',
        ]);

    $this->assertDatabaseHas('contacts', [
        'id' => $contact->id,
        'name' => 'Ana María Pérez',
    ]);
});

it('permite eliminar un contacto propio', function () {
    $user = User::factory()->create();

    $contact = $user->contacts()->create(datosContacto());

    Sanctum::actingAs($user);

    $this->deleteJson("/api/v1/contacts/{$contact->id}")
        ->assertNoContent();

    $this->assertDatabaseMissing('contacts', [
        'id' => $contact->id,
    ]);
});

it('impide acceder al contacto de otro usuario', function () {
    $propietario = User::factory()->create();
    $otroUsuario = User::factory()->create();

    $contact = $propietario->contacts()->create(datosContacto());

    Sanctum::actingAs($otroUsuario);

    $this->getJson("/api/v1/contacts/{$contact->id}")
        ->assertNotFound();
});
