<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'firstName' => ['required', 'string', 'max:50'],
            'lastName' => ['required', 'string', 'max:80'],
            'username' => ['required', 'string', 'min:3', 'max:30', 'alpha_dash', 'unique:users,username'],
            'password' => ['required', 'string', 'min:4'],
            'tipo' => ['required', Rule::in(['admin', 'normal', 'arbitro', 'jugador', 'capitan', 'entrenador', 'aficionado'])],
            'teamName' => ['nullable', 'string', 'max:255'],
        ]);

        if ($datos['tipo'] === 'admin' && ! $this->requestHasAdminToken($request)) {
            return response()->json([
                'message' => 'Solo un administrador puede crear otros administradores.',
            ], 403);
        }

        $username = Str::lower($datos['username']);

        $user = User::create([
            'name' => trim($datos['firstName'].' '.$datos['lastName']),
            'username' => $username,
            'email' => $username.'@liga.local',
            'password' => Hash::make($datos['password']),
            'rol' => $this->toBackendRole($datos['tipo']),
            'team_name' => $datos['teamName'] ?? null,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Usuario creado correctamente.',
            'user' => $this->serializeUser($user),
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credenciales = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $username = Str::lower($credenciales['username']);

        $user = User::query()
            ->where('username', $username)
            ->orWhere('email', $username)
            ->first();

        if (! $user || ! Hash::check($credenciales['password'], $user->password)) {
            return response()->json([
                'message' => 'Usuario o contrasena incorrectos.',
            ], 422);
        }

        $plainToken = Str::random(80);
        $user->forceFill([
            'api_token' => hash('sha256', $plainToken),
        ])->save();

        return response()->json([
            'ok' => true,
            'message' => 'Sesion iniciada correctamente.',
            'token' => $plainToken,
            'user' => $this->serializeUser($user),
        ]);
    }

    private function serializeUser(User $user): array
    {
        [$firstName] = explode(' ', $user->name, 2) + [''];

        return [
            'username' => $user->username ?? $user->email,
            'firstName' => $firstName,
            'tipo' => $this->toFrontendRole($user->rol),
            'teamName' => $user->team_name,
            'createdAt' => $user->created_at?->toISOString(),
        ];
    }

    private function requestHasAdminToken(Request $request): bool
    {
        $token = (string) $request->bearerToken();

        if ($token === '') {
            return false;
        }

        $user = User::where('api_token', hash('sha256', $token))->first();

        return (bool) $user?->esAdministrador();
    }

    private function toBackendRole(string $tipo): string
    {
        return $tipo === 'admin' ? 'administrador' : $tipo;
    }

    private function toFrontendRole(string $rol): string
    {
        return $rol === 'administrador' ? 'admin' : $rol;
    }
}
