<?php

namespace App\Http\Controllers\ControllersBasicos;

use App\Http\Controllers\Controller;

use App\Models\ModelosBasicos\Tipo;
use Illuminate\Http\Request;

class TipoController extends Controller
{
    public function index()
    {
        return view('tipos.index');
    }

    public function create()
    {
        return view('tipos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo' => 'required|string|max:255',
        ]);

        Tipo::create($request->all());

        return redirect()->route('tipos.index')->with('success', 'Tipo creado exitosamente.');
    }

    public function edit(Tipo $tipo)
    {
        return view('tipos.edit', compact('tipo'));
    }

    public function update(Request $request, Tipo $tipo)
    {
        $request->validate([
            'tipo' => 'required|string|max:255|unique:tipos,tipo,' . $tipo->id,
        ]);

        $tipo->update($request->all());

        return redirect()->route('tipos.index')->with('success', 'Tipo actualizado exitosamente.');
    }

    public function destroy(Tipo $tipo)
    {
        if ($tipo->activo()->count() > 0) {
            return back()->with('error', 'No se puede eliminar porque hay activos asociados a este tipo.');
        }

        $tipo->delete();
        return redirect()->route('tipos.index')->with('success', 'Tipo eliminado exitosamente.');
    }
}
