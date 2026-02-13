<?php

namespace App\Http\Controllers\ControllersBasicos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ModelosBasicos\Almacen;

class AlmacenController extends Controller
{
    public function index()
    {
        $almacenes = Almacen::all();
        return view('almacenes.index', compact('almacenes'));
    }

    public function create()
    {
        return view('almacenes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'almacen' => 'required|string|max:255|unique:almacenes,almacen',
        ]);

        Almacen::create($request->all());

        return redirect()->route('almacenes.index')->with('success', 'Almacén creado exitosamente.');
    }
    public function show(string $id)
    {
        $almacen = Almacen::with([
            'activos.modelo.marca',
            'activos.tipo',
            'activos.salud'
        ])->findOrFail($id);

        return view('almacenes.show', compact('almacen'));
    }

    public function edit(string $id)
    {
        $almacen = Almacen::findOrFail($id);
        return view('almacenes.edit', compact('almacen'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'almacen' => 'required|string|max:255|unique:almacenes,almacen,' . $id,
        ]);

        $almacen = Almacen::findOrFail($id);
        $almacen->update($request->all());

        return redirect()->route('almacenes.index')->with('success', 'Almacén actualizado exitosamente.');
    }

    public function destroy(string $id)
    {
        $almacen = Almacen::findOrFail($id);
        if ($almacen->activos()->count() > 0) {
            return back()->with('error', 'No se puede eliminar porque hay activos asociados a este almacén.');
        }
        $almacen->delete();
        return redirect()->route('almacenes.index')->with('success', 'Almacén eliminado exitosamente.');
    }
}
