<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Jugador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AngularJugadorContractTest extends TestCase
{
    // Usamos base de datos de testing para crear datos controlados
    // y no depender de los datos reales del proyecto.
    use RefreshDatabase;

    public function test_jugadores_endpoint_keeps_the_json_contract_expected_by_angular(): void
    {
        // Preparamos un club y un jugador con factories.
        // Estos datos son los que consumira el endpoint /api/jugadores.
        $club = Club::factory()->create([
            'nombre' => 'Club Maestre',
            'ciudad' => 'Ciudad Real',
            'categoria' => 'Juvenil',
        ]);

        Jugador::factory()->create([
            'nombre' => 'Alvaro Prieto',
            'posicion' => 'Defensa',
            'dorsal' => 5,
            'club_id' => $club->id,
        ]);

        // Este test no solo mira que responda 200.
        // Tambien comprueba la forma del JSON que Angular espera recibir.
        $this->getJson('/api/jugadores')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'nombre',
                        'posicion',
                        'dorsal',
                        'club_id',
                        'club' => [
                            'id',
                            'nombre',
                            'ciudad',
                            'categoria',
                        ],
                    ],
                ],
            ])
            // Por ultimo comprobamos valores concretos para asegurar
            // que el jugador y su club llegan bien relacionados.
            ->assertJsonPath('data.0.nombre', 'Alvaro Prieto')
            ->assertJsonPath('data.0.club.nombre', 'Club Maestre');
    }
}
