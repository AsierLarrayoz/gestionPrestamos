<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Prestamo;
use App\Models\Activo;
use App\Models\ModelosBasicos\Almacen;
use App\Models\ModelosBasicos\Lector;
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
    public function createManual()
    {
        $almacenes = Almacen::all();

        // AÑADIMOS 'almacenes' AL WITH PARA SABER DÓNDE ESTÁN
        $activos = Activo::with(['modelo.marca', 'tipo', 'almacenes'])
            ->where('cantidad', '>', 0)
            ->get();

        $prestamosActivos = Prestamo::with(['activo.modelo.marca', 'activo.tipo', 'user'])
            ->whereNull('fecha_devuelto')
            ->orderBy('fecha_prestado', 'desc')
            ->get();

        return view('prestamos.create_manual', compact('almacenes', 'activos', 'prestamosActivos'));
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

        /*$prestamoPendiente = Prestamo::where('activo_id', $activo->id)
            ->whereNull('fecha_devuelto')
            ->first();*/
        $prestamosPendientes = Prestamo::where('activo_id', $activo->id)
            ->whereNull('fecha_devuelto')
            ->get();

        if ($request->has('accion_confirmada')) {
            return $this->ejecutarOperacion($request, $activo);
        }

        if ($activo->is_serialized) {
            $accion = $prestamosPendientes->isNotEmpty() ? 'devolver' : 'prestar';
            $request->merge([
                'accion_confirmada' => $accion,
                'cantidad_confirmada' => 1,
                // Si va a devolver, cogemos el ID del único préstamo que tiene
                'prestamo_id' => $prestamosPendientes->first()->id ?? null
            ]);
            return $this->ejecutarOperacion($request, $activo);
        }

        $stockAlmacen = $activo->almacenes()->where('almacen_id', $almacenId)->first()->pivot->cantidad ?? 0;
        $cantidadYaPrestada = $prestamosPendientes->sum('cantidad_prestada');

        return back()->with([
            'abrir_modal' => true,
            'activoOp' => $activo,
            'stockActual' => $stockAlmacen,
            'codigoActivo' => $codigo,
            'almacenActual' => $almacenId,
            'prestamosPendientes' => $prestamosPendientes, // Mandamos la lista entera a la vista
            'cantidadYaPrestada' => $cantidadYaPrestada
        ]);
    }

    private function ejecutarOperacion(Request $request, $activo)
    {
        $accion = $request->input('accion_confirmada');
        $cantidad = (int) $request->input('cantidad_confirmada');
        $almacenId = $request->input('almacen_id');

        // --- DEVOLUCIÓN ---
        if ($accion === 'devolver') {
            // Buscamos el préstamo exacto que el usuario seleccionó en el desplegable
            $prestamoPendiente = Prestamo::find($request->input('prestamo_id'));

            if (!$prestamoPendiente) {
                return back()->with('error', 'Error: No has seleccionado un préstamo válido para devolver.');
            }
            if ($cantidad > $prestamoPendiente->cantidad_prestada) {
                return back()->with('error', "No puedes devolver $cantidad. Ese lote solo tiene {$prestamoPendiente->cantidad_prestada} prestados.");
            }

            $diferencia = $prestamoPendiente->cantidad_prestada - $cantidad;

            // ... (AQUÍ VA EXACTAMENTE LA MISMA LÓGICA DE SPLIT/DIVISIÓN QUE YA TENÍAS ANTES) ...
            // (Pega aquí el bloque de "Devolución Total" y "Devolución Parcial" que hicimos en el paso anterior)

            // 1. Lógica de guardado del historial
            if ($diferencia == 0) {
                $prestamoPendiente->update(['fecha_devuelto' => Carbon::now(), 'cantidad_devuelta' => $cantidad, 'almacen_devuelto_id' => $almacenId]);
            } else {
                $tipoParcial = $request->input('tipo_devolucion_parcial', 'dividir');
                if ($tipoParcial === 'dividir') {
                    $prestamoPendiente->update(['cantidad_prestada' => $cantidad, 'cantidad_devuelta' => $cantidad, 'fecha_devuelto' => Carbon::now(), 'almacen_devuelto_id' => $almacenId]);
                    Prestamo::create(['fecha_prestado' => $prestamoPendiente->fecha_prestado, 'activo_id' => $activo->id, 'user_id' => $prestamoPendiente->user_id, 'almacen_prestado_id' => $prestamoPendiente->almacen_prestado_id, 'cantidad_prestada' => $diferencia, 'descripcion' => $prestamoPendiente->descripcion . ' (Resto pendiente)']);
                } else {
                    $prestamoPendiente->update(['cantidad_devuelta' => $cantidad, 'fecha_devuelto' => Carbon::now(), 'almacen_devuelto_id' => $almacenId]);
                }
            }

            // 2. Devolvemos Stock al Almacén
            $activo->almacenes()->syncWithoutDetaching([$almacenId => ['cantidad' => \Illuminate\Support\Facades\DB::raw("cantidad + $cantidad")]]);
            $activo->increment('cantidad', $cantidad);

            return back()->with('success', "Devolución procesada correctamente.");
        }

        // --- PRÉSTAMO ---
        if ($accion === 'prestar') {
            $stockActual = $activo->almacenes()->where('almacen_id', $almacenId)->first()->pivot->cantidad ?? 0;

            if ($stockActual < $cantidad) {
                return back()->with('error', "Stock insuficiente. Solo quedan $stockActual unidades.");
            }

            // YA NO SE SUMAN. SIEMPRE SE CREA UNO NUEVO.
            Prestamo::create([
                'fecha_prestado' => Carbon::now(),
                'activo_id' => $activo->id,
                'user_id' => Auth::id(),
                'almacen_prestado_id' => $almacenId,
                'cantidad_prestada' => $cantidad,
                'descripcion' => $request->input('descripcion') // Guardamos a quién o para qué es
            ]);

            $activo->almacenes()->updateExistingPivot($almacenId, ['cantidad' => \Illuminate\Support\Facades\DB::raw("cantidad - $cantidad")]);
            $activo->decrement('cantidad', $cantidad);

            return back()->with('success', "Préstamo de $cantidad unidades realizado.");
        }


        return back()->with('error', 'Acción no reconocida.');
    }
    public function apiLectura(Request $request)
    {
        $lectorId = $request->input('reader_id');

        $codigo = $request->input('qr') ?? $request->input('rfid');

        if (!$lectorId || !$codigo) {
            return response()->json([
                'status' => 'KO',
                'msg' => 'Faltan datos: reader_id o codigo (qr/rfid)'
            ]);
        }

        $lector = Lector::where('identificador_unico', $lectorId)->first();
        if (!$lector) {
            return response()->json(['status' => 'KO', 'msg' => 'Lector no reconocido']);
        }

        $activo = Activo::with(['modelo', 'tipo'])
            ->where('rfid_code', $codigo)
            ->orWhere('uuid', $codigo)
            ->orWhere('serial_number', $codigo)
            ->first();

        if (!$activo) {
            return response()->json(['status' => 'KO', 'msg' => 'Activo no encontrado']);
        }

        $prestamoPendiente = Prestamo::where('activo_id', $activo->id)
            ->whereNull('fecha_devuelto')
            ->first();

        $systemUserId = 4;

        try {
            DB::beginTransaction();

            if ($prestamoPendiente) {
                // --- DEVOLUCIÓN ---
                $prestamoPendiente->update([
                    'fecha_devuelto' => Carbon::now(),
                    'cantidad_devuelta' => 1,
                    'almacen_devuelto_id' => $lector->almacen_id
                ]);

                $activo->almacenes()->syncWithoutDetaching([
                    $lector->almacen_id => ['cantidad' => DB::raw("cantidad + 1")]
                ]);
                $activo->increment('cantidad', 1);

                DB::commit();
                return response()->json([
                    'status' => 'OK',
                    'msg' => 'Devolucion: ' . ($activo->tipo->tipo ?? 'Item') . ' ' . ($activo->modelo->modelo ?? '')
                ]);
            } else {
                // --- PRÉSTAMO ---
                $stockLector = $activo->almacenes()->where('almacen_id', $lector->almacen_id)->first()->pivot->cantidad ?? 0;

                if ($stockLector < 1) {
                    return response()->json(['status' => 'KO', 'msg' => 'El activo no esta en este almacen']);
                }

                Prestamo::create([
                    'fecha_prestado' => Carbon::now(),
                    'activo_id' => $activo->id,
                    'user_id' => $systemUserId,
                    'almacen_prestado_id' => $lector->almacen_id,
                    'cantidad_prestada' => 1,
                    'descripcion' => 'Movimiento automatico Lector: ' . $lector->nombre
                ]);

                $activo->almacenes()->updateExistingPivot($lector->almacen_id, [
                    'cantidad' => DB::raw("cantidad - 1")
                ]);
                $activo->decrement('cantidad', 1);

                DB::commit();
                return response()->json([
                    'status' => 'OK',
                    'msg' => 'Prestamo: ' . ($activo->tipo->tipo ?? 'Item') . ' ' . ($activo->modelo->modelo ?? '')
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'KO', 'msg' => 'Error DB: ' . $e->getMessage()]);
        }
    }
}
