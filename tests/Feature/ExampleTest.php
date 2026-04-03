<?php

namespace Tests\Feature;

// Descomenta esta linea si quieres reiniciar la base de datos en cada prueba.
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Ejemplo de prueba basica.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
