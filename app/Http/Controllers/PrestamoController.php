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
        $prestamosActivos = Prestamo::with([
            'activo.modelo',
            'activo.modelo.marca',
            'activo.tipo',
            'user',
            'almacenPrestado'
        ])
            ->whereNull('fecha_devuelto')
            ->get();
        return view('prestamos.index', compact('prestamosActivos'));
    }
    public function historial()
    {
        $prestamosPasados = Prestamo::with(['activo.modelo', 'user'])
            ->whereNotNull('fecha_devuelto')
            ->orderBy('fecha_devuelto', 'desc')
            ->get();
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
        ]);

        $codigo = $request->input('codigo');
        $almacenId = $request->input('almacen_id');

        $activo = Activo::where('uuid', $codigo)
            ->orWhere('rfid_code', $codigo)
            ->first();

        if (!$activo) {
            return back()->with('error', 'Código no reconocido.');
        }

        $prestamoPendiente = Prestamo::where('activo_id', $activo->id)
            ->whereNull('fecha_devuelto')
            ->first();

        if ($request->has('accion_confirmada')) {
            return $this->ejecutarOperacion($request, $activo, $prestamoPendiente);
        }

        if ($activo->is_serialized) {
            $accion = $prestamoPendiente ? 'devolver' : 'prestar';
            //Esta linea 
            $request->merge(['accion_confirmada' => $accion, 'cantidad_confirmada' => 1]);
            return $this->ejecutarOperacion($request, $activo, $prestamoPendiente);
        }
        $stockAlmacen = $activo->almacenes()->where('almacen_id', $almacenId)->first()->pivot->cantidad ?? 0;
        $cantidadYaPrestada = $prestamoPendiente ? $prestamoPendiente->cantidad_prestada : 0;

        return back()->with([
            'abrir_modal' => true,
            'activoOp' => $activo,
            'stockActual' => $stockAlmacen,
            'codigoActivo' => $codigo,
            'almacenActual' => $almacenId,
            'prestamoExistente' => $prestamoPendiente,
            'cantidadYaPrestada' => $cantidadYaPrestada
        ]);
    }

    /**
     * Función privada para realizar la lógica de BD y no repetir código
     */
    private function ejecutarOperacion(Request $request, $activo, $prestamoPendiente)
    {
        $accion = $request->input('accion_confirmada');
        $cantidad = (int) $request->input('cantidad_confirmada');
        $almacenId = $request->input('almacen_id');

        //DEVOLUCIÓN
        if ($accion === 'devolver') {
            if (!$prestamoPendiente) {
                return back()->with('error', 'Error: No hay préstamo activo para devolver.');
            }
            if ($cantidad > $prestamoPendiente->cantidad_prestada) {
                return back()->with('error', "No puedes devolver $cantidad. Solo tienes {$prestamoPendiente->cantidad_prestada} prestados.");
            }

            // 1. Devolucion total o parcial
            if ($cantidad == $prestamoPendiente->cantidad_prestada) {
                // Devolución Total
                $prestamoPendiente->update([
                    'fecha_devuelto' => Carbon::now(),
                    'cantidad_devuelta' => $cantidad,
                    'almacen_devuelto_id' => $almacenId
                ]);
            } else {
                //Devolucion parcial y actualizo cantidad del prestamo
                $prestamoPendiente->decrement('cantidad_prestada', $cantidad);
            }

            // 2. Devolvemos Stock al Almacén
            $activo->almacenes()->syncWithoutDetaching([
                $almacenId => ['cantidad' => DB::raw("cantidad + $cantidad")]
            ]);
            $activo->increment('cantidad', $cantidad);

            return back()->with('success', "Devolución de $cantidad unidades procesada.");
        }

        // --- LÓGICA DE PRÉSTAMO ---
        if ($accion === 'prestar') {
            $stockActual = $activo->almacenes()->where('almacen_id', $almacenId)->first()->pivot->cantidad ?? 0;

            if ($stockActual < $cantidad) {
                return back()->with('error', "Stock insuficiente. Solo quedan $stockActual unidades.");
            }

            // Crear prestamo o aumentar un prestamo
            if ($prestamoPendiente) {
                // Si ya existe uno abierto, le sumamos cantidad
                $prestamoPendiente->increment('cantidad_prestada', $cantidad);
            } else {
                Prestamo::create([
                    'fecha_prestado' => Carbon::now(),
                    'activo_id' => $activo->id,
                    'user_id' => Auth::id(),
                    'almacen_prestado_id' => $almacenId,
                    'cantidad_prestada' => $cantidad,
                    'descripcion' => $request->descripcion
                ]);
            }
            $activo->almacenes()->updateExistingPivot($almacenId, [
                'cantidad' => DB::raw("cantidad - $cantidad")
            ]);
            $activo->decrement('cantidad', $cantidad);

            return back()->with('success', "Préstamo de $cantidad unidades realizado.");
        }

        return back()->with('error', 'Acción no reconocida.');
    }
}
