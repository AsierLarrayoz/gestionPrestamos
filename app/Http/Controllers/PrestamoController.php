<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prestamo;
use App\Models\Activo;

class PrestamoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
            'codigo' => 'required'
        ]);

        $codigo = $request->input('codigo');

        $activo = Activo::where('uuid', $codigo)
            ->orWhere('rfid_code', $codigo)
            ->first();

        if (!$activo) {
            return back()->with('error', 'El código escaneado no existe en el sistema.');
        }

        $prestamoPendiente = Prestamo::where('activo_id', $activo->id)
            ->whereNull('fecha_devuelto')
            ->first();

        if ($prestamoPendiente) {
            $prestamoPendiente->update([
                'fecha_devuelto' => now(),
                'cantidad_devuelta' => $prestamoPendiente->cantidad_prestada
            ]);

            return back()->with('success', 'Devolución registrada: ' . $activo->modelo->modelo);
        }

        Prestamo::create([
            'fecha_prestado' => now(),
            'cantidad_prestada' => 1,
            'activo_id' => $activo->id,
            'user_id' => auth()->id(),
            'descripcion' => $request->descripcion
            //Aqui algo de almacen
        ]);

        return back()->with('success', 'Préstamo registrado: ' . $activo->modelo->modelo);
    }
}
