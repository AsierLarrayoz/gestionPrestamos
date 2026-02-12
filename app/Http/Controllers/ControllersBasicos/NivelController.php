<?php

namespace App\Http\Controllers\ControllersBasicos;

use App\Http\Controllers\Controller;

use App\Models\ModelosBasicos\Nivel;
use Illuminate\Http\Request;

class NivelController extends Controller
{
    public function index()
    {
        return view('niveles.index');
    }

    public function create()
    {
        return view('niveles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nivel' => 'required|unique:niveles,nivel',
        ]);
        Nivel::create($request->all());
        return redirect()->route('niveles.index')->with('success', 'Nivel creado exitosamente.');
    }

    public function edit(Nivel $nivel)
    {
        return view('niveles.edit', compact('nivel'));
    }

    public function update(Request $request, Nivel $nivel)
    {
        $request->validate([
            'nivel' => 'required|unique:niveles,nivel,' . $nivel->id,
        ]);
        $nivel->update($request->all());
        return redirect()->route('niveles.index')->with('success', 'Nivel actualizado exitosamente.');
    }

    public function destroy(Nivel $nivel)
    {
        if ($nivel->activo()->count() > 0) {
            return back()->with('error', 'No se puede eliminar porque hay activos asociados a este nivel.');
        }
        $nivel->delete();
        return redirect()->route('niveles.index')->with('success', 'Nivel eliminado exitosamente.');
    }
}
