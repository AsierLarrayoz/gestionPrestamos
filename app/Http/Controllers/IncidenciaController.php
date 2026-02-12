<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Incidencia;
use App\Models\Activo;
use App\Models\Nivel;
use App\Models\Estado;
use App\Models\Prestamo;

class IncidenciaController extends Controller
{
    public function index()
    {
        $incidencias = Incidencia::with(['activo.modelo', 'usuario', 'estado', 'nivel'])->get();
        //Tendre que poner lo de paginate(10)
        return view('incidencias.index', compact('incidencias'));
    }

    public function create()
    {
        $activos = Activo::all();
        $niveles = Nivel::all();
        $estados = Estado::all();
        $prestamos = Prestamo::all();
        //Tendre que poner lo de paginate(10)
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

        $datos = array_merge($validatedData, ['user_id' => Auth::id()]);
        Incidencia::create($datos);
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

        $validatedData = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'estado_id' => 'nullable|exists:estados,id',
            'nivel_id' => 'required|exists:niveles,id',
        ]);

        $incidencia->update($validatedData);

        return redirect()->route('incidencias.index')->with('success', 'Incidencia actualizada.');
    }

    public function destroy(string $id)
    {
        $incidencia = Incidencia::findOrFail($id);
        $incidencia->delete();

        return redirect()->route('incidencias.index')->with('success', 'Incidencia eliminada.');
    }
}
