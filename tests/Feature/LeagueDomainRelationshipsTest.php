<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Jugador;
use App\Models\Liga;
use App\Models\Partido;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeagueDomainRelationshipsTest extends TestCase
{
    // RefreshDatabase reinicia la base de datos de pruebas para que
    // cada test empiece desde cero y no dependa de datos anteriores.
    use RefreshDatabase;

    public function test_main_entities_expose_the_expected_relationships(): void
    {
        // Creamos dos clubes que usaremos en el test.
        $clubLocal = Club::create([
            'nombre' => 'Club Maestre',
            'ciudad' => 'Ciudad Real',
            'categoria' => 'Juvenil',
        ]);

        $clubVisitante = Club::create([
            'nombre' => 'Club Calatrava',
            'ciudad' => 'Almagro',
            'categoria' => 'Juvenil',
        ]);

        // Creamos un jugador asociado al club local.
        $jugador = Jugador::create([
            'nombre' => 'Diego Lopez',
            'posicion' => 'Delantero',
            'dorsal' => 9,
            'club_id' => $clubLocal->id,
        ]);

        // Creamos una liga de prueba.
        $liga = Liga::create([
            'nombre' => 'Liga Escolar',
            'deporte' => 'Futbol sala',
            'temporada' => '2025/2026',
        ]);

        // Creamos un partido relacionando la liga y los dos clubes.
        $partido = Partido::create([
            'liga_id' => $liga->id,
            'club_local_id' => $clubLocal->id,
            'club_visitante_id' => $clubVisitante->id,
            'fecha' => '2026-04-10 18:00:00',
            'resultado' => '2-1',
        ]);

        // Comprobamos la relacion jugador -> club
        // y la relacion inversa club -> jugadores.
        $this->assertTrue($jugador->club->is($clubLocal));
        $this->assertCount(1, $clubLocal->jugadores);
        $this->assertTrue($clubLocal->jugadores->first()->is($jugador));

        // Comprobamos la relacion liga -> partidos
        // y la inversa partido -> liga.
        $this->assertCount(1, $liga->partidos);
        $this->assertTrue($liga->partidos->first()->is($partido));
        $this->assertTrue($partido->liga->is($liga));

        // Comprobamos las dos relaciones del partido con los clubes:
        // local y visitante.
        $this->assertTrue($partido->clubLocal->is($clubLocal));
        $this->assertTrue($partido->clubVisitante->is($clubVisitante));
        $this->assertCount(1, $clubLocal->partidosLocales);
        $this->assertCount(1, $clubVisitante->partidosVisitantes);
    }
}
