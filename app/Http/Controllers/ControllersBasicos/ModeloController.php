<?php

namespace App\Http\Controllers\ControllersBasicos;

use App\Http\Controllers\Controller;

use App\Models\ModelosBasicos\Modelo;
use App\Models\ModelosBasicos\Marca;
use Illuminate\Http\Request;

class ModeloController extends Controller
{
    public function index()
    {
        $modelos = Modelo::with('marca')->get();
        return view('modelos.index', compact('modelos'));
    }
    public function create()
    {
        $marcas = Marca::all();
        return view('modelos.create', compact('marcas'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'modelo' => 'required|unique:modelos,modelo',
            'marca_id' => 'required|exists:marcas,id',
        ]);
        Modelo::create($request->all());
        return redirect()->route('modelos.index')->with('success', 'Modelo creado exitosamente.');
    }
    public function edit(Modelo $modelo)
    {
        $marcas = Marca::all();
        return view('modelos.edit', compact('modelo', 'marcas'));
    }
    public function update(Request $request, Modelo $modelo)
    {
        $request->validate([
            'modelo' => 'unique:modelos,modelo,' . $modelo->id,
            'marca_id' => 'required|exists:marcas,id',
        ]);
        $modelo->update($request->all());
        return redirect()->route('modelos.index')->with('success', 'Modelo actualizado exitosamente.');
    }
    public function destroy(Modelo $modelo)
    {
        $modelo->delete();
        return redirect()->route('modelos.index')->with('success', 'Modelo eliminado exitosamente.');
    }
}
