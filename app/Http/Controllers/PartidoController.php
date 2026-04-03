<?php

namespace App\Http\Controllers;

use App\Models\Partido;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PartidoController extends Controller
{
    /**
     * Muestra el listado de partidos con sus relaciones principales.
     */
    public function index(): JsonResponse
    {
        $partidos = Partido::query()
            ->with(['liga', 'clubLocal', 'clubVisitante'])
            ->orderBy('fecha')
            ->get();

        return response()->json([
            'data' => $partidos,
        ]);
    }

    /**
     * Guarda un nuevo partido.
     */
    public function store(Request $request): JsonResponse
    {
        $datosValidados = $this->validarPartido($request);

        $partido = Partido::create($datosValidados);

        return response()->json([
            'message' => 'Partido creado correctamente.',
            'data' => $partido->load(['liga', 'clubLocal', 'clubVisitante']),
        ], 201);
    }

    /**
     * Muestra un partido concreto.
     */
    public function show(Partido $partido): JsonResponse
    {
        return response()->json([
            'data' => $partido->load(['liga', 'clubLocal', 'clubVisitante']),
        ]);
    }

    /**
     * Actualiza un partido existente.
     */
    public function update(Request $request, Partido $partido): JsonResponse
    {
        $datosValidados = $this->validarPartido($request);

        $partido->update($datosValidados);

        return response()->json([
            'message' => 'Partido actualizado correctamente.',
            'data' => $partido->fresh()->load(['liga', 'clubLocal', 'clubVisitante']),
        ]);
    }

    /**
     * Elimina un partido.
     */
    public function destroy(Partido $partido): JsonResponse
    {
        $partido->delete();

        return response()->json([
            'message' => 'Partido eliminado correctamente.',
        ]);
    }

    /**
     * Centraliza las reglas de validacion del partido.
     */
    private function validarPartido(Request $request): array
    {
        $datosValidados = $request->validate([
            'liga_id' => ['required', 'integer', 'exists:ligas,id'],
            'club_local_id' => ['required', 'integer', 'exists:clubs,id'],
            'club_visitante_id' => ['required', 'integer', 'exists:clubs,id'],
            'fecha' => ['required', 'date'],
            'resultado' => ['nullable', 'regex:/^\d{1,2}\-\d{1,2}$/'],
        ], [
            'resultado.regex' => 'El resultado debe seguir el formato 3-1.',
        ]);

        // Un mismo club no puede jugar contra si mismo.
        if ($datosValidados['club_local_id'] === $datosValidados['club_visitante_id']) {
            throw ValidationException::withMessages([
                'club_visitante_id' => 'El club visitante debe ser distinto al club local.',
            ]);
        }

        return $datosValidados;
    }
}
