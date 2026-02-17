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

        $user = Auth::user();

        if (!$modulo) {
            return redirect('/dashboard')->with('error', 'Módulo de seguridad no especificado.');
        }

        $permisos = $user->permisos;
        $campoPermiso = "permiso_" . $modulo;
        if (!$permisos || !isset($permisos->$campoPermiso) || !$permisos->$campoPermiso) {

            $nombreLimpio = str_replace(['_r', '_wr'], '', $modulo);

            return redirect('/dashboard')->with('error', "No tienes permiso de acceso/escritura en: " . ucfirst($nombreLimpio));
        }

        return $next($request);
    }
}
