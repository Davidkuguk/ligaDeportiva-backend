<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Liga;
use App\Models\Partido;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiPartidoControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Comprueba el CRUD basico de partidos y sus relaciones.
     */
    public function test_partidos_api_permite_listar_y_gestionar_registros(): void
    {
        $admin = User::factory()->administrador()->create();
        $liga = Liga::create([
            'nombre' => 'Liga Escolar',
            'deporte' => 'Futbol sala',
            'temporada' => '2025/2026',
        ]);
        $clubLocal = Club::create([
            'nombre' => 'IES Maestre',
            'ciudad' => 'Ciudad Real',
            'categoria' => 'Juvenil',
        ]);
        $clubVisitante = Club::create([
            'nombre' => 'IES Atenea',
            'ciudad' => 'Ciudad Real',
            'categoria' => 'Juvenil',
        ]);
        $partido = Partido::create([
            'liga_id' => $liga->id,
            'club_local_id' => $clubLocal->id,
            'club_visitante_id' => $clubVisitante->id,
            'fecha' => '2026-04-20 17:00:00',
            'resultado' => '2-1',
        ]);

        $this->getJson('/api/partidos')
            ->assertOk()
            ->assertJsonPath('data.0.resultado', '2-1')
            ->assertJsonPath('data.0.club_local.nombre', 'IES Maestre');

        $this->getJson('/api/partidos/'.$partido->id)
            ->assertOk()
            ->assertJsonPath('data.liga.nombre', 'Liga Escolar');

        $this->actingAs($admin)->postJson('/api/partidos', [
            'liga_id' => $liga->id,
            'club_local_id' => $clubVisitante->id,
            'club_visitante_id' => $clubLocal->id,
            'fecha' => '2026-04-27 17:00:00',
            'resultado' => null,
        ])->assertCreated()
            ->assertJsonPath('data.club_local.nombre', 'IES Atenea');

        $this->actingAs($admin)->putJson('/api/partidos/'.$partido->id, [
            'liga_id' => $liga->id,
            'club_local_id' => $clubLocal->id,
            'club_visitante_id' => $clubVisitante->id,
            'fecha' => '2026-04-20 17:00:00',
            'resultado' => '3-3',
        ])->assertOk()
            ->assertJsonPath('data.resultado', '3-3');

        $this->actingAs($admin)->deleteJson('/api/partidos/'.$partido->id)
            ->assertOk();

        $this->assertDatabaseMissing('partidos', [
            'id' => $partido->id,
        ]);
    }

    /**
     * Comprueba que no se permita un partido con el mismo club en ambos lados.
     */
    public function test_partidos_api_valida_que_los_clubes_sean_distintos(): void
    {
        $admin = User::factory()->administrador()->create();
        $liga = Liga::create([
            'nombre' => 'Liga Escolar',
            'deporte' => 'Futbol sala',
            'temporada' => '2025/2026',
        ]);
        $club = Club::create([
            'nombre' => 'IES Maestre',
            'ciudad' => 'Ciudad Real',
            'categoria' => 'Juvenil',
        ]);

        $this->actingAs($admin)->postJson('/api/partidos', [
            'liga_id' => $liga->id,
            'club_local_id' => $club->id,
            'club_visitante_id' => $club->id,
            'fecha' => '2026-04-30 17:00:00',
            'resultado' => '1-0',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['club_visitante_id']);
    }
}
