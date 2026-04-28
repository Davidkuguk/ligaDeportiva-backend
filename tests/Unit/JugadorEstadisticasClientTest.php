<?php

namespace Tests\Unit;

use App\Services\JugadorEstadisticasClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class JugadorEstadisticasClientTest extends TestCase
{
    public function test_it_uses_a_fake_http_response_for_player_statistics(): void
    {
        // En el test usamos una URL falsa para demostrar que no se llama
        // a ningun servicio real de internet.
        config()->set('services.jugadores_stats.url', 'https://estadisticas.test');

        // Http::fake() intercepta la peticion HTTP y devuelve estos datos.
        // Asi controlamos exactamente la respuesta que recibe la aplicacion.
        Http::fake([
            'estadisticas.test/jugadores/15/resumen' => Http::response([
                'jugador_id' => 15,
                'partidos' => 8,
                'goles' => 5,
                'tarjetas' => 1,
            ]),
        ]);

        // Ejecutamos el cliente como lo haria el codigo real.
        $resumen = app(JugadorEstadisticasClient::class)->resumen(15);

        // Comprobamos que el resultado final coincide con el JSON simulado.
        $this->assertSame([
            'jugador_id' => 15,
            'partidos' => 8,
            'goles' => 5,
            'tarjetas' => 1,
        ], $resumen);

        // Tambien verificamos que la aplicacion intento llamar al endpoint correcto.
        Http::assertSent(fn ($request): bool => $request->url() === 'https://estadisticas.test/jugadores/15/resumen');
    }
}
