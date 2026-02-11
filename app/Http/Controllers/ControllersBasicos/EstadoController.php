<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estado;

class EstadoController extends Controller
{
    public function index()
    {
        $estados = Estado::all();
        return view('estados.index', compact('estados'));
    }

    public function create()
    {
        return view('estados.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'estado' => 'required|unique:estados,estado',
        ]);
        Estado::create($request->all());
        return redirect()->route('estados.index')->with('success', 'Estado creado exitosamente.');
    }

    public function edit(Estado $estado)
    {
        return view('estados.edit', compact('estado'));
    }

    public function update(Request $request, Estado $estado)
    {
        $request->validate([
            'estado' => 'required|unique:estados,estado,' . $estado->id,
        ]);
        $estado->update($request->all());
        return redirect()->route('estados.index')->with('success', 'Estado actualizado exitosamente.');
    }

    public function destroy(Estado $estado)
    {
        if ($estado->activo()->count() > 0) {
            return back()->with('error', 'No se puede eliminar porque hay activos asociados a este estado.');
        }
        $estado->delete();
        return redirect()->route('estados.index')->with('success', 'Estado eliminado exitosamente.');
    }
}
