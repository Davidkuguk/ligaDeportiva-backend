<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Partido extends Model
{
    // Estos son los datos que podemos enviar directamente al crear un partido.
    protected $fillable = [
        'liga_id',
        'club_local_id',
        'club_visitante_id',
        'fecha',
        'resultado',
    ];

    // casts sirve para decirle a Laravel como queremos interpretar
    // ciertos campos al leerlos de la base de datos.
    // Asi fecha no se trata como texto plano, sino como fecha/hora.
    protected $casts = [
        'fecha' => 'datetime',
    ];

    // Cada partido pertenece a una sola liga.
    public function liga(): BelongsTo
    {
        return $this->belongsTo(Liga::class);
    }

    // Relacion con el club que juega como local.
    // Tenemos que indicar la clave foranea porque no se llama club_id.
    public function clubLocal(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'club_local_id');
    }

    // Relacion con el club que juega como visitante.
    public function clubVisitante(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'club_visitante_id');
    }
}
