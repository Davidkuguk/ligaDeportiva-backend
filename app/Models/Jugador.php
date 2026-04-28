<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jugador extends Model
{
    // HasFactory permite crear jugadores falsos con Jugador::factory().
    use HasFactory;

    // Laravel habria intentado usar "jugadors" como nombre de tabla
    // por la forma inglesa del plural.
    // Como nuestra tabla real se llama "jugadores", lo indicamos a mano.
    protected $table = 'jugadores';

    // Estos son los atributos que permitimos guardar de forma masiva.
    protected $fillable = [
        'nombre',
        'posicion',
        'dorsal',
        'club_id',
    ];

    // Un jugador pertenece a un unico club.
    // Gracias a esta relacion podremos hacer cosas como:
    // $jugador->club->nombre
    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }
}
