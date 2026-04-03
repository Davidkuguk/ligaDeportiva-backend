<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Liga extends Model
{
    // Campos que podemos rellenar facilmente al crear una liga.
    protected $fillable = [
        'nombre',
        'deporte',
        'temporada',
    ];

    // Una liga tiene muchos partidos.
    // Laravel deduce la clave foranea liga_id en la tabla partidos.
    public function partidos(): HasMany
    {
        return $this->hasMany(Partido::class);
    }
}
