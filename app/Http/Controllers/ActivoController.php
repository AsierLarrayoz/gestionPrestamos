<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Activo;
use App\Models\Marca;
use App\Models\Salud;
use App\Models\Tipo;
use App\Models\Modelo;
use App\Models\Nivel;
use App\Models\Almacen;

class ActivoController extends Controller
{
    public function index()
    {
        $activos = Activo::with(['modelo.marca', 'tipo', 'salud', 'nivel'])->get();
        return view('activos.index', compact('activos'));
    }

    public function create()
    {
        $marcas = Marca::all();
        $salud = Salud::all();
        $tipos = Tipo::all();
        $niveles = Nivel::all();
        $almacenes = Almacen::all();

        return view('activos.create', compact('marcas', 'salud', 'tipos', 'niveles', 'almacenes'));
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
            'rfid' => 'nullable|string|max:255|unique:activos,rfid',
            'cantidad' => 'required|integer|min:1',
            'almacen_id' => 'required|exists:almacenes,id',
            'marca_id' => 'nullable|exists:marcas,id',
            'modelo_id' => 'nullable|exists:modelos,id',
            'tipo_id' => 'required|exists:tipos,id',
            'nivel_id' => 'required|exists:niveles,id',
            'salud_id' => 'required|exists:salud,id',
        ]);

        $datosActivo = $request->except(['almacen_id']);
        $uuid = Str::uuid()->toString();

        $datosGuardar = array_merge($datosActivo, ['uuid' => $uuid]);

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
        $niveles = Nivel::all();
        $almacenes = Almacen::all();
        $marcaId = $activo->modelo?->marca_id;
        $modelos = $marcaId ? Modelo::where('marca_id', $marcaId)->get() : collect();

        return view('activos.edit', compact('activo', 'marcas', 'modelos', 'tipos', 'niveles', 'salud'));
    }

    public function update(Request $request, string $id)
    {
        $activo = Activo::findOrFail($id);

        $validatedData = $request->validate([
            'serial_number' => 'nullable|string|max:255|unique:activos,serial_number,' . $activo->id,
            'rfid'  => 'nullable|string|max:255|unique:activos,rfid,' . $activo->id,
            'modelo_id' => 'nullable|exists:modelos,id',
            'tipo_id' => 'required|exists:tipos,id',
            'nivel_id' => 'required|exists:niveles,id',
            'salud_id' => 'required|exists:salud,id',
            'cantidad' => 'required|integer|min:0',
            'almacen_id' => 'required|exists:almacenes,id', // Almacén donde se aplicará el cambio
        ]);

        $cantidadAnterior = $activo->cantidad;
        $nuevaCantidad = $request->cantidad;
        $diferencia = $nuevaCantidad - $cantidadAnterior;
        $activo->update($validatedData);

        if ($diferencia != 0) {
            $almacenDestino = $activo->almacenes()->where('almacen_id', $request->almacen_id)->first();

            if ($almacenDestino) {
                $activo->almacenes()->updateExistingPivot($request->almacen_id, [
                    'cantidad' => $almacenDestino->pivot->cantidad + $diferencia
                ]);
            } else {
                if ($diferencia > 0) {
                    $activo->almacenes()->attach($request->almacen_id, ['cantidad' => $diferencia]);
                } else {
                    return back()->with('error', 'No puedes quitar stock de un almacén donde no hay existencias.');
                }
            }
        }

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
}
