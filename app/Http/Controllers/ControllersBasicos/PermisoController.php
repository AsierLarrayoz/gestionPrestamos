<?php

namespace App\Http\Controllers\ControllersBasicos;

use App\Http\Controllers\Controller;
use App\Models\ModelosBasicos\Rol;
use App\Models\ModelosBasicos\Permiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermisoController extends Controller
{
    /**
     * Muestra la lista de Roles definidos.
     */
    public function index()
    {
        // Cargamos los roles con sus permisos para mostrarlos en la tabla
        $roles = Rol::with('permisos')->get();
        return view('permisos.index', compact('roles'));
    }

    /**
     * Muestra el formulario para crear un nuevo Rol.
     */
    public function create()
    {
        // Necesitamos todos los permisos para pintar los checkboxes
        $permisos = Permiso::all();
        return view('permisos.create', compact('permisos'));
    }

    /**
     * Guarda el nuevo Rol en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name|max:50',
            'permissions' => 'array' // Array de IDs de permisos seleccionados
        ]);

        try {
            DB::beginTransaction();

            // 1. Creamos el Rol
            $rol = Rol::create([
                'name' => $request->name,
                'label' => $request->name, // Usamos el mismo nombre como etiqueta por ahora
            ]);

            // 2. Sincronizamos los permisos (tabla pivote permission_role)
            if ($request->has('permissions')) {
                $rol->permisos()->sync($request->permissions);
            }

            DB::commit();
            return redirect()->route('permisos.index')->with('success', 'Rol creado correctamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error al crear el rol: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario para editar un Rol existente.
     */
    public function edit(string $id)
    {
        $rol = Rol::with('permisos')->findOrFail($id);
        $permisos = Permiso::all();

        return view('permisos.edit', compact('rol', 'permisos'));
    }

    /**
     * Actualiza el Rol y sus permisos.
     */
    public function update(Request $request, string $id)
    {
        $rol = Rol::findOrFail($id);

        // Protección: No dejar editar el nombre del Super Admin para no romper la lógica del sistema
        if ($rol->id == 1 || $rol->name == 'Super Administrador') {
            // Solo dejamos actualizar permisos, pero no el nombre
            // O podemos bloquearlo totalmente si prefieres.
        }

        $request->validate([
            'name' => 'required|string|max:50|unique:roles,name,' . $id,
            'permissions' => 'array'
        ]);

        $rol->update([
            'name' => $request->name,
            'label' => $request->name,
        ]);

        // Actualizamos la tabla pivote.
        // sync() borra los que no estén en el array y añade los nuevos.
        $rol->permisos()->sync($request->input('permissions', []));

        return redirect()->route('permisos.index')->with('success', 'Rol actualizado correctamente.');
    }

    /**
     * Elimina un Rol.
     */
    public function destroy(string $id)
    {
        $rol = Rol::findOrFail($id);

        // 1. Seguridad: No borrar el rol Super Admin
        if ($rol->id == 1 || $rol->name == 'Super Administrador') {
            return back()->with('error', 'No se puede eliminar el rol de Super Administrador.');
        }

        // 2. Seguridad: No borrar si hay usuarios asignados
        // Como la relación es many-to-many, contamos en la tabla pivote role_user
        // Nota: Laravel permite acceder a esto si definiste la relación 'users()' en el modelo Rol.
        // Si no, podemos hacerlo manual o confiar en el foreign key constraint si lo pusiste.

        // Asumiendo que tienes la relación users() en el modelo Rol:
        /*
        if ($rol->users()->count() > 0) {
            return back()->with('error', 'No puedes eliminar un rol que tiene usuarios asignados.');
        }
        */

        $rol->delete(); // El 'cascade' de la migración limpiará la tabla permission_role

        return redirect()->route('permisos.index')->with('success', 'Rol eliminado correctamente.');
    }
}
