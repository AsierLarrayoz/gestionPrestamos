<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * @param $permisoRequerido - El string del permiso (ej: 'usuarios.escribir')
     */
    public function handle(Request $request, Closure $next, string $permisoRequerido = null): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!$permisoRequerido) {
            return $next($request);
        }

        $permisoRequerido = strtolower($permisoRequerido); //porsiacaso

        //esto da errror pero no es un error ya que $user = Auth::user(); devuelve el modelo de user
        if (!Auth::user()->hasPermission($permisoRequerido)) {

            if ($request->expectsJson()) {
                return response()->json(['error' => 'No tienes permiso para realizar esta acción.'], 403);
            }

            return redirect()->route('dashboard')
                ->with('error', 'Acceso denegado. No tienes el permiso: ' . $permisoRequerido);
        }

        return $next($request);
    }
}
