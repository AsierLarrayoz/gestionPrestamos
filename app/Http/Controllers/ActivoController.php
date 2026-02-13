<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Activo;
use App\Models\ModelosBasicos\Marca;
use App\Models\ModelosBasicos\Salud;
use App\Models\ModelosBasicos\Tipo;
use App\Models\ModelosBasicos\Modelo;
use App\Models\ModelosBasicos\Almacen;

class ActivoController extends Controller
{
    public function index()
    {
        $activos = Activo::with(['modelo.marca', 'tipo', 'salud'])->get();
        return view('activos.index', compact('activos'));
    }

    public function create()
    {
        $marcas = Marca::all();
        $salud = Salud::all();
        $tipos = Tipo::all();
        $almacenes = Almacen::all();
        //Tendre que poner lo de paginate(10)
        return view('activos.create', compact('marcas', 'salud', 'tipos', 'almacenes'));
    }

    public function getModelosByMarca($id)
    {
        $modelos = Modelo::where('marca_id', $id)->get();
        return response()->json($modelos);
    }

    public function store(Request $request)
    {
        $request->validate([
            'serial_number' => 'nullable|string|max:255|unique:activos,serial_number',
            'rfid_code' => 'nullable|string|max:255|unique:activos,rfid_code',
            'cantidad' => 'required|integer|min:1',
            'almacen_id' => 'required|exists:almacenes,id',
            'marca_id' => 'nullable|exists:marcas,id',
            'modelo_id' => 'nullable|exists:modelos,id',
            'tipo_id' => 'required|exists:tipos,id',
            'salud_id' => 'required|exists:salud,id',
        ]);
        if (!empty($request->serial_number)) {
            $is_serialized = true;
            $cantidadFinal = 1;
        } else {
            $is_serialized = false;
            $cantidadFinal = $request->cantidad;
        }

        $datosActivo = $request->except(['almacen_id', 'cantidad']);
        $uuid = Str::uuid()->toString();

        $datosGuardar = array_merge($datosActivo, [
            'uuid'          => $uuid,
            'is_serialized' => $is_serialized,
            'cantidad'      => $cantidadFinal
        ]);

        $activo = Activo::create($datosGuardar);

        $activo->almacenes()->attach($request->almacen_id, [
            'cantidad' => $request->cantidad
        ]);

        return redirect()->route('activos.index')->with('success', 'Activo creado exitosamente.');
    }

    public function show(string $id)
    {
        $activo = Activo::findOrFail($id);
        return view('activos.show', compact('activo'));
    }

    public function edit(string $id)
    {
        $activo = Activo::with('modelo')->findOrFail($id); // Cargamos el modelo
        $marcas = Marca::all();
        $salud = Salud::all();
        $tipos = Tipo::all();
        $almacenes = Almacen::all();
        $marcaId = $activo->modelo?->marca_id;
        $modelos = $marcaId ? Modelo::where('marca_id', $marcaId)->get() : collect();

        return view('activos.edit', compact('activo', 'marcas', 'modelos', 'tipos', 'salud', 'almacenes'));
    }

    public function update(Request $request, string $id)
    {
        $activo = Activo::findOrFail($id);

        $validatedData = $request->validate([
            'rfid_code'     => 'nullable|string|max:255|unique:activos,rfid_code,' . $activo->id,
            'modelo_id'     => 'nullable|exists:modelos,id',
            'tipo_id'       => 'required|exists:tipos,id',
            'salud_id'      => 'required|exists:salud,id',
            'cantidad'      => 'required|integer|min:0',
            'almacen_id'    => 'required|exists:almacenes,id',
        ]);
        if ($activo->serial_number) {
            $validatedData['serial_number'] = $activo->serial_number;
        }

        $nuevaCantAlmacen = !empty($request->serial_number) ? 1 : $request->cantidad;

        $almacenDestino = $activo->almacenes()->where('almacen_id', $request->almacen_id)->first();

        if ($almacenDestino) {
            $activo->almacenes()->updateExistingPivot($request->almacen_id, [
                'cantidad' => $nuevaCantAlmacen
            ]);
        } else {
            $activo->almacenes()->attach($request->almacen_id, ['cantidad' => $nuevaCantAlmacen]);
        }

        $activo->load('almacenes');
        $totalRealGlobal = $activo->almacenes->sum('pivot.cantidad');

        $activo->update(array_merge($validatedData, [
            'cantidad'      => $totalRealGlobal,
            'is_serialized' => !empty($request->serial_number)
        ]));

        return redirect()->route('activos.index')->with('success', 'Activo y stock actualizados correctamente.');
    }

    public function destroy(string $id)
    {
        $activo = Activo::findOrFail($id);

        if ($activo->prestamos()->exists()) {
            return back()->with('error', 'No se puede eliminar: Este activo aparece en el historial de préstamos.');
        }

        if ($activo->incidencias()->exists()) {
            return back()->with('error', 'No se puede eliminar: Este activo tiene incidencias registradas.');
        }

        $activo->almacenes()->detach();
        $activo->delete();

        return redirect()->route('activos.index')->with('success', 'Activo eliminado exitosamente.');
    }
    // app/Http/Controllers/ActivoController.php

    public function quickStoreMarca(Request $request)
    {
        $request->validate(['marca' => 'required|string|unique:marcas,marca']);
        $marca = \App\Models\ModelosBasicos\Marca::create($request->all());
        return response()->json($marca);
    }

    public function quickStoreModelo(Request $request)
    {
        $request->validate([
            'modelo' => 'required|string',
            'marca_id' => 'required|exists:marcas,id'
        ]);
        $modelo = \App\Models\ModelosBasicos\Modelo::create($request->all());
        return response()->json($modelo);
    }

    public function quickStoreTipo(Request $request)
    {
        $request->validate(['tipo' => 'required|string|unique:tipos,tipo']);
        $tipo = \App\Models\ModelosBasicos\Tipo::create($request->all());
        return response()->json($tipo);
    }

    public function quickStoreSalud(Request $request)
    {
        $request->validate(['salud' => 'required|string|unique:salud,salud']);
        $salud = \App\Models\ModelosBasicos\Salud::create($request->all());
        return response()->json($salud);
    }
}
