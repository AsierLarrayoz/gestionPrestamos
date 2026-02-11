<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Incidencia;
use App\Models\Activo;
use App\Models\Nivel;
use App\Models\Estado;

class IncidenciaController extends Controller
{
    public function index()
    {
        $incidencias = Incidencia::all();
        return view('incidencias.index', compact('incidencias'));
    }

    public function create()
    {
        $activos = Activo::all();
        $niveles = Nivel::all();
        $estados = Estado::all();

        return view('incidencias.create', compact('activos', 'niveles', 'estados'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_incidencia' => 'required|date',
            'estado_id' => 'nullable|exists:estados,id',
            'nivel_id' => 'nullable|exists:niveles,id',
            'activo_id' => 'required|exists:activos,id',
            'prestamo_id' => 'nullable|exists:prestamos,id',
        ]);

        $incidencia = [
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'fecha_incidencia' => $request->fecha_incidencia,
            'estado_id' => $request->estado_id,
            'activo_id' => $request->activo_id,
            'prestamo_id' => $request->prestamo_id,
            'user_id' => Auth::id(),
            'nivel_id' => $request->nivel_id
        ];
        Incidencia::create($incidencia);


        return redirect()->route('incidencias.index')->with('success', 'Incidencia reportada exitosamente.');
    }

    public function show(string $id)
    {
        $incidencia = Incidencia::findOrFail($id);
        return view('incidencias.show', compact('incidencia'));
    }

    public function edit(string $id)
    {
        $incidencia = Incidencia::findOrFail($id);
        $activos = Activo::all();
        $niveles = Nivel::all();
        $estados = Estado::all();

        return view('incidencias.edit', compact('incidencia', 'activos', 'niveles', 'estados'));
    }

    public function update(Request $request, string $id)
    {
        $incidencia = Incidencia::findOrFail($id);

        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'estado_id' => 'nullable|exists:estados,id',
            'nivel_id' => 'required|exists:niveles,id',
        ]);

        $incidencia->update($request->all());

        return redirect()->route('incidencias.index')->with('success', 'Incidencia actualizada.');
    }

    public function destroy(string $id)
    {
        $incidencia = Incidencia::findOrFail($id);
        $incidencia->delete();

        return redirect()->route('incidencias.index')->with('success', 'Incidencia eliminada.');
    }
}
