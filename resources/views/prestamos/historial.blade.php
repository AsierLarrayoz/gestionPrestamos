@extends('layout.layout')

@section('content')
<div class="card card-flush">
    <div class="card-header align-items-center py-5 gap-2 gap-md-5">
        <div class="card-title">
            <h3 class="fw-bold me-3">Historial de Devoluciones</h3>
            <div class="d-flex align-items-center position-relative my-1">
                <i class="ki-outline ki-magnifier fs-3 position-absolute ms-4"></i>
                <input type="text" id="buscador_historial" class="form-control form-control-solid w-250px ps-12" placeholder="Buscar usuario, activo..." />
            </div>
        </div>
        <div class="card-toolbar">
            <a href="{{ route('prestamos.index') }}" class="btn btn-light-primary">
                <i class="ki-outline ki-arrow-left fs-2"></i> Volver a Activos
            </a>
        </div>
    </div>

    <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="tabla_historial">
                <thead>
                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                        <th class="min-w-200px">Activo (Tipo + Modelo)</th>
                        <th class="min-w-200px">Cantidad</th>
                        <th class="min-w-200px">Descripcion</th>
                        <th class="min-w-150px">Usuario</th>
                        <th class="min-w-200px">Flujo (Origen <i class="ki-outline ki-arrow-right fs-7"></i> Destino)</th>
                        <th class="min-w-150px">Fechas</th>
                        <th class="text-end min-w-100px">Duración Total</th>
                    </tr>
                </thead>
                <tbody class="fw-semibold text-gray-600">
                    @forelse($prestamosPasados as $prestamo)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="d-flex flex-column">
                                    <span class="text-gray-800 fw-bold mb-1">
                                        {{ $prestamo->activo->tipo->tipo ?? '' }} {{ $prestamo->activo->modelo->modelo ?? 'Modelo Desconocido' }}
                                    </span>
                                    <span class="text-muted fs-7">
                                        {{ $prestamo->activo->modelo->marca->marca ?? 'Sin Marca' }}
                                        - S/N: {{ $prestamo->activo->serial_number ?? '---' }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="text-gray-800 fw-bold">Prestado: {{ $prestamo->cantidad_prestada}}</span>
                                <span class="text-gray-800 fw-bold">Devuelto: {{ $prestamo->cantidad_devuelta}}</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="text-gray-800 fw-bold">{{ $prestamo->descripcion}}</span>
                            </div>
                        </td>

                        <td>
                            <div class="d-flex flex-column">
                                <span class="text-gray-800 fw-bold">{{ $prestamo->user->name ?? 'Usuario Eliminado' }}</span>
                                <span class="text-muted fs-7">{{ $prestamo->user->email ?? '' }}</span>
                            </div>
                        </td>

                        <td>
                            <div class="d-flex align-items-center">
                                <span class="badge badge-light-warning fs-7" data-bs-toggle="tooltip" title="Salió de aquí">
                                    {{ $prestamo->almacenPrestado->almacen ?? '?' }}
                                </span>

                                <i class="ki-outline ki-arrow-right fs-2 text-gray-400 mx-2"></i>

                                <span class="badge badge-light-success fs-7" data-bs-toggle="tooltip" title="Se devolvió aquí">
                                    {{ $prestamo->almacenDevuelto->almacen ?? '?' }}
                                </span>
                            </div>
                        </td>

                        <td>
                            <div class="d-flex flex-column">
                                <span class="text-gray-800 fs-7">
                                    <i class="ki-outline ki-calendar-tick fs-8 me-1 text-muted"></i>
                                    Del: <strong>{{ $prestamo->fecha_prestado->format('d/m/Y H:i') }}</strong>
                                </span>
                                <span class="text-gray-800 fs-7 mt-1">
                                    <i class="ki-outline ki-calendar-check fs-8 me-1 text-muted"></i>
                                    Al: <strong>{{ $prestamo->fecha_devuelto->format('d/m/Y H:i') }}</strong>
                                </span>
                            </div>
                        </td>

                        <td class="text-end">
                            @php
                            // Calculamos el tiempo que estuvo prestado
                            $diff = $prestamo->fecha_prestado->diff($prestamo->fecha_devuelto);
                            @endphp

                            <span class="badge badge-light fw-bold fs-7 font-monospace">
                                {{ $diff->format('%a d : %h h : %i m') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted fs-5 py-10">
                            <div class="d-flex flex-column align-items-center">
                                <i class="ki-outline ki-time fs-3x text-gray-300 mb-3"></i>
                                <span>Aún no hay historial de devoluciones.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Buscador simple en tiempo real
    document.getElementById('buscador_historial').addEventListener('keyup', function() {
        let filtro = this.value.toLowerCase();
        let filas = document.querySelectorAll('#tabla_historial tbody tr');

        filas.forEach(fila => {
            let texto = fila.innerText.toLowerCase();
            if (texto.includes(filtro)) {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        });
    });
</script>
@endsection