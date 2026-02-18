<?php

namespace App\Http\Controllers;

use App\Models\ModelosBasicos\Permiso;
use App\Models\ModelosBasicos\Rol;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $usuarios = User::with('roles')->get();
        return view('configuracion.index', compact('usuarios'));
    }

    public function create()
    {
        $roles = Rol::with('permisos')->get();
        $permisos = Permiso::all();

        return view('configuracion.create', compact('roles', 'permisos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['nullable', 'exists:roles,id'],
            'permissions' => ['array'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 1. Asignar Rol (si se seleccionó uno)
        if ($request->filled('role_id')) {
            $user->roles()->attach($request->role_id);
        }

        // 2. Asignar Permisos Directos (Excepciones)
        if ($request->has('permissions')) {
            $user->permissions()->sync($request->permissions);
        }

        return redirect()->route('configuracion.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(string $id)
    {
        $usuario = User::with(['roles', 'permissions'])->findOrFail($id); // Cargamos sus permisos directos
        $roles = Rol::with('permisos')->get();
        $permisos = Permiso::all();

        return view('configuracion.edit', compact('usuario', 'roles', 'permisos'));
    }

    public function update(Request $request, string $id)
    {
        $usuario = User::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $usuario->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['nullable', 'exists:roles,id'],
            'permissions' => ['array'],
        ]);


        $usuario->name = $request->name;
        $usuario->email = $request->email;
        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }
        $usuario->save();

        // 1. Sincronizar Rol
        if ($request->filled('role_id')) {
            $usuario->roles()->sync([$request->role_id]);
        } else {
            $usuario->roles()->detach(); // Si lo deja vacío, le quitamos el rol
        }

        // 2. Sincronizar Permisos Directos
        // sync() borra los viejos y pone los nuevos marcados
        $usuario->permissions()->sync($request->input('permissions', []));

        return redirect()->route('configuracion.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        // ... (Tu código de destroy igual que antes) ...
        $usuario = User::findOrFail($id);
        $usuario->delete();
        return redirect()->route('configuracion.index')->with('success', 'Usuario eliminado.');
    }
}
