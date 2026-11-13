
<?php

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('crea un contacto válido', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/v1/contacts', [
        'name' => 'Ana Torres',
        'phone_number' => '3001234567',
        'address' => 'Calle 10 # 20-30',
        'email' => 'ana@example.com',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'Ana Torres')
        ->assertJsonPath('data.phone_number', '3001234567')
        ->assertJsonPath('data.email', 'ana@example.com');

    $this->assertDatabaseHas('contacts', [
        'user_id' => $user->id,
        'name' => 'Ana Torres',
        'phone_number' => '3001234567',
        'address' => 'Calle 10 # 20-30',
        'email' => 'ana@example.com',
    ]);
});

test('devuelve 404 para un contacto ajeno', function () {
    $owner = User::factory()->create();
    $visitor = User::factory()->create();

    $contact = Contact::factory()->create([
        'user_id' => $owner->id,
        'name' => 'Contacto privado',
    ]);

    // Comprobar que el contacto realmente existe.
    $this->assertDatabaseHas('contacts', [
        'id' => $contact->id,
        'user_id' => $owner->id,
        'name' => 'Contacto privado',
    ]);

    // Comprobar que se puede consultar directamente.
    $this->assertNotNull(Contact::find($contact->id));

    Sanctum::actingAs($visitor);

    $this->getJson('/api/v1/contacts/'.$contact->id)
        ->assertNotFound();
});

test('solicita autenticación para consultar contactos', function () {
    $this->getJson('/api/v1/contacts')
        ->assertUnauthorized();
});

test('rechaza crear un contacto sin teléfono', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $this->postJson('/api/v1/contacts', [
        'name' => 'Ana Torres',
        'address' => 'Calle 10 # 20-30',
        'email' => 'ana@example.com',
    ])
        ->assertUnprocessable();

    $this->assertDatabaseMissing('contacts', [
        'name' => 'Ana Torres',
        'user_id' => $user->id,
    ]);
});

test('rechaza repetir correo o teléfono en la agenda del usuario', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    Contact::factory()->create([
        'user_id' => $user->id,
        'email' => 'ana@example.com',
        'phone_number' => '3001234567',
    ]);

    $this->postJson('/api/v1/contacts', [
        'name' => 'Otro contacto',
        'phone_number' => '3001234567',
        'address' => 'Calle 20',
        'email' => 'ana@example.com',
    ])
        ->assertUnprocessable();

    $this->assertDatabaseCount('contacts', 1);
});
