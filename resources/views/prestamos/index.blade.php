@extends('layout.layout')

@section('content')
<div class="card card-flush">
    <div class="card-header align-items-center py-5 gap-2 gap-md-5">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                <i class="ki-outline ki-magnifier fs-3 position-absolute ms-4"></i>
                <input type="text" id="buscador_prestamos" class="form-control form-control-solid w-250px ps-12" placeholder="Buscar activo, usuario..." />
            </div>
        </div>
        <div class="card-toolbar">
            <a href="{{ route('prestamos.create') }}" class="btn btn-primary">
                <i class="ki-outline ki-scan-barcode fs-2"></i> Ir al Escáner
            </a>
        </div>
    </div>

    <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="tabla_prestamos">
                <thead>
                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                        <th class="min-w-200px">Activo Prestado (Tipo + Modelo)</th>
                        <th class="min-w-150px">Cantidad prestada</th>
                        <th class="min-w-150px">Descripcion</th>
                        <th class="min-w-150px">Usuario Responsable</th>
                        <th class="min-w-100px">Almacén Origen</th>
                        <th class="min-w-100px">Fecha Salida</th>
                        <th class="min-w-150px text-end">Tiempo Transcurrido</th>
                    </tr>
                </thead>
                <tbody class="fw-semibold text-gray-600">
                    @forelse($prestamosActivos as $prestamo)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                    <div class="symbol-label fs-3 bg-light-primary text-primary">
                                        {{ substr($prestamo->activo->tipo->tipo ?? 'A', 0, 1) }}
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <a href="#" class="text-gray-800 text-hover-primary mb-1 fw-bold">
                                        {{ $prestamo->activo->tipo->tipo ?? '' }} {{ $prestamo->activo->modelo->modelo ?? 'Modelo Desconocido' }}
                                    </a>
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
                            <span class="badge badge-light-secondary fs-7 fw-bold">
                                <i class="ki-outline ki-geolocation fs-4 me-1"></i>
                                {{ $prestamo->almacenPrestado->almacen ?? 'N/D' }}
                            </span>
                        </td>

                        <td>
                            <span class="fw-bold text-gray-800">{{ $prestamo->fecha_prestado->format('d/m/Y') }}</span>
                            <span class="text-muted d-block fs-7">{{ $prestamo->fecha_prestado->format('H:i') }}</span>
                        </td>

                        <td class="text-end">
                            @php
                            // Calculamos la diferencia con el momento actual
                            $diff = $prestamo->fecha_prestado->diff(now());

                            // Lógica de color: Rojo si lleva más de 3 días, Verde si es reciente
                            $colorBadge = $diff->days > 3 ? 'badge-light-danger' : 'badge-light-success';
                            @endphp

                            <span class="badge {{ $colorBadge }} fs-7 font-monospace p-2">
                                {{ $diff->format('%a d : %h h : %i m : %s s') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted fs-5 py-10">
                            <div class="d-flex flex-column align-items-center">
                                <i class="ki-outline ki-check-circle fs-3x text-success mb-3"></i>
                                <span>No hay préstamos activos. Todo el inventario está en los almacenes.</span>
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
    // Buscador simple en Javascript para filtrar la tabla sin recargar
    document.getElementById('buscador_prestamos').addEventListener('keyup', function() {
        let filtro = this.value.toLowerCase();
        let filas = document.querySelectorAll('#tabla_prestamos tbody tr');

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