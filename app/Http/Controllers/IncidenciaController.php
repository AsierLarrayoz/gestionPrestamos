<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incidencia;

class IncidenciaController extends Controller
{
    public function index()
    {
        $incidencias = Incidencia::all();
        return view('incidencias.index', compact($incidencias));
    }

    public function create()
    {
        return view('incidencias.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'descripcion' => 'nullable|string',
            'fecha_incidencia' => 'required|date',
            'estado_id' => 'nullable|exists:estados,id',
            'nivel_id' => 'nullable|exists:niveles,id',
            'user_id' => 'required|exists:users,id',
            'activo_id' => 'required|exists:activos,id',
            'prestamo_id' => 'nullable|exists:prestamos,id',
        ]);

        Incidencia::create($validatedData);
        return redirect()->back()->with('success', 'Incidencia creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
