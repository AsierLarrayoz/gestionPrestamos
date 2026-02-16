@extends('layout.layout')

@section('content')

@if(session('warning'))
<div class="alert alert-dismissible bg-light-warning d-flex flex-column flex-sm-row p-5 mb-10">
    <i class="ki-outline ki-information-5 fs-2hx text-warning me-4 mb-5 mb-sm-0"></i>
    <div class="d-flex flex-column text-start pe-0 pe-sm-10">
        <h4 class="fw-semibold">Atención con el Stock</h4>
        <span>{{ session('warning') }}</span>
    </div>
    <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
        <i class="ki-outline ki-cross fs-1 text-warning"></i>
    </button>
</div>
@endif

<div class="card card-flush">
    <div class="card-header align-items-center py-5 gap-2 gap-md-5">
        <div class="card-title">
            <h3 class="fw-bold">Calendario de Reservas</h3>
        </div>
        <div class="card-toolbar">
            <a href="{{ route('reservas.create') }}" class="btn btn-primary">
                <i class="ki-outline ki-plus fs-2"></i> Nueva Reserva
            </a>
        </div>
    </div>

    <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                        <th>Material</th>
                        <th>Solicitante</th>
                        <th>Fechas</th>
                        <th>Cantidad</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody class="fw-semibold text-gray-600">
                    @forelse($reservas as $reserva)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                    <div class="symbol-label fs-3 bg-light-info text-info">
                                        {{ substr($reserva->tipo->tipo ?? 'R', 0, 1) }}
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="text-gray-800 fw-bold mb-1">{{ $reserva->tipo->tipo ?? 'Tipo borrado' }}</span>
                                    <span class="text-muted fs-7">{{ $reserva->descripcion }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="fw-bold">{{ $reserva->user->name ?? 'Usuario' }}</span>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="badge badge-light-primary mb-1">
                                    In: {{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d/m/Y H:i') }}
                                </span>
                                <span class="badge badge-light-danger">
                                    Out: {{ \Carbon\Carbon::parse($reserva->fecha_fin)->format('d/m/Y H:i') }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-secondary fs-6 fw-bold">{{ $reserva->cantidad }} uds</span>
                        </td>
                        <td class="text-end">
                            <form action="{{ route('reservas.destroy', $reserva->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm">
                                    <i class="ki-outline ki-trash fs-2"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">No hay reservas próximas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection