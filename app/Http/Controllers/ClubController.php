<?php

namespace App\Http\Controllers;

use App\Models\Club;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClubController extends Controller
{
    /**
     * Muestra el listado de clubes.
     */
    public function index(): JsonResponse
    {
        // Cargamos el numero de jugadores de cada club para que la API
        // devuelva informacion util sin necesidad de otra consulta extra.
        $clubs = Club::query()
            ->withCount('jugadores')
            ->orderBy('nombre')
            ->get();

        return response()->json([
            'data' => $clubs,
        ]);
    }

    /**
     * Guarda un nuevo club en la base de datos.
     */
    public function store(Request $request): JsonResponse
    {
        $datosValidados = $this->validarClub($request);

        $club = Club::create($datosValidados);

        return response()->json([
            'message' => 'Club creado correctamente.',
            'data' => $club->loadCount('jugadores'),
        ], 201);
    }

    /**
     * Muestra un club concreto.
     */
    public function show(Club $club): JsonResponse
    {
        // En el detalle del club cargamos sus jugadores para ver la relacion.
        $club->load('jugadores');
        $club->loadCount(['jugadores', 'partidosLocales', 'partidosVisitantes']);

        return response()->json([
            'data' => $club,
        ]);
    }

    /**
     * Actualiza un club existente.
     */
    public function update(Request $request, Club $club): JsonResponse
    {
        $datosValidados = $this->validarClub($request);

        $club->update($datosValidados);

        return response()->json([
            'message' => 'Club actualizado correctamente.',
            'data' => $club->fresh()->loadCount('jugadores'),
        ]);
    }

    /**
     * Elimina un club.
     */
    public function destroy(Club $club): JsonResponse
    {
        $club->delete();

        return response()->json([
            'message' => 'Club eliminado correctamente.',
        ]);
    }

    /**
     * Reune en un solo sitio las reglas de validacion del club.
     */
    private function validarClub(Request $request): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[\pL\pN\s\.\'\-]+$/u'],
            'ciudad' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[\pL\s\.\'\-]+$/u'],
            'categoria' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\pL\s\.\'\-]+$/u'],
        ], [
            'nombre.regex' => 'El nombre del club solo puede contener letras, numeros, espacios y signos simples.',
            'ciudad.regex' => 'La ciudad solo puede contener letras, espacios y signos simples.',
            'categoria.regex' => 'La categoria solo puede contener letras, espacios y signos simples.',
        ]);
    }
}
