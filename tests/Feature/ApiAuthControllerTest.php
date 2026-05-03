<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiAuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_and_use_bearer_token_for_admin_routes(): void
    {
        User::create([
            'name' => 'Admin Liga',
            'username' => 'admin',
            'email' => 'admin@liga.local',
            'password' => Hash::make('admin'),
            'rol' => 'administrador',
        ]);

        $login = $this->postJson('/api/auth/login', [
            'username' => 'admin',
            'password' => 'admin',
        ])
            ->assertOk()
            ->assertJsonPath('user.username', 'admin')
            ->assertJsonPath('user.tipo', 'admin')
            ->json();

        $this->withToken($login['token'])
            ->postJson('/api/clubs', [
                'nombre' => 'Club Token Admin',
                'ciudad' => 'Ciudad Real',
                'categoria' => 'Juvenil',
            ])
            ->assertCreated()
            ->assertJsonPath('data.nombre', 'Club Token Admin');
    }

    public function test_database_seeder_creates_default_admin_user(): void
    {
        $this->seed();

        $admin = User::where('username', 'admin')->first();

        $this->assertNotNull($admin);
        $this->assertTrue($admin->esAdministrador());
        $this->assertTrue(Hash::check('admin', $admin->password));
    }
}
