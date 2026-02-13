<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Prestamo;
use App\Models\Activo;
use App\Models\ModelosBasicos\Almacen;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PrestamoController extends Controller
{
    public function index()
    {
        $prestamosActivos = Prestamo::with(['activo.modelo', 'usuario'])
            ->whereNull('fecha_devuelto')
            ->get();
        //Tendre que poner lo de paginate(10)
        return view('prestamos.index', compact('prestamosActivos'));
    }
    public function historial()
    {
        $prestamosPasados = Prestamo::with(['activo.modelo', 'usuario'])
            ->whereNotNull('fecha_devuelto')
            ->orderBy('fecha_devuelto', 'desc')
            ->get();
        //Tendre que poner lo de paginate(10)
        return view('prestamos.historial', compact('prestamosPasados'));
    }

    public function create()
    {
        $almacenes = Almacen::all();
        return view('prestamos.create', compact('almacenes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required',
            'almacen_id' => 'required|exists:almacenes,id',
            'cantidad' => 'nullable|integer|min:1'
        ]);

        $codigo = $request->input('codigo');
        $almacenActual = $request->input('almacen_id');

        $activo = Activo::where('uuid', $codigo)
            ->orWhere('rfid_code', $codigo)
            ->first();

        if (!$activo) {
            return back()->with('error', 'Código no reconocido.');
        }

        $cantidadAccion = $activo->is_serialized ? 1 : ($request->input('cantidad', 1));

        $prestamoPendiente = Prestamo::where('activo_id', $activo->id)
            ->whereNull('fecha_devuelto')
            //->where('usuario_id', Auth::id())
            ->first();
        //DEVOLVER
        if ($prestamoPendiente) {
            if (!$activo->is_serialized && $cantidadAccion > $prestamoPendiente->cantidad_prestada) {
                return back()->with('error', "No puedes devolver $cantidadAccion unidades; solo hay $prestamoPendiente->cantidad_prestada prestadas.");
            }

            $prestamoPendiente->update([
                'fecha_devuelto' => Carbon::now(),
                'cantidad_devuelta' => $cantidadAccion,
                'almacen_devuelto_id' => $almacenActual
            ]);

            $activo->almacenes()->syncWithoutDetaching([
                $almacenActual => ['cantidad' => DB::raw("cantidad + $cantidadAccion")]
            ]);

            $activo->increment('cantidad', $cantidadAccion);

            $mensaje = $activo->is_serialized ? 'Devolución registrada.' : "Se han devuelto $cantidadAccion unidades.";
            return back()->with('success', $mensaje);
        }
        //PRESTAR
        $stockEnAlmacen = $activo->almacenes()
            ->where('almacen_id', $almacenActual)
            ->first();

        if (!$stockEnAlmacen || $stockEnAlmacen->pivot->cantidad < $cantidadAccion) {
            return back()->with('error', "Stock insuficiente. Disponible: " . ($stockEnAlmacen->pivot->cantidad ?? 0));
        }

        Prestamo::create([
            'fecha_prestado' => Carbon::now(),
            'activo_id' => $activo->id,
            'usuario_id' => Auth::id(),
            'almacen_prestado_id' => $almacenActual,
            'cantidad_prestada' => $cantidadAccion,
            'descripcion' => $request->descripcion
        ]);

        /*$activo->almacenes()->updateExistingPivot($almacenActual, [
            'cantidad' => $stockEnAlmacen->pivot->cantidad - $cantidadAccion
        ]);*/
        //Es mejor asi por que envia la orden directa a la base de datos y bloquea
        //la fila un instante para restar el numero
        $activo->almacenes()->updateExistingPivot($almacenActual, [
            'cantidad' => DB::raw("cantidad - $cantidadAccion")
        ]);

        $activo->decrement('cantidad', $cantidadAccion);

        return back()->with('success', 'Préstamo iniciado.');
    }
}
