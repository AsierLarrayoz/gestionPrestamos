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
                <input type="text" name="name" class="form-control form-control-solid" placeholder="Ej: Técnico de Almacén" required />
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
                                <th class="min-w-100px text-center bg-light-warning rounded-end">Escritura (Editar)</th>
                            </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                            @php
                            // 1. Definimos nombres bonitos para los grupos
                            $nombresModulos = [
                            'usuarios' => 'Gestión de Usuarios',
                            'activos' => 'Inventario de Activos',
                            'almacenes' => 'Almacenes',
                            'incidencias' => 'Incidencias',
                            'prestamos' => 'Préstamos',
                            'reservas' => 'Reservas',
                            'logs' => 'Logs del Sistema',
                            ];

                            // 2. Agrupamos los permisos que vienen de la BD por su prefijo (ej: 'usuarios')
                            $grupos = $permisos->groupBy(function($item) {
                            return explode('.', $item->name)[0];
                            });
                            @endphp

                            @foreach($grupos as $key => $permisosGrupo)
                            <tr>
                                <td class="text-gray-800 fw-bold">
                                    {{ $nombresModulos[$key] ?? ucfirst($key) }}
                                </td>

                                {{-- Buscamos el permiso .leer dentro del grupo --}}
                                @php $pLeer = $permisosGrupo->firstWhere('name', $key . '.leer'); @endphp
                                <td class="text-center bg-light bg-opacity-10">
                                    @if($pLeer)
                                    <label class="form-check form-check-custom form-check-solid form-check-sm justify-content-center">
                                        <input class="form-check-input chk-read" type="checkbox"
                                            name="permissions[]"
                                            id="chk_{{ $key }}_r"
                                            value="{{ $pLeer->id }}" />
                                    </label>
                                    @endif
                                </td>

                                {{-- Buscamos el permiso .escribir dentro del grupo --}}
                                @php $pEscribir = $permisosGrupo->firstWhere('name', $key . '.escribir'); @endphp
                                <td class="text-center bg-light bg-opacity-10">
                                    @if($pEscribir)
                                    <label class="form-check form-check-custom form-check-solid form-check-sm justify-content-center">
                                        <input class="form-check-input chk-write" type="checkbox"
                                            name="permissions[]"
                                            id="chk_{{ $key }}_wr"
                                            value="{{ $pEscribir->id }}"
                                            onchange="sincronizarLectura('{{ $key }}')" />
                                    </label>
                                    @endif
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
    function sincronizarLectura(key) {
        const chkWrite = document.getElementById(`chk_${key}_wr`);
        const chkRead = document.getElementById(`chk_${key}_r`);
        // Si marco escribir y existe el de leer, marco leer también
        if (chkWrite && chkWrite.checked && chkRead) {
            chkRead.checked = true;
        }
    }

    function marcarTodos(estado) {
        document.querySelectorAll('.form-check-input').forEach(chk => chk.checked = estado);
    }
</script>
@endsection