<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activo;

class ActivoController extends Controller
{
    public function index()
    {
        $activos = Activo::all();
        return view('activos.index', compact('activos'));
    }

    public function create()
    {
        return view('activos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'marca_id' => 'required|exists:marcas,id',
            'modelo_id' => 'required|exists:modelos,id',
            'tipo_id' => 'required|exists:tipos,id',
            'nivel_id' => 'required|exists:niveles,id',
            'salud_id' => 'required|exists:salud,id',
        ]);

        Activo::create($request->all());
        return redirect()->route('activos.index')->with('success', 'Activo creado exitosamente.');
    }

    public function show(string $id)
    {
        //Aqui si acaso se muestra el detalle del activo
    }

    public function edit(string $id)
    {
        $activo = Activo::findOrFail($id);
        return view('activos.edit', compact('activo'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nombre' => 'required',
            'marca_id' => 'required|exists:marcas,id',
            'modelo_id' => 'required|exists:modelos,id',
            'tipo_id' => 'required|exists:tipos,id',
            'nivel_id' => 'required|exists:niveles,id',
            'salud_id' => 'required|exists:salud,id',
        ]);

        $activo = Activo::findOrFail($id);
        $activo->update($request->all());

        return redirect()->route('activos.index')->with('success', 'Activo actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $activo = Activo::findOrFail($id);
        $activo->delete();
        return redirect()->route('activos.index')->with('success', 'Activo eliminado exitosamente.');
    }
}
