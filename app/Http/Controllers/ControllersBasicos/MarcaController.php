<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    public function index()
    {
        $marcas = Marca::all();
        return view('marcas.index', compact('marcas'));
    }

    public function create()
    {
        return view('marcas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'marca' => 'required|unique:marcas,marca',
        ]);
        Marca::create($request->all());
        return redirect()->route('marcas.index')->with('success', 'Marca creada exitosamente.');
    }

    public function edit(Marca $marca)
    {
        return view('marcas.edit', compact('marca'));
    }

    public function update(Request $request, Marca $marca)
    {
        $request->validate([
            'marca' => 'required|unique:marcas,marca,' . $marca->id,
        ]);
        $marca->update($request->all());
        return redirect()->route('marcas.index')->with('success', 'Marca actualizada exitosamente.');
    }

    public function destroy(Marca $marca)
    {
        if ($marca->modelo()->count() > 0) {
            return back()->with('error', 'No se puede eliminar porque hay modelos asociados a esta marca.');
        }
        $marca->delete();
        return redirect()->route('marcas.index')->with('success', 'Marca eliminada exitosamente.');
    }
}
