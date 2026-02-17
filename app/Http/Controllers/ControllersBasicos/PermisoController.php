<?php

namespace App\Http\Controllers\ControllersBasicos;

use App\Http\Controllers\Controller;
use App\Models\ModelosBasicos\Permiso; // Cambiado de Rol a Perimiso
use Illuminate\Http\Request;

class PermisoController extends Controller
{
    public function index()
    {
        $permisos = Permiso::all();
        return view('permisos.index', compact('permisos'));
    }

    public function create()
    {
        return view('permisos.create');
    }

    public function store(Request $request)
    {
        // 1. Validamos que los datos lleguen (pueden ser nulos porque son checkboxes)
        $request->validate([
            'nombre_rol' => 'required|string|unique:permisos,nombre_rol|max:50',
        ]);

        // 2. Creamos el perfil de permisos
        Permiso::create([
            'nombre_rol'             => $request->nombre_rol,
            'permiso_usuarios_wr'    => $request->has('permiso_usuarios_wr'),
            'permiso_activos_wr'     => $request->has('permiso_activos_wr'),
            'permiso_almacenes_wr'   => $request->has('permiso_almacenes_wr'),
            'permiso_incidencias_wr' => $request->has('permiso_incidencias_wr'),
            'permiso_prestamos_wr'   => $request->has('permiso_prestamos_wr'),
            'permiso_reservas_wr'    => $request->has('permiso_reservas_wr'),
            'permiso_usuarios_r'    => $request->has('permiso_usuarios_r'),
            'permiso_activos_r'     => $request->has('permiso_activos_r'),
            'permiso_almacenes_r'   => $request->has('permiso_almacenes_r'),
            'permiso_incidencias_r' => $request->has('permiso_incidencias_r'),
            'permiso_prestamos_r'   => $request->has('permiso_prestamos_r'),
            'permiso_reservas_r'    => $request->has('permiso_reservas_r'),
        ]);

        return redirect()->route('permisos.index')->with('success', 'Perfil de permisos creado correctamente.');
    }

    public function edit(string $id)
    {
        $permiso = Permiso::findOrFail($id);
        return view('permisos.edit', compact('permiso'));
    }

    public function update(Request $request, string $id)
    {
        $permiso = Permiso::findOrFail($id);
        $request->validate([
            'nombre_rol' => 'required|string|max:50|unique:permisos,nombre_rol,' . $id,
        ]);

        $permiso->update([
            'nombre_rol'             => $request->nombre_rol,
            'permiso_usuarios_wr'    => $request->has('permiso_usuarios_wr'),
            'permiso_activos_wr'     => $request->has('permiso_activos_wr'),
            'permiso_almacenes_wr'   => $request->has('permiso_almacenes_wr'),
            'permiso_incidencias_wr' => $request->has('permiso_incidencias_wr'),
            'permiso_prestamos_wr'   => $request->has('permiso_prestamos_wr'),
            'permiso_reservas_wr'    => $request->has('permiso_reservas_wr'),
            'permiso_usuarios_r'     => $request->has('permiso_usuarios_r'),
            'permiso_activos_r'      => $request->has('permiso_activos_r'),
            'permiso_almacenes_r'    => $request->has('permiso_almacenes_r'),
            'permiso_incidencias_r'  => $request->has('permiso_incidencias_r'),
            'permiso_prestamos_r'    => $request->has('permiso_prestamos_r'),
            'permiso_reservas_r'    => $request->has('permiso_reservas_r'),

        ]);
        return redirect()->route('permisos.index')->with('success', 'Permisos del rol actualizados.');
    }

    public function destroy(string $id)
    {
        $permiso = Permiso::findOrFail($id);

        // Verificamos si hay usuarios usando este perfil de permisos
        if ($permiso->users()->exists()) {
            return back()->with('error', 'No se puede eliminar un perfil de permisos que tiene usuarios asignados.');
        }

        $permiso->delete();
        return redirect()->route('permisos.index')->with('success', 'Perfil eliminado.');
    }
}
