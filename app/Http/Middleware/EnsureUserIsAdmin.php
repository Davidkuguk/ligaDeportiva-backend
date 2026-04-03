<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Comprueba que el usuario autenticado sea administrador
     * antes de permitir acciones de modificacion.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Atajo temporal para demos en Postman:
        // si llega la cabecera con la clave correcta, dejamos pasar la peticion
        // aunque no exista un login real implementado todavia.
        $demoAdminKeyConfigurada = (string) config('app.demo_admin_key');
        $demoAdminKeyRecibida = (string) $request->header('X-Demo-Admin-Key', '');

        if (
            $demoAdminKeyConfigurada !== ''
            && $demoAdminKeyRecibida !== ''
            && hash_equals($demoAdminKeyConfigurada, $demoAdminKeyRecibida)
        ) {
            return $next($request);
        }

        // Si no hay usuario autenticado, devolvemos error 401.
        if (! $request->user()) {
            return response()->json([
                'message' => 'Debes iniciar sesion para realizar esta accion.',
            ], 401);
        }

        // Si el usuario existe pero no es administrador, devolvemos 403.
        if (! $request->user()->esAdministrador()) {
            return response()->json([
                'message' => 'No tienes permisos de administrador para realizar esta accion.',
            ], 403);
        }

        return $next($request);
    }
}
