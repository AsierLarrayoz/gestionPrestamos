<?php

namespace App\Http\Controllers\ControllersBasicos;

use App\Http\Controllers\Controller;
use App\Models\ModelosBasicos\Almacen;
use App\Models\ModelosBasicos\Lector;
use Illuminate\Http\Request;

class LectorController extends Controller
{
    public function index()
    {
        $lectores = Lector::with('almacen')->get();
        return view('lectores.index', compact('lectores'));
    }
    public function create()
    {
        $almacenes = Almacen::all();
        return view('lectores.create', compact('almacenes'));
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|unique:lectores,nombre|max:255',
            'identificador_unico' => 'required|integer|unique:lectores,identificador_unico|min:1',
            'almacen_id' => 'required|exists:almacenes,id',
            'tipo' => 'nullable|string|max:255'
        ]);

        Lector::create($validatedData);
        return redirect()->route('lectores.index')->with('success', 'Lector creado exitosamente.');
    }
    public function edit(string $id)
    {
        $almacenes = Almacen::all();
        $lector = Lector::findOrFail($id);
        return view('lectores.edit', compact('almacenes', 'lector'));
    }
    public function update(Request $request, string $id)
    {
        $lector = Lector::findOrFail($id);

        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255|unique:lectores,nombre,' . $lector->id,
            'identificador_unico' => 'required|string|min:1|unique:lectores,identificador_unico,' . $lector->id,
            'almacen_id' => 'required|exists:almacenes,id',
            'tipo' => 'nullable|string|max:255'
        ]);

        $lector->update($validatedData);
        return redirect()->route('lectores.index')->with('success', 'Lector actualizado correctamente.');
    }
    public function destroy(string $id)
    {
        $lector = Lector::findOrFail($id);
        $lector->delete();

        return redirect()->route('lectores.index')->with('success', 'Lector eliminado exitosamente.');
    }
}
