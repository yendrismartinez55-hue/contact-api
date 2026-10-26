<?php

it('responde el estado del servicio', function () {
    $this->getJson('/api/v1/health')
        ->assertOk()
        ->assertJson([
            'status' => 'ok',
            'app' => 'contac-api',
        ]);
});

it('responde 404 en JSON cuando la ruta no existe', function () {
    $this->getJson('/api/v1/no-existe')
        ->assertNotFound()
        ->assertJsonStructure([
            'message',
        ]);
});
