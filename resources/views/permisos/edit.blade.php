@extends('layout.layout')

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h3 class="card-title fw-bold">Editar Rol: {{ $permiso->nombre_rol }}</h3>
        <div class="card-toolbar">
            <a href="{{ route('permisos.index') }}" class="btn btn-sm btn-light">Volver</a>
        </div>
    </div>

    <div class="card-body">
        <form action="{{ route('permisos.update', $permiso->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-10 fv-row">
                <label class="required form-label fw-bold">Nombre del Rol</label>
                <input type="text" name="nombre_rol" class="form-control form-control-solid" value="{{ $permiso->nombre_rol }}" required />
            </div>

            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5">
                    <thead>
                        <tr class="text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-200px">Módulo</th>
                            <th class="min-w-100px text-center bg-light-success rounded-start">Lectura</th>
                            <th class="min-w-100px text-center bg-light-warning rounded-end">Escritura</th>
                        </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-600">
                        @php
                        $modulos = [
                        'usuarios' => 'Gestión de Usuarios',
                        'activos' => 'Inventario de Activos',
                        'almacenes' => 'Almacenes',
                        'incidencias' => 'Incidencias y Reportes',
                        'prestamos' => 'Préstamos y Devoluciones',
                        ];
                        @endphp

                        @foreach($modulos as $key => $label)
                        @php
                        // Nombres dinámicos de las columnas en BD
                        $colRead = "permiso_{$key}_r";
                        $colWrite = "permiso_{$key}_wr";
                        @endphp
                        <tr>
                            <td>{{ $label }}</td>

                            <td class="text-center">
                                <label class="form-check form-check-custom form-check-solid form-check-sm justify-content-center">
                                    <input class="form-check-input" type="checkbox"
                                        name="{{ $colRead }}"
                                        id="chk_{{ $key }}_r"
                                        value="1"
                                        @checked($permiso->$colRead) />
                                </label>
                            </td>

                            <td class="text-center">
                                <label class="form-check form-check-custom form-check-solid form-check-sm justify-content-center">
                                    <input class="form-check-input" type="checkbox"
                                        name="{{ $colWrite }}"
                                        id="chk_{{ $key }}_wr"
                                        value="1"
                                        @checked($permiso->$colWrite)
                                    onchange="sincronizarLectura('{{ $key }}')" />
                                </label>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-10">
                <button type="submit" class="btn btn-primary">Actualizar Rol</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function sincronizarLectura(key) {
        const chkWrite = document.getElementById(`chk_${key}_wr`);
        const chkRead = document.getElementById(`chk_${key}_r`);
        if (chkWrite.checked) chkRead.checked = true;
    }
</script>
@endsection