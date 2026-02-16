<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ModelosBasicos\Permiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $usuarios = User::with('permisos')->get();
        return view('configuracion.index', compact('usuarios'));
    }

    public function create()
    {
        $permisos = Permiso::all();
        return view('configuracion.create', compact('permisos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        $nuevoPerfil = Permiso::create([
            'permiso_usuarios'    => $request->has('permiso_usuarios'),
            'permiso_activos'     => $request->has('permiso_activos'),
            'permiso_almacenes'   => $request->has('permiso_almacenes'),
            'permiso_incidencias' => $request->has('permiso_incidencias'),
            'permiso_prestamos'   => $request->has('permiso_prestamos'),
        ]);
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'permisos_id' => $nuevoPerfil->id,
        ]);

        return redirect()->route('configuracion.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(string $id)
    {
        $usuario = User::findOrFail($id);
        $permisos = Permiso::all();
        return view('configuracion.edit', compact('usuario', 'permisos'));
    }

    public function update(Request $request, string $id)
    {
        $usuario = User::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $usuario->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $usuario->name = $request->name;
        $usuario->email = $request->email;

        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }

        $usuario->save();
        if ($usuario->permisos) {
            $permisoUsuarios = $request->has('permiso_usuarios');
            if (Auth::id() == $usuario->id) {
                $permisoUsuarios = true;
            }
            $usuario->permisos->update([
                'permiso_usuarios'    => $permisoUsuarios,
                'permiso_activos'     => $request->has('permiso_activos'),
                'permiso_almacenes'   => $request->has('permiso_almacenes'),
                'permiso_incidencias' => $request->has('permiso_incidencias'),
                'permiso_prestamos'   => $request->has('permiso_prestamos'),
            ]);
        }

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
