<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\ModelosBasicos\Tipo;
use App\Models\Activo;
use Carbon\Carbon;

use function Symfony\Component\Clock\now;

class ReservaController extends Controller
{
    public function index()
    {
        $reservas = Reserva::with(['tipo', 'user'])
            ->where('fecha_fin', '>=', Carbon::now())
            ->orderBy('fecha_inicio', 'asc')
            ->get();

        return view('reservas.index', compact('reservas'));
    }
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
        return view('reservas.create', compact('tipos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo_id' => 'required|exists:tipos,id',
            'fecha_inicio' => 'required|date|after_or_equal:today',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'cantidad' => 'required|integer|min:1',
        ]);

        $stockTotal = Activo::where('tipo_id', $request->tipo_id)->sum('cantidad');

        $cantidadYaReservada = Reserva::where('tipo_id', $request->tipo_id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('fecha_inicio', [$request->fecha_inicio, $request->fecha_fin])
                    ->orWhereBetween('fecha_fin', [$request->fecha_inicio, $request->fecha_fin])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('fecha_inicio', '<', $request->fecha_inicio)
                            ->where('fecha_fin', '>', $request->fecha_fin);
                    });
            })
            ->sum('cantidad');

        $demandaTotal = $cantidadYaReservada + $request->cantidad;

        Reserva::create([
            'tipo_id' => $request->tipo_id,
            'user_id' => Auth::id(),
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'cantidad' => $request->cantidad,
            'descripcion' => $request->descripcion
        ]);

        if ($demandaTotal > $stockTotal) {
            $falta = $demandaTotal - $stockTotal;
            return redirect()->route('reservas.index')->with('warning', "Reserva guardada, pero ¡CUIDADO! Hay overbooking. Faltarán aproximadamente $falta unidades para esas fechas.");
        }

        return redirect()->route('reservas.index')->with('success', 'Reserva planificada correctamente. Hay stock suficiente.');
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
