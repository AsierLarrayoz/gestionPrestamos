<?php

namespace App\Http\Controllers;

use App\Models\Salud;
use Illuminate\Http\Request;

class SaludController extends Controller
{
    public function index()
    {
        $saludes = Salud::all();
        return view('saludes.index', compact('saludes'));
    }

    public function create()
    {
        return view('saludes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'salud' => 'required|unique:salud|max:255',
        ]);

        Salud::create($request->all());

        return redirect()->route('saludes.index')->with('success', 'Estado de salud creado.');
    }

    public function edit(Salud $salud)
    {
        return view('saludes.edit', compact('salude'));
    }

    public function update(Request $request, Salud $salud)
    {
        $request->validate([
            'salud' => 'required|max:255|unique:salud,salud,' . $salud->id,
        ]);

        $salud->update($request->all());

        return redirect()->route('saludes.index')->with('success', 'Estado de salud actualizado.');
    }

    public function destroy(Salud $salud)
    {
        if ($salud->activo()->count() > 0) {
            return back()->with('error', 'No se puede eliminar porque hay activos asociados a este estado.');
        }

        $salud->delete();
        return redirect()->route('saludes.index')->with('success', 'Estado de salud eliminado.');
    }
}
