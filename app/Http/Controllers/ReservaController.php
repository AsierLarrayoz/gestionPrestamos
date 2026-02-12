<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\Tipo;
use App\Models\Activo;
use Carbon\Carbon;

use function Symfony\Component\Clock\now;

class ReservaController extends Controller
{
    public function indexReservasActivas()
    {
        $reservasActivas = Reserva::with(['activo', 'usuario'])
            ->where('fecha_fin', '>=', Carbon::now())
            ->orderBy('fecha_inicio', 'asc')
            ->get();

        return view('reservas.index', compact('reservasActivas'));
    }
    public function indexReservasInactivas()
    {
        $reservasInactivas = Reserva::with(['activo', 'usuario'])
            ->where('fecha_fin', '<', Carbon::now())
            ->orderBy('fecha_fin', 'desc')
            ->get();

        return view('reservas.index', compact('reservasInactivas'));
    }
    public function create()
    {
        $tipos = Tipo::all();
        $activos = Activo::all();
        return view('reservas.create', compact('tipos', 'activos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'activo_id' => 'required|exists:activos,id',
            'fecha_inicio' => 'required|date|after_or_equal:today',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'cantidad' => 'required|integer|min:1',
        ]);

        Reserva::create([
            'activo_id' => $request->activo_id,
            'usuario_id' => Auth::id(),
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'cantidad' => $request->cantidad,
        ]);

        return redirect()->route('reservas.index_activas')->with('success', 'Reserva creada exitosamente.');
    }

    public function show(string $id)
    {
        $reserva = Reserva::with(['activo', 'usuario'])->findOrFail($id);
        return view('reservas.show', compact('reserva'));
    }

    public function edit(string $id)
    {
        $reserva = Reserva::findOrFail($id);
        $activos = Activo::all();
        return view('reservas.edit', compact('reserva', 'activos'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'activo_id' => 'required|exists:activos,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'cantidad' => 'required|integer|min:1',
        ]);

        $reserva = Reserva::findOrFail($id);
        $reserva->update($request->all());

        return redirect()->route('reservas.index_activas')->with('success', 'Reserva actualizada.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $reserva = Reserva::findOrFail($id);
        $reserva->delete();

        return redirect()->route('reservas.index_activas')->with('success', 'Reserva eliminada.');
    }
}
