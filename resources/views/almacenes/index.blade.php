@extends('layout.layout')

@section('content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                <input type="text" data-kt-almacen-table-filter="search" class="form-control form-control-solid w-250px ps-13" placeholder="Buscar almacén..." />
            </div>
        </div>
        <div class="card-toolbar">
            <a href="{{ route('almacenes.create') }}" class="btn btn-primary">
                <i class="ki-outline ki-plus fs-2"></i> Nuevo Almacén
            </a>
        </div>
    </div>

    <div class="card-body py-4">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_almacenes">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <!--<th class="min-w-50px">ID</th>-->
                        <th class="min-w-250px">Nombre del Almacén</th>
                        <th class="min-w-150px">Nº de Activos</th>
                        <th class="text-end min-w-100px">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                    @foreach($almacenes as $almacen)
                    <tr>
                        <!--<td>{{ $almacen->id }}</td>-->
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40px me-3">
                                    <span class="symbol-label bg-light-info text-info fw-bold">
                                        {{ substr($almacen->almacen, 0, 1) }}
                                    </span>
                                </div>
                                <span class="text-gray-800 text-hover-primary fw-bold">{{ $almacen->almacen }}</span>
                            </div>
                        </td>
                        <td>
                            {{-- Usamos el conteo que definiste en tu controlador --}}
                            <span class="badge badge-light-dark">{{ $almacen->activos()->count() }} items</span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('almacenes.show', $almacen->id) }}" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" title="Ver Inventario">
                                <i class="ki-outline ki-eye fs-2"></i>
                            </a>
                            <a href="{{ route('almacenes.edit', $almacen->id) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" title="Editar">
                                <i class="ki-outline ki-pencil fs-2"></i>
                            </a>

                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm"
                                onclick="confirmarEliminacionAlmacen('{{ $almacen->id }}', '{{ addslashes($almacen->almacen) }}')" title="Borrar">
                                <i class="ki-outline ki-trash fs-2"></i>
                            </button>

                            <form id="delete-form-{{ $almacen->id }}" action="{{ route('almacenes.destroy', $almacen->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        var table = $('#kt_table_almacenes').DataTable({
            "info": false,
            'order': [],
            'pageLength': 10
        });

        $('[data-kt-almacen-table-filter="search"]').on('keyup', function() {
            table.search(this.value).draw();
        });
    });

    function confirmarEliminacionAlmacen(id, nombre) {
        Swal.fire({
            title: '¿Borrar almacén?',
            text: 'Vas a eliminar "' + nombre + '". Esta acción no se puede deshacer. Escribe "si eliminar" para confirmar:',
            input: 'text',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            inputValidator: (value) => {
                if (value !== 'si eliminar') {
                    return 'Debes escribir exactamente "si eliminar"'
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endsection