<?php

it('responde el estado del servicio', function () {
    $this->getJson('/api/v1/health')
        ->assertOk()
        ->assertJson([
            'status' => 'ok',
            'app' => 'contac-api',
        ]);
});

it('responde 404 en texto plano cuando la ruta no existe', function () {
    $this->get('/api/v1/no-existe')
        ->assertNotFound()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
        ->assertSeeText('El recurso solicitado no existe.');
});
