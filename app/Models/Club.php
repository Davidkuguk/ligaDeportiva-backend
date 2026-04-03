<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Club extends Model
{
    // fillable indica los campos que Laravel permite rellenar de golpe
    // cuando usamos create(), update() o metodos parecidos.
    // Esto nos protege frente a asignaciones masivas no deseadas.
    protected $fillable = [
        'nombre',
        'ciudad',
        'categoria',
    ];

    // Un club tiene muchos jugadores.
    // Laravel buscara automaticamente la clave foranea club_id
    // en la tabla jugadores porque la relacion parte de este modelo.
    public function jugadores(): HasMany
    {
        return $this->hasMany(Jugador::class);
    }

    // Esta relacion nos deja sacar todos los partidos donde este club
    // juega como equipo local.
    // Aqui si indicamos manualmente la clave foranea porque no se llama club_id,
    // sino club_local_id.
    public function partidosLocales(): HasMany
    {
        return $this->hasMany(Partido::class, 'club_local_id');
    }

    // Igual que la relacion anterior, pero para los partidos
    // en los que el club actua como visitante.
    public function partidosVisitantes(): HasMany
    {
        return $this->hasMany(Partido::class, 'club_visitante_id');
    }
}
