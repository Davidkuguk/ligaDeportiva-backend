<?php

namespace App\Http\Controllers;

use App\Models\Jugador;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JugadorController extends Controller
{
    /**
     * Muestra el listado de jugadores.
     */
    public function index(): JsonResponse
    {
        // Cargamos el club para que el frontend sepa a que equipo
        // pertenece cada jugador.
        $jugadores = Jugador::query()
            ->with('club')
            ->orderBy('nombre')
            ->get();

        return response()->json([
            'data' => $jugadores,
        ]);
    }

    /**
     * Guarda un nuevo jugador.
     */
    public function store(Request $request): JsonResponse
    {
        $datosValidados = $this->validarJugador($request);

        $jugador = Jugador::create($datosValidados);

        return response()->json([
            'message' => 'Jugador creado correctamente.',
            'data' => $jugador->load('club'),
        ], 201);
    }

    /**
     * Muestra un jugador concreto.
     */
    public function show(Jugador $jugador): JsonResponse
    {
        return response()->json([
            'data' => $jugador->load('club'),
        ]);
    }

    /**
     * Actualiza un jugador existente.
     */
    public function update(Request $request, Jugador $jugador): JsonResponse
    {
        $datosValidados = $this->validarJugador($request, $jugador);

        $jugador->update($datosValidados);

        return response()->json([
            'message' => 'Jugador actualizado correctamente.',
            'data' => $jugador->fresh()->load('club'),
        ]);
    }

    /**
     * Elimina un jugador.
     */
    public function destroy(Jugador $jugador): JsonResponse
    {
        $jugador->delete();

        return response()->json([
            'message' => 'Jugador eliminado correctamente.',
        ]);
    }

    /**
     * Reune en un solo sitio las reglas de validacion del jugador.
     */
    private function validarJugador(Request $request, ?Jugador $jugador = null): array
    {
        // Para comprobar el dorsal unico por club necesitamos saber
        // a que club ira asociado el jugador.
        $clubId = $request->input('club_id', $jugador?->club_id);

        return $request->validate([
            'nombre' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[\pL\s\.\'\-]+$/u'],
            'posicion' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\pL\s\.\'\-]+$/u'],
            'dorsal' => [
                'required',
                'integer',
                'min:1',
                'max:99',
                Rule::unique('jugadores', 'dorsal')
                    ->where(fn ($query) => $query->where('club_id', $clubId))
                    ->ignore($jugador?->id),
            ],
            'club_id' => ['required', 'integer', 'exists:clubs,id'],
        ], [
            'nombre.regex' => 'El nombre del jugador solo puede contener letras, espacios y signos simples.',
            'posicion.regex' => 'La posicion solo puede contener letras, espacios y signos simples.',
        ]);
    }
}
