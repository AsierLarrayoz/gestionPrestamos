@extends('layout.layout')

@section('content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <h3 class="fw-bold">Inventario en: <span class="text-primary">{{ $almacen->almacen }}</span></h3>
        </div>
        <div class="card-toolbar">
            <a href="{{ route('almacenes.index') }}" class="btn btn-light-primary btn-sm">
                <i class="ki-outline ki-arrow-left fs-2"></i> Volver
            </a>
        </div>
    </div>

    <div class="card-body py-4">
        <div class="table-responsive">
            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                <thead>
                    <tr class="fw-bold text-muted bg-light">
                        <th class="ps-4 min-w-150px rounded-start">Identificadores (S/N - RFID)</th>
                        <th class="min-w-150px">Activo (Marca / Modelo)</th>
                        <th class="min-w-100px">Tipo</th>
                        <th class="min-w-100px">Estado de Salud</th>
                        <th class="min-w-100px text-end pe-4 rounded-end">Stock en Sede</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($almacen->activos as $activo)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex flex-column">
                                <span class="text-gray-900 fw-bold fs-6">{{ $activo->serial_number ?? 'S/N: No asignado' }}</span>
                                <span class="text-muted fw-semibold fs-7">RFID: {{ $activo->rfid_code ?? '---' }}</span>
                            </div>
                        </td>

                        <td>
                            <div class="d-flex flex-column">
                                <span class="text-gray-900 fw-bold fs-6">{{ $activo->modelo->modelo ?? 'Sin Modelo' }}</span>
                                <span class="text-muted fw-semibold fs-7">{{ $activo->modelo->marca->marca ?? 'Genérica' }}</span>
                            </div>
                        </td>

                        <td>
                            <span class="badge badge-light-info fw-bold">{{ $activo->tipo->tipo ?? 'N/A' }}</span>
                        </td>

                        <td>
                            <span class="badge badge-light-info fw-bold">{{ $activo->salud->salud ?? 'Desconocido' }}</span>
                        </td>

                        <td class="text-end pe-4">
                            <span class="text-gray-900 fw-bolder fs-5">{{ $activo->pivot->cantidad }}</span>
                            <span class="text-muted fs-7 ms-1">uds</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection