@extends('layout.layout')

@section('content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                <input type="text" data-kt-activo-table-filter="search" class="form-control form-control-solid w-250px ps-13" placeholder="Buscar activo..." />
            </div>
        </div>
        <div class="card-toolbar">
            <a href="{{ route('activos.create') }}" class="btn btn-primary">
                <i class="ki-outline ki-plus fs-2"></i> Nuevo Activo
            </a>
        </div>
    </div>

    <div class="card-body py-4">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_activos">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <!--<th>ID</th>-->
                        <th class="min-w-150px">Activo (Marca/Mod)</th>
                        <th>S/N - RFID</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Cantidad total</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                    @foreach($activos as $activo)
                    <tr>
                        <!--<td>{{ $activo->id }}</td>-->
                        <td>
                            <div class="d-flex flex-column">
                                <span class="text-gray-800 fw-bold">{{ $activo->modelo->marca->marca ?? 'S/M' }}</span>
                                <span class="text-muted fs-7">{{ $activo->modelo->modelo ?? 'Genérico' }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="badge badge-light-secondary fs-8">{{ $activo->serial_number ?? 'Sin S/N' }}</div>
                            <div class="text-muted fs-9">{{ $activo->rfid ?? 'Sin RFID' }}</div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="badge badge-bold-primary fw-bold fs-7">{{ $activo->tipo->tipo }}</span>
                            </div>
                        </td>
                        <td>
                            @php
                            $color = match($activo->salud->salud ?? '') {
                            'Excelente' => 'success',
                            'Dañado' => 'danger',
                            'En reparación' => 'warning',
                            default => 'primary'
                            };
                            @endphp
                            <span class="badge badge-light-{{ $color }}">{{ $activo->salud->salud ?? 'N/A' }}</span>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="badge badge-light-primary fw-bold fs-7">{{ $activo->cantidad }} unidades</span>
                            </div>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('activos.edit', $activo->id) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"><i class="ki-outline ki-pencil fs-2"></i></a>
                            <button onclick="confirmarBorrado('{{ $activo->id }}', '{{ $activo->serial_number }}')" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm"><i class="ki-outline ki-trash fs-2"></i></button>
                            <form id="delete-form-{{ $activo->id }}" action="{{ route('activos.destroy', $activo->id) }}" method="POST" style="display: none;">@csrf @method('DELETE')</form>
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
        var table = $('#kt_table_activos').DataTable({
            "info": false,
            'pageLength': 10
        });
        $('[data-kt-activo-table-filter="search"]').on('keyup', function() {
            table.search(this.value).draw();
        });
    });

    function confirmarBorrado(id, sn) {
        Swal.fire({
            title: '¿Borrar activo?',
            text: 'Escribe "si eliminar" para borrar el equipo S/N: ' + sn,
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