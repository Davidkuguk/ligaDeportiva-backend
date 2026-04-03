<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Jugador;
use App\Models\Liga;
use App\Models\Partido;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    // Cada test trabaja con una base limpia.
    use RefreshDatabase;

    public function test_database_seeder_populates_example_league_data(): void
    {
        // Ejecutamos todos los seeders definidos en DatabaseSeeder.
        $this->seed();

        // Verificamos que la cantidad de datos insertados coincide
        // con lo esperado en el ejemplo.
        $this->assertDatabaseCount('clubs', 3);
        $this->assertDatabaseCount('jugadores', 9);
        $this->assertDatabaseCount('ligas', 1);
        $this->assertDatabaseCount('partidos', 3);

        // Recuperamos algunos registros para hacer comprobaciones
        // un poco mas expresivas que solo contar filas.
        $liga = Liga::where('nombre', 'Liga Deportiva IES Maestre de Calatrava')->first();
        $clubMaestre = Club::where('nombre', 'IES Maestre de Calatrava')->first();

        // Nos aseguramos de que existen y de que las relaciones cargan bien.
        $this->assertNotNull($liga);
        $this->assertNotNull($clubMaestre);
        $this->assertCount(3, $clubMaestre->jugadores);
        $this->assertCount(3, $liga->partidos);
        $this->assertTrue(Partido::whereNotNull('resultado')->count() >= 2);
        $this->assertTrue(Jugador::count() >= 6);
    }
}
