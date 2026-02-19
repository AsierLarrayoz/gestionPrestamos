@extends('layout.layout')

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h3 class="card-title fw-bold">Editar Rol: {{ $rol->name }}</h3>
        <div class="card-toolbar">
            <a href="{{ route('permisos.index') }}" class="btn btn-sm btn-light">Volver</a>
        </div>
    </div>

    <div class="card-body">
        <form action="{{ route('permisos.update', $rol->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-10 fv-row">
                <label class="required form-label fw-bold">Nombre del Rol</label>
                <input type="text" name="name" class="form-control form-control-solid" value="{{ $rol->name }}" required
                    {{ $rol->id == 1 ? 'readonly' : '' }} />
                @if($rol->id == 1)
                <div class="form-text">El nombre del Super Admin no se puede modificar.</div>
                @endif
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
                        $nombresModulos = [
                        'usuarios' => 'Gestión de Usuarios',
                        'activos' => 'Inventario de Activos',
                        'almacenes' => 'Almacenes',
                        'incidencias' => 'Incidencias',
                        'prestamos' => 'Préstamos',
                        'reservas' => 'Reservas',
                        'lectores' => 'Lectores',
                        'logs' => 'Logs del Sistema',
                        ];

                        // Agrupamos permisos disponibles
                        $grupos = $permisos->groupBy(function($item) {
                        return explode('.', $item->name)[0];
                        });

                        // IDs que ya tiene el rol
                        $permisosActivos = $rol->permisos->pluck('id')->toArray();
                        @endphp

                        @foreach($grupos as $key => $permisosGrupo)
                        <tr>
                            <td class="text-gray-800 fw-bold">
                                {{ $nombresModulos[$key] ?? ucfirst($key) }}
                            </td>

                            {{-- LECTURA --}}
                            @php $pLeer = $permisosGrupo->firstWhere('name', $key . '.leer'); @endphp
                            <td class="text-center bg-light bg-opacity-10">
                                @if($pLeer)
                                <label class="form-check form-check-custom form-check-solid form-check-sm justify-content-center">
                                    <input class="form-check-input" type="checkbox"
                                        name="permissions[]"
                                        id="chk_{{ $key }}_r"
                                        value="{{ $pLeer->id }}"
                                        @checked(in_array($pLeer->id, $permisosActivos)) />
                                </label>
                                @endif
                            </td>

                            {{-- ESCRITURA --}}
                            @php $pEscribir = $permisosGrupo->firstWhere('name', $key . '.escribir'); @endphp
                            <td class="text-center bg-light bg-opacity-10">
                                @if($pEscribir)
                                <label class="form-check form-check-custom form-check-solid form-check-sm justify-content-center">
                                    <input class="form-check-input" type="checkbox"
                                        name="permissions[]"
                                        id="chk_{{ $key }}_wr"
                                        value="{{ $pEscribir->id }}"
                                        @checked(in_array($pEscribir->id, $permisosActivos))
                                    onchange="sincronizarLectura('{{ $key }}')" />
                                </label>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if(Auth::user()->hasPermission('usuarios.escribir'))
            <div class="d-flex justify-content-end mt-10">
                <button type="submit" class="btn btn-primary">Actualizar Rol</button>
            </div>
            @endif
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function sincronizarLectura(key) {
        const chkWrite = document.getElementById(`chk_${key}_wr`);
        const chkRead = document.getElementById(`chk_${key}_r`);
        if (chkWrite && chkWrite.checked && chkRead) {
            chkRead.checked = true;
        }
    }
</script>
@endsection