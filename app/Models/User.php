<?php

namespace App\Models;

// Descomenta esta linea si en algun momento quieres obligar
// a que los usuarios verifiquen su correo electronico.
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Atributos que se pueden asignar de forma masiva.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
    ];

    /**
     * Atributos que deben ocultarse al serializar el modelo.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Devuelve los atributos que deben convertirse automaticamente.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Comprueba si el usuario tiene rol de administrador.
     */
    public function esAdministrador(): bool
    {
        return $this->rol === 'administrador';
    }
}
