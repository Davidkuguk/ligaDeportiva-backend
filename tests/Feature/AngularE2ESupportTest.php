<?php

namespace Tests\Feature;

use Tests\TestCase;

class AngularE2ESupportTest extends TestCase
{
    public function test_health_endpoint_confirms_the_api_is_available_for_e2e_tests(): void
    {
        // Este endpoint sirve para que una prueba E2E compruebe rapidamente
        // que el backend esta levantado antes de probar pantallas de Angular.
        $this->getJson('/api/health')
            ->assertOk()
            ->assertJson([
                'status' => 'ok',
                'service' => 'liga-deportiva-backend',
            ]);
    }

    public function test_api_accepts_angular_preflight_requests(): void
    {
        // Los navegadores envian una peticion OPTIONS antes de algunas
        // llamadas desde Angular. Si CORS falla aqui, el E2E fallara tambien.
        $this->withHeaders([
            'Origin' => 'http://localhost:4200',
            'Access-Control-Request-Method' => 'GET',
        ])
            ->options('/api/jugadores')
            // 204 significa que Laravel acepta la preflight sin devolver contenido.
            ->assertNoContent()
            ->assertHeader('Access-Control-Allow-Origin', 'http://localhost:4200');
    }
}
