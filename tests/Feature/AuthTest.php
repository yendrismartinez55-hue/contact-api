
<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

function loginData(array $cambios = []): array
{
    return array_merge([
        'email' => 'ana@contacts.test',
        'password' => 'secreta123',
        'device_name' => 'pruebas',
    ], $cambios);
}

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'ana@contacts.test',
        'password' => bcrypt('secreta123'),
    ]);
});

it('entrega un token con credenciales correctas', function () {
    $this->postJson('/api/v1/auth/login', loginData())
        ->assertOk()
        ->assertJsonStructure([
            'token',
            'token_type',
            'user' => ['id', 'name', 'email'],
        ])
        ->assertJsonPath('user.email', 'ana@contacts.test')
        ->assertJsonMissingPath('user.password');

    expect($this->user->tokens()->count())->toBe(1);
});

it('rechaza una contraseña incorrecta con 422', function () {
    $this->postJson('/api/v1/auth/login', loginData([
        'password' => 'otra',
    ]))
        ->assertUnprocessable()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
        ->assertSeeText('Las credenciales no son correctas.');
});

it('usa el mismo mensaje si el correo no existe', function () {
    $this->postJson('/api/v1/auth/login', loginData([
        'email' => 'nadie@contacts.test',
    ]))
        ->assertUnprocessable()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
        ->assertSeeText('Las credenciales no son correctas.');
});

it('valida los campos obligatorios', function (string $campo) {
    $this->postJson('/api/v1/auth/login', loginData([
        $campo => '',
    ]))
        ->assertUnprocessable()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
        ->assertSeeText('Los datos enviados no son válidos.');
})->with(['email', 'password', 'device_name']);

it('pide token para ver el perfil', function () {
    $this->getJson('/api/v1/auth/me')
        ->assertUnauthorized();
});

it('responde 401 en texto plano aunque falte la cabecera Accept', function () {
    $this->get('/api/v1/auth/me')
        ->assertUnauthorized()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
        ->assertSeeText('No estás autenticado.');
});

it('devuelve el perfil del usuario autenticado', function () {
    Sanctum::actingAs($this->user);

    $this->getJson('/api/v1/auth/me')
        ->assertOk()
        ->assertJsonPath('data.email', 'ana@contacts.test')
        ->assertJsonMissingPath('data.password');
});

it('cierra sesión y revoca el token', function () {
    $user = User::where('email', 'ana@contacts.test')->first();

    $token = $user->createToken('pruebas')->plainTextToken;

    $this->withToken($token)
        ->postJson('/api/v1/auth/logout')
        ->assertNoContent();

    expect($user->tokens()->count())->toBe(0);
});

it('limita los intentos de login a 5 por minuto', function () {
    foreach (range(1, 5) as $intento) {
        $this->postJson(
            '/api/v1/auth/login',
            loginData(['password' => 'otra'])
        )->assertUnprocessable();
    }

    $this->postJson(
        '/api/v1/auth/login',
        loginData(['password' => 'otra'])
    )->assertTooManyRequests();
});
