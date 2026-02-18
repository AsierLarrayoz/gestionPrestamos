@extends('layout.layout')

@section('content')
<div class="card card-flush">
    <div class="card-header align-items-center py-5 gap-2 gap-md-5">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                <i class="ki-outline ki-magnifier fs-3 position-absolute ms-4"></i>
                <input type="text" id="buscador_incidencias" class="form-control form-control-solid w-250px ps-12" placeholder="Buscar incidencia..." />
            </div>
        </div>
        <div class="card-toolbar">
            @if(Auth::user()->hasPermission('incidencias.escribir'))
            <a href="{{ route('incidencias.create') }}" class="btn btn-primary">
                <i class="ki-outline ki-plus-square fs-2"></i> Reportar Incidencia
            </a>
            @endif
        </div>
    </div>

    <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="tabla_incidencias">
                <thead>
                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                        <th class="min-w-150px">Incidencia</th>
                        <th class="min-w-150px">Activo Afectado</th>
                        <th class="min-w-100px">Nivel / Estado</th>
                        <th class="min-w-100px">Reportado por</th>
                        <th class="min-w-100px">Fecha</th>
                        <th class="text-end min-w-100px">Acciones</th>
                    </tr>
                </thead>
                <tbody class="fw-semibold text-gray-600">
                    @forelse($incidencias as $incidencia)
                    <tr>
                        <td>
                            <div class="d-flex flex-column">
                                <a href="#" class="text-gray-800 text-hover-primary mb-1 fw-bold">{{ $incidencia->titulo }}</a>
                                <span class="text-muted fs-7">{{ Str::limit($incidencia->descripcion, 40) }}</span>
                            </div>
                        </td>

                        <td>
                            @if($incidencia->activo)
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-circle symbol-35px overflow-hidden me-2">
                                    <div class="symbol-label bg-light-danger text-danger fw-bold">
                                        {{ substr($incidencia->activo->tipo->tipo ?? 'A', 0, 1) }}
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="text-gray-800 fw-bold fs-7">
                                        {{ $incidencia->activo->tipo->tipo ?? '' }} {{ $incidencia->activo->modelo->modelo ?? '' }}
                                    </span>
                                    <span class="text-muted fs-8">S/N: {{ $incidencia->activo->serial_number ?? '---' }}</span>
                                </div>
                            </div>
                            @else
                            <span class="text-muted">Activo eliminado</span>
                            @endif
                        </td>

                        <td>
                            <div class="d-flex flex-column gap-1">
                                <span class="badge badge-light-danger fs-8 fw-bold">
                                    {{ $incidencia->nivel->nivel ?? 'N/D' }}
                                </span>
                                <span class="badge badge-light-primary fs-8 fw-bold">
                                    {{ $incidencia->estado->estado ?? 'N/D' }}
                                </span>
                            </div>
                        </td>

                        <td>
                            <span class="text-gray-800 fw-bold d-block">{{ $incidencia->user->name ?? 'Usuario' }}</span>
                            <span class="text-muted fs-7">{{ $incidencia->user->email ?? '' }}</span>
                        </td>

                        <td>
                            <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($incidencia->fecha_incidencia)->format('d/m/Y') }}</span>
                            <span class="text-muted d-block fs-7">{{ \Carbon\Carbon::parse($incidencia->fecha_incidencia)->format('H:i') }}</span>
                        </td>

                        <td class="text-end">
                            <a href="{{ route('incidencias.edit', $incidencia->id) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                <i class="ki-outline ki-pencil fs-2"></i>
                            </a>

                            <form action="{{ route('incidencias.destroy', $incidencia->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que quieres eliminar esta incidencia?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm">
                                    <i class="ki-outline ki-trash fs-2"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">No hay incidencias registradas. ¡Todo funciona bien!</td>
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
    document.getElementById('buscador_incidencias').addEventListener('keyup', function() {
        let filtro = this.value.toLowerCase();
        let filas = document.querySelectorAll('#tabla_incidencias tbody tr');
        filas.forEach(fila => {
            let texto = fila.innerText.toLowerCase();
            fila.style.display = texto.includes(filtro) ? '' : 'none';
        });
    });
</script>
@endsection