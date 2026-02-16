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
            'permiso_usuarios'    => 'nullable|boolean',
            'permiso_activos'     => 'nullable|boolean',
            'permiso_almacenes'   => 'nullable|boolean',
            'permiso_incidencias' => 'nullable|boolean',
            'permiso_prestamos'   => 'nullable|boolean',
        ]);

        // 2. Creamos el perfil de permisos
        // Usamos has() porque si un checkbox no se marca, no viaja en el request
        Permiso::create([
            'permiso_usuarios'    => $request->has('permiso_usuarios'),
            'permiso_activos'     => $request->has('permiso_activos'),
            'permiso_almacenes'   => $request->has('permiso_almacenes'),
            'permiso_incidencias' => $request->has('permiso_incidencias'),
            'permiso_prestamos'   => $request->has('permiso_prestamos'),
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
            'permiso_usuarios'    => 'nullable|boolean',
            'permiso_activos'     => 'nullable|boolean',
            'permiso_almacenes'   => 'nullable|boolean',
            'permiso_incidencias' => 'nullable|boolean',
            'permiso_prestamos'   => 'nullable|boolean',
        ]);

        // Actualizamos cada campo evaluando si el checkbox fue marcado o no
        $permiso->update([
            'permiso_usuarios'    => $request->has('permiso_usuarios'),
            'permiso_activos'     => $request->has('permiso_activos'),
            'permiso_almacenes'   => $request->has('permiso_almacenes'),
            'permiso_incidencias' => $request->has('permiso_incidencias'),
            'permiso_prestamos'   => $request->has('permiso_prestamos'),
        ]);

        return redirect()->route('permisos.index')->with('success', 'Perfil de permisos actualizado.');
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
