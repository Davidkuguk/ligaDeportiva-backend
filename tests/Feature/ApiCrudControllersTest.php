<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Jugador;
use App\Models\Liga;
use App\Models\Partido;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiCrudControllersTest extends TestCase
{
    use RefreshDatabase;

    public function test_club_crud_endpoints_work_correctly(): void
    {
        $admin = User::factory()->administrador()->create();

        $club = Club::create([
            'nombre' => 'Club Inicial',
            'ciudad' => 'Ciudad Real',
            'categoria' => 'Cadete',
        ]);

        $this->getJson('/api/clubs')
            ->assertOk()
            ->assertJsonPath('data.0.nombre', 'Club Inicial');

        $this->getJson('/api/clubs/'.$club->id)
            ->assertOk()
            ->assertJsonPath('data.nombre', 'Club Inicial');

        $this->actingAs($admin)->postJson('/api/clubs', [
            'nombre' => 'Club Nuevo',
            'ciudad' => 'Puertollano',
            'categoria' => 'Juvenil',
        ])
            ->assertCreated()
            ->assertJsonPath('data.nombre', 'Club Nuevo');

        $this->actingAs($admin)->putJson('/api/clubs/'.$club->id, [
            'nombre' => 'Club Actualizado',
            'ciudad' => 'Almagro',
            'categoria' => 'Senior',
        ])
            ->assertOk()
            ->assertJsonPath('data.nombre', 'Club Actualizado');

        $this->actingAs($admin)->deleteJson('/api/clubs/'.$club->id)
            ->assertOk()
            ->assertJsonPath('message', 'Club eliminado correctamente.');
    }

    public function test_jugador_crud_endpoints_work_correctly(): void
    {
        $admin = User::factory()->administrador()->create();

        $club = Club::create([
            'nombre' => 'Club Maestre',
            'ciudad' => 'Ciudad Real',
            'categoria' => 'Juvenil',
        ]);

        $jugador = Jugador::create([
            'nombre' => 'Diego Lopez',
            'posicion' => 'Delantero',
            'dorsal' => 9,
            'club_id' => $club->id,
        ]);

        $this->getJson('/api/jugadores')
            ->assertOk()
            ->assertJsonPath('data.0.nombre', 'Diego Lopez');

        $this->getJson('/api/jugadores/'.$jugador->id)
            ->assertOk()
            ->assertJsonPath('data.club.nombre', 'Club Maestre');

        $this->actingAs($admin)->postJson('/api/jugadores', [
            'nombre' => 'Pablo Ruiz',
            'posicion' => 'Defensa',
            'dorsal' => 4,
            'club_id' => $club->id,
        ])
            ->assertCreated()
            ->assertJsonPath('data.nombre', 'Pablo Ruiz');

        $this->actingAs($admin)->putJson('/api/jugadores/'.$jugador->id, [
            'nombre' => 'Diego Lopez Actualizado',
            'posicion' => 'Ala',
            'dorsal' => 10,
            'club_id' => $club->id,
        ])
            ->assertOk()
            ->assertJsonPath('data.dorsal', 10);

        $this->actingAs($admin)->deleteJson('/api/jugadores/'.$jugador->id)
            ->assertOk()
            ->assertJsonPath('message', 'Jugador eliminado correctamente.');
    }

    public function test_liga_crud_endpoints_work_correctly(): void
    {
        $admin = User::factory()->administrador()->create();

        $clubLocal = Club::create([
            'nombre' => 'Club Local',
            'ciudad' => 'Ciudad Real',
            'categoria' => 'Juvenil',
        ]);

        $clubVisitante = Club::create([
            'nombre' => 'Club Visitante',
            'ciudad' => 'Almagro',
            'categoria' => 'Juvenil',
        ]);

        $liga = Liga::create([
            'nombre' => 'Liga Escolar',
            'deporte' => 'Futbol sala',
            'temporada' => '2025/2026',
        ]);

        Partido::create([
            'liga_id' => $liga->id,
            'club_local_id' => $clubLocal->id,
            'club_visitante_id' => $clubVisitante->id,
            'fecha' => '2026-04-20 18:00:00',
            'resultado' => '1-0',
        ]);

        $this->getJson('/api/ligas')
            ->assertOk()
            ->assertJsonPath('data.0.nombre', 'Liga Escolar');

        $this->getJson('/api/ligas/'.$liga->id)
            ->assertOk()
            ->assertJsonPath('data.partidos.0.club_local.nombre', 'Club Local')
            ->assertJsonPath('data.partidos.0.club_visitante.nombre', 'Club Visitante');

        $this->actingAs($admin)->postJson('/api/ligas', [
            'nombre' => 'Liga Nueva',
            'deporte' => 'Baloncesto',
            'temporada' => '2026/2027',
        ])
            ->assertCreated()
            ->assertJsonPath('data.nombre', 'Liga Nueva');

        $this->actingAs($admin)->putJson('/api/ligas/'.$liga->id, [
            'nombre' => 'Liga Escolar Actualizada',
            'deporte' => 'Futbol 7',
            'temporada' => '2025/2026',
        ])
            ->assertOk()
            ->assertJsonPath('data.deporte', 'Futbol 7');

        $this->actingAs($admin)->deleteJson('/api/ligas/'.$liga->id)
            ->assertOk()
            ->assertJsonPath('message', 'Liga eliminada correctamente.');
    }
}
