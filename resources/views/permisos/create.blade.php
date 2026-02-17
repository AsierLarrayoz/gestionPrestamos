@extends('layout.layout')

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h3 class="card-title fw-bold">Crear Nuevo Rol de Usuario</h3>
        <div class="card-toolbar">
            <a href="{{ route('permisos.index') }}" class="btn btn-sm btn-light">Volver</a>
        </div>
    </div>

    <div class="card-body">
        <form action="{{ route('permisos.store') }}" method="POST" id="form_permisos">
            @csrf

            <div class="mb-10 fv-row">
                <label class="required form-label fw-bold">Nombre del Rol</label>
                <input type="text" name="nombre_rol" class="form-control form-control-solid" placeholder="Ej: Técnico de Almacén, Becario, Administrador..." required />
            </div>

            <div class="d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <h4 class="fw-bold m-0">Configuración de Accesos</h4>
                    <button type="button" class="btn btn-sm btn-light-primary" onclick="marcarTodos(true)">
                        <i class="ki-outline ki-check-circle fs-3"></i> Marcar Todo
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-5">
                        <thead>
                            <tr class="text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                <th class="min-w-200px">Módulo</th>
                                <th class="min-w-100px text-center bg-light-success rounded-start">Lectura (Ver)</th>
                                <th class="min-w-100px text-center bg-light-warning rounded-end">Escritura (Crear/Editar/Borrar)</th>
                            </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                            @php
                            // Array de configuración para generar la tabla dinámicamente
                            $modulos = [
                            'usuarios' => 'Gestión de Usuarios',
                            'activos' => 'Inventario de Activos',
                            'almacenes' => 'Almacenes',
                            'incidencias' => 'Incidencias y Reportes',
                            'prestamos' => 'Préstamos y Devoluciones',
                            ];
                            @endphp

                            @foreach($modulos as $key => $label)
                            <tr>
                                <td class="text-gray-800 fw-bold">{{ $label }}</td>

                                <td class="text-center bg-light bg-opacity-10">
                                    <label class="form-check form-check-custom form-check-solid form-check-sm justify-content-center">
                                        <input class="form-check-input chk-read" type="checkbox"
                                            name="permiso_{{ $key }}_r"
                                            id="chk_{{ $key }}_r"
                                            value="1" />
                                    </label>
                                </td>

                                <td class="text-center bg-light bg-opacity-10">
                                    <label class="form-check form-check-custom form-check-solid form-check-sm justify-content-center">
                                        <input class="form-check-input chk-write" type="checkbox"
                                            name="permiso_{{ $key }}_wr"
                                            id="chk_{{ $key }}_wr"
                                            value="1"
                                            onchange="sincronizarLectura('{{ $key }}')" />
                                    </label>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-10">
                <button type="submit" class="btn btn-primary">Guardar Rol</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Si marcas escritura, automáticamente marcas lectura
    function sincronizarLectura(key) {
        const chkWrite = document.getElementById(`chk_${key}_wr`);
        const chkRead = document.getElementById(`chk_${key}_r`);

        if (chkWrite.checked) {
            chkRead.checked = true;
        }
    }

    function marcarTodos(estado) {
        const checkboxes = document.querySelectorAll('.form-check-input');
        checkboxes.forEach(chk => chk.checked = estado);
    }
</script>
@endsection