<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ModelosBasicos\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $usuarios = User::with('rol')->get();
        return view('configuracion.index', compact('usuarios'));
    }

    public function create()
    {
        $roles = Rol::all(); // Pasamos los roles para el select
        return view('configuracion.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'rol_id' => ['required', 'exists:roles,id'], // Validamos que el rol exista
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol_id' => $request->rol_id,
        ]);

        return redirect()->route('configuracion.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(string $id)
    {
        $usuario = User::findOrFail($id);
        $roles = Rol::all();
        return view('configuracion.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, string $id)
    {
        $usuario = User::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $usuario->id],
            'rol_id' => ['required', 'exists:roles,id'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $usuario->name = $request->name;
        $usuario->email = $request->email;
        $usuario->rol_id = $request->rol_id;

        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }

        $usuario->save();

        return redirect()->route('configuracion.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        if (Auth::id() == $id) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta mientras estás conectado.');
        }

        $usuario = User::findOrFail($id);

        if ($usuario->prestamos()->whereNull('fecha_devuelto')->exists()) {
            return back()->with('error', 'Este usuario tiene préstamos pendientes.');
        }


        $usuario->delete();

        return redirect()->route('configuracion.index')->with('success', 'Usuario eliminado correctamente.');
    }
}
