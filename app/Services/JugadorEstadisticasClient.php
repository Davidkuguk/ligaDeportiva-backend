<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class JugadorEstadisticasClient
{
    /**
     * Este metodo representa una llamada a un servicio externo de estadisticas.
     * En produccion podria consultar una API real, pero en los tests se simula
     * con Http::fake() para no depender de internet ni de servidores externos.
     *
     * @return array<string, mixed>
     */
    public function resumen(int $jugadorId): array
    {
        // La URL base se lee desde config/services.php para poder cambiarla
        // segun el entorno: local, testing o produccion.
        $baseUrl = rtrim((string) config('services.jugadores_stats.url'), '/');

        // acceptJson() indica que esperamos una respuesta JSON.
        // throw() hace que Laravel lance error si la API responde con fallo.
        return Http::acceptJson()
            ->get($baseUrl.'/jugadores/'.$jugadorId.'/resumen')
            ->throw()
            ->json();
    }
}
