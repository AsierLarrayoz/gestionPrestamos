<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\Request;


class RolController extends Controller
{
    public function index()
    {
        $roles = Rol::all();
        return view('roles.index', compact('roles'));
    }
    public function create()
    {
        return view('roles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'rol' => 'required|unique:roles|max:255',
        ]);
        Rol::create($request->all());
        return redirect()->route('roles.index')->with('success', 'Rol creado correctamente.');
    }
    public function edit(Rol $role)
    {
        return view('roles.edit', compact('role'));
    }
    public function update(Request $request, Rol $role)
    {
        $request->validate([
            'rol' => 'required|max:255|unique:roles,rol,' . $role->id,
        ]);

        $role->update($request->all());

        return redirect()->route('roles.index')->with('success', 'Rol actualizado.');
    }
    public function destroy(Rol $role)
    {
        if ($role->users()->exists()) {
            return back()->with('error', 'No se puede eliminar un rol que tiene usuarios asignados.');
        }
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Rol eliminado.');
    }
}
