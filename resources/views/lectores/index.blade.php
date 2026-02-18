@extends('layout.layout')

@section('content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                <input type="text" data-kt-lector-table-filter="search" class="form-control form-control-solid w-250px ps-13" placeholder="Buscar activo..." />
            </div>
        </div>
        @if(Auth::user()->hasPermission('lectores.escribir'))
        <div class="card-toolbar">
            <a href="{{ route('lectores.create') }}" class="btn btn-primary">
                <i class="ki-outline ki-plus fs-2"></i> Nuevo Lector
            </a>
        </div>
        @endif
    </div>

    <div class="card-body py-4">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_lectores">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th>Nombre</th>
                        <th>Identificador</th>
                        <th>Almacén asociado</th>
                        <th>Tipo</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                    @foreach($lectores as $lector)
                    <tr>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="text-gray-800 fw-bold">{{ $lector->nombre}}</span>
                            </div>
                        </td>
                        <td>
                            <div class="badge badge-light-secondary fs-8">{{ $lector->identificador_unico}}</div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="badge badge-bold-primary fw-bold fs-7">{{$lector->almacen->almacen}}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-light">{{ $lector->tipo ?? 'Sin tipo' }}</span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('lectores.edit', $lector->id) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                <i class="ki-outline ki-pencil fs-2"></i>
                            </a>


                            <button onclick="confirmarBorrado('{{ $lector->id }}', '{{ $lector->nombre }}')" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm">
                                <i class="ki-outline ki-trash fs-2"></i>
                            </button>

                            <form id="delete-form-{{ $lector->id }}" action="{{ route('lectores.destroy', $lector->id) }}" method="POST" style="display: none;">
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
        var table = $('#kt_table_lectores').DataTable({
            "info": false,
            'pageLength': 10
        });
        $('[data-kt-lector-table-filter="search"]').on('keyup', function() {
            table.search(this.value).draw();
        });
    });

    function confirmarBorrado(id, nombre) {
        Swal.fire({
            title: '¿Borrar lector?',
            text: 'Escribe "si eliminar" para borrar el equipo S/N: ' + nombre,
            input: 'text',
            icon: 'error',
            showCancelButton: true,
            inputValidator: (value) => {
                if (value !== 'si eliminar') return 'Texto incorrecto';
            }
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('delete-form-' + id).submit();
        });
    }
</script>
@endsection