<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Maneja la petición entrante.
     * * @param $modulo - El nombre del permiso que queremos revisar
     */
    // Añadimos "= null" para que no sea obligatorio y no de error
    public function handle(Request $request, Closure $next, string $modulo = null): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Si entramos a una ruta protegida sin especificar el módulo, denegamos por seguridad
        if (!$modulo) {
            return redirect('/dashboard')->with('error', 'Error de configuración de seguridad.');
        }

        $permisos = Auth::user()->permisos;
        $campoPermiso = "permiso_" . $modulo;

        if (!$permisos || !$permisos->$campoPermiso) {
            return redirect('/dashboard')->with('error', "No tienes acceso al módulo de " . ucfirst($modulo));
        }

        return $next($request);
    }
}
