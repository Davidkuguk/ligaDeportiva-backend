<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Guardamos una contrasena por defecto para reutilizarla
     * en los usuarios de prueba sin recalcularla cada vez.
     */
    protected static ?string $password;

    /**
     * Aqui definimos los datos por defecto de un usuario generado
     * con la factoria.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'rol' => 'usuario',
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Este estado deja el correo sin verificar por si en algun test
     * queremos simular ese caso.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Estado especial para crear usuarios administradores en pruebas.
     */
    public function administrador(): static
    {
        return $this->state(fn (array $attributes) => [
            'rol' => 'administrador',
        ]);
    }
}
