<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Jugador;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAdminMiddlewareAndValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_user_cannot_create_records(): void
    {
        $this->postJson('/api/clubs', [
            'nombre' => 'Club Invitado',
            'ciudad' => 'Ciudad Real',
            'categoria' => 'Juvenil',
        ])
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Debes iniciar sesion para realizar esta accion.');
    }

    public function test_guest_user_can_create_records_with_demo_admin_key(): void
    {
        config([
            'app.demo_admin_key' => 'demo-ut3-key',
        ]);

        $this->withHeader('X-Demo-Admin-Key', 'demo-ut3-key')
            ->postJson('/api/clubs', [
                'nombre' => 'Club Demo',
                'ciudad' => 'Ciudad Real',
                'categoria' => 'Juvenil',
            ])
            ->assertCreated()
            ->assertJsonPath('data.nombre', 'Club Demo');
    }

    public function test_non_admin_user_cannot_create_records(): void
    {
        $usuarioNormal = User::factory()->create([
            'rol' => 'usuario',
        ]);

        $this->actingAs($usuarioNormal)->postJson('/api/clubs', [
            'nombre' => 'Club Sin Permiso',
            'ciudad' => 'Ciudad Real',
            'categoria' => 'Juvenil',
        ])
            ->assertForbidden()
            ->assertJsonPath('message', 'No tienes permisos de administrador para realizar esta accion.');
    }

    public function test_admin_can_create_records(): void
    {
        $admin = User::factory()->administrador()->create();

        $this->actingAs($admin)->postJson('/api/clubs', [
            'nombre' => 'Club Administrador',
            'ciudad' => 'Ciudad Real',
            'categoria' => 'Juvenil',
        ])
            ->assertCreated()
            ->assertJsonPath('data.nombre', 'Club Administrador');
    }

    public function test_validation_rejects_invalid_club_data(): void
    {
        $admin = User::factory()->administrador()->create();

        $this->actingAs($admin)->postJson('/api/clubs', [
            'nombre' => '@@@',
            'ciudad' => 'C1udad',
            'categoria' => 'Juvenil1',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nombre', 'ciudad', 'categoria']);
    }

    public function test_validation_rejects_repeated_dorsal_in_the_same_club(): void
    {
        $admin = User::factory()->administrador()->create();

        $club = Club::create([
            'nombre' => 'Club Maestre',
            'ciudad' => 'Ciudad Real',
            'categoria' => 'Juvenil',
        ]);

        Jugador::create([
            'nombre' => 'Jugador Uno',
            'posicion' => 'Defensa',
            'dorsal' => 7,
            'club_id' => $club->id,
        ]);

        $this->actingAs($admin)->postJson('/api/jugadores', [
            'nombre' => 'Jugador Dos',
            'posicion' => 'Ala',
            'dorsal' => 7,
            'club_id' => $club->id,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['dorsal']);
    }

    public function test_validation_rejects_invalid_temporada_format(): void
    {
        $admin = User::factory()->administrador()->create();

        $this->actingAs($admin)->postJson('/api/ligas', [
            'nombre' => 'Liga Mal Formada',
            'deporte' => 'Baloncesto',
            'temporada' => '2025-2026',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['temporada']);
    }

    public function test_validation_rejects_non_consecutive_temporada_years(): void
    {
        $admin = User::factory()->administrador()->create();

        $this->actingAs($admin)->postJson('/api/ligas', [
            'nombre' => 'Liga Incorrecta',
            'deporte' => 'Baloncesto',
            'temporada' => '2025/2027',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['temporada']);
    }
}
