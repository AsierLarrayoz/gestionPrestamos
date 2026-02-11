<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Prestamo;
use App\Models\Activo;

class PrestamoController extends Controller
{
    public function index()
    {
        $prestamosActivos = Prestamo::whereNull('fecha_devuelto')->get();
        return view('prestamos.index', compact('prestamosActivos'));
    }

    public function historial()
    {
        $prestamosPasados = Prestamo::where('fecha_devuelto', '<=', now())->get();
        return view('prestamos.historial', compact('prestamosPasados'));
    }

    public function create()
    {
        return view('prestamos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required',
            'almacen_id' => 'required|exists:almacenes,id'
        ]);

        $codigo = $request->input('codigo');
        $almacenActual = $request->input('almacen_id');

        $activo = Activo::where('uuid', $codigo)
            ->orWhere('rfid_code', $codigo)
            ->firstOrFail();

        $prestamoPendiente = Prestamo::where('activo_id', $activo->id)
            ->whereNull('fecha_devuelto')
            ->first();

        if ($prestamoPendiente) {
            $prestamoPendiente->update([
                'fecha_devuelto' => now(),
                'cantidad_devuelta' => $prestamoPendiente->cantidad_prestada,
                'almacen_devuelto_id' => $almacenActual
            ]);

            $activo->update([
                'almacen_id' => $almacenActual
            ]);

            return back()->with('success', 'Devolución registrada');
        }

        Prestamo::create([
            'fecha_prestado' => now(),
            'cantidad_prestada' => 1,
            'activo_id' => $activo->id,
            'user_id' => Auth::id(),
            'almacen_prestado_id' => $activo->almacen_id,
            'descripcion' => $request->descripcion
        ]);

        return back()->with('success', 'Préstamo iniciado desde su almacén de origen.');
    }
}
