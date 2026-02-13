@extends('layout.layout')

@section('content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                <input type="text" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-13" placeholder="Buscar usuario" />
            </div>
        </div>
        <div class="card-toolbar">
            <div class="d-flex justify-content-end">
                <a href="{{ route('configuracion.create') }}" class="btn btn-primary">
                    <i class="ki-outline ki-plus fs-2"></i> Añadir Usuario
                </a>
            </div>
        </div>
    </div>

    <div class="card-body py-4">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th class="min-w-50px">ID</th>
                        <th class="min-w-125px">Usuario</th>
                        <th class="min-w-125px">Email</th>
                        <th class="min-w-125px">Rol</th>
                        <th class="text-end min-w-100px">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                    @foreach($usuarios as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td class="d-flex align-items-center">
                            <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                <div class="symbol-label fs-3 bg-light-danger text-danger">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <span class="text-gray-800 text-hover-primary mb-1">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <div class="badge badge-light-primary fw-bold">{{ $user->rol->rol ?? 'Sin Rol' }}</div>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('configuracion.edit', $user->id) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" title="Editar">
                                <i class="ki-outline ki-pencil fs-2"></i>
                            </a>

                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm"
                                onclick="confirmarEliminacion('{{ $user->id }}', '{{ addslashes($user->name) }}')" title="Eliminar">
                                <i class="ki-outline ki-trash fs-2"></i>
                            </button>

                            <form id="delete-form-{{ $user->id }}" action="{{ route('configuracion.destroy', $user->id) }}" method="POST" style="display: none;">
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
    function confirmarEliminacion(id, nombre) {
        Swal.fire({
            title: '¿Estás totalmente seguro?',
            text: 'Vas a eliminar al usuario "' + nombre + '". Esta acción no se puede deshacer. Para confirmar, escribe "si eliminar" abajo:',
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off'
            },
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            inputValidator: (value) => {
                if (!value) {
                    return '¡Necesitas escribir algo!'
                }
                if (value !== 'si eliminar') {
                    return 'Debes escribir exactamente "si eliminar"'
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Si el texto es correcto, enviamos el formulario
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
<script>
    //Busqueda de usuarios
    $(document).ready(function() {
        // Inicializamos DataTable sobre la tabla
        var table = $('#kt_table_users').DataTable({
            "info": false, // Quita el texto de "Mostrando X de Y"
            'order': [], // Quita el orden inicial automático
            'pageLength': 5, //Paginacion de usuarios
            "lengthChange": false // Quita el selector de cantidad de registros
        });

        // Conectamos tu input de búsqueda con la tabla
        $('[data-kt-user-table-filter="search"]').on('keyup', function() {
            table.search(this.value).draw();
        });
    });
</script>
@endsection