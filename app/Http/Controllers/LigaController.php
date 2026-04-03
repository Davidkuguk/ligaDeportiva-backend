<?php

namespace App\Http\Controllers;

use App\Models\Liga;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LigaController extends Controller
{
    /**
     * Muestra el listado de ligas.
     */
    public function index(): JsonResponse
    {
        // withCount anade el numero de partidos sin necesidad
        // de cargar todos los registros relacionados.
        $ligas = Liga::query()
            ->withCount('partidos')
            ->orderBy('temporada', 'desc')
            ->orderBy('nombre')
            ->get();

        return response()->json([
            'data' => $ligas,
        ]);
    }

    /**
     * Guarda una nueva liga.
     */
    public function store(Request $request): JsonResponse
    {
        $datosValidados = $this->validarLiga($request);

        $liga = Liga::create($datosValidados);

        return response()->json([
            'message' => 'Liga creada correctamente.',
            'data' => $liga->loadCount('partidos'),
        ], 201);
    }

    /**
     * Muestra una liga concreta.
     */
    public function show(Liga $liga): JsonResponse
    {
        // En el detalle cargamos los partidos y tambien los clubes
        // implicados para que la respuesta sea mas completa.
        $liga->load([
            'partidos.clubLocal',
            'partidos.clubVisitante',
        ]);
        $liga->loadCount('partidos');

        return response()->json([
            'data' => $liga,
        ]);
    }

    /**
     * Actualiza una liga existente.
     */
    public function update(Request $request, Liga $liga): JsonResponse
    {
        $datosValidados = $this->validarLiga($request);

        $liga->update($datosValidados);

        return response()->json([
            'message' => 'Liga actualizada correctamente.',
            'data' => $liga->fresh()->loadCount('partidos'),
        ]);
    }

    /**
     * Elimina una liga.
     */
    public function destroy(Liga $liga): JsonResponse
    {
        $liga->delete();

        return response()->json([
            'message' => 'Liga eliminada correctamente.',
        ]);
    }

    /**
     * Reune en un solo sitio las reglas de validacion de la liga.
     */
    private function validarLiga(Request $request): array
    {
        $datosValidados = $request->validate([
            'nombre' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[\pL\pN\s\.\'\-]+$/u'],
            'deporte' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\pL\pN\s\.\'\-]+$/u'],
            'temporada' => ['required', 'regex:/^\d{4}\/\d{4}$/'],
        ], [
            'nombre.regex' => 'El nombre de la liga solo puede contener letras, numeros, espacios y signos simples.',
            'deporte.regex' => 'El deporte solo puede contener letras, numeros, espacios y signos simples.',
            'temporada.regex' => 'La temporada debe seguir el formato 2025/2026.',
        ]);

        // Comprobamos que la segunda parte de la temporada
        // sea exactamente el anio siguiente a la primera.
        [$anioInicio, $anioFin] = array_map('intval', explode('/', $datosValidados['temporada']));

        if ($anioFin !== $anioInicio + 1) {
            throw ValidationException::withMessages([
                'temporada' => 'La temporada debe tener anios consecutivos, por ejemplo 2025/2026.',
            ]);
        }

        return $datosValidados;
    }
}
