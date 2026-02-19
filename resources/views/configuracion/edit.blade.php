@extends('layout.layout')

@section('content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <h3 class="card-title fw-bolder">Editar Usuario: {{ $usuario->name }}</h3>
    </div>

    <div class="card-body">
        <form action="{{ route('configuracion.update', $usuario->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-semibold mb-2">Nombre</label>
                    <input type="text" name="name" value="{{ $usuario->name }}" class="form-control form-control-solid" required />
                </div>
                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-semibold mb-2">Email</label>
                    <input type="email" name="email" value="{{ $usuario->email }}" class="form-control form-control-solid" required />
                </div>
            </div>

            <div class="fv-row mb-8">
                <label class="fs-6 fw-bold mb-2">Rol Principal</label>
                <select name="role_id" class="form-select form-select-solid" data-control="select2">
                    <option value="">Sin Rol</option>
                    @foreach($roles as $rol)
                    <option value="{{ $rol->id }}"
                        {{ $usuario->roles->contains($rol->id) ? 'selected' : '' }}>
                        {{ $rol->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-8">
                <div class="d-flex align-items-center collapsible py-3 toggle mb-0" data-bs-toggle="collapse" data-bs-target="#kt_permisos_manuales">
                    <div class="btn btn-sm btn-icon btn-active-color-primary ms-n3 me-2">
                        <i class="ki-outline ki-plus-square fs-2 toggle-off"></i>
                        <i class="ki-outline ki-minus-square fs-2 toggle-on"></i>
                    </div>
                    <h4 class="text-gray-700 fw-bold cursor-pointer mb-0">Permisos Directos / Adicionales</h4>
                </div>

                @php
                // IDs de permisos directos
                $permisosDirectos = $usuario->permissions ? $usuario->permissions->pluck('id')->toArray() : [];

                // IDs de permisos que vienen por Roles
                $permisosPorRoles = [];
                if ($usuario->roles) {
                foreach($usuario->roles as $rol) {
                if($rol->permisos) { // Relación en el modelo Rol
                $permisosPorRoles = array_merge($permisosPorRoles, $rol->permisos->pluck('id')->toArray());
                }
                }
                }

                // Unimos ambos y quitamos duplicados para el check visual
                $todosMisPermisosIds = array_unique(array_merge($permisosDirectos, $permisosPorRoles));

                $modulos = [
                'usuarios' => 'Usuarios', 'activos' => 'Activos',
                'almacenes' => 'Almacenes', 'incidencias' => 'Incidencias',
                'prestamos' => 'Préstamos', 'reservas' => 'Reservas', 'logs' => 'Logs'
                ];
                $grupos = $permisos->groupBy(function($i) { return explode('.', $i->name)[0]; });
                @endphp

                <div id="kt_permisos_manuales" class="collapse {{ count($permisosDirectos) > 0 ? 'show' : '' }} mt-5">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-5">
                            <thead>
                                <tr class="text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-200px">Módulo</th>
                                    <th class="text-center bg-light-success">Lectura</th>
                                    <th class="text-center bg-light-warning">Escritura</th>
                                </tr>
                            </thead>
                            <tbody class="fw-semibold text-gray-600">
                                @foreach($grupos as $key => $grupo)
                                <tr>
                                    <td>{{ $modulos[$key] ?? ucfirst($key) }}</td>

                                    {{-- LECTURA --}}
                                    <td class="text-center bg-light bg-opacity-10">
                                        @if($p = $grupo->firstWhere('name', $key.'.leer'))
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $p->id }}"
                                            @checked(in_array($p->id, $todosMisPermisosIds))>
                                        @endif
                                    </td>

                                    {{-- ESCRITURA --}}
                                    <td class="text-center bg-light bg-opacity-10">
                                        @if($p = $grupo->firstWhere('name', $key.'.escribir'))
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $p->id }}"
                                            @checked(in_array($p->id, $todosMisPermisosIds))>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">Nueva Contraseña (Opcional)</label>
                    <input type="password" name="password" class="form-control form-control-solid" />
                </div>
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">Confirmar</label>
                    <input type="password" name="password_confirmation" class="form-control form-control-solid" />
                </div>
            </div>

            @if(Auth::user()->hasPermission('usuarios.escribir'))
            <div class="text-center pt-10">
                <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
            </div>
            @endif
        </form>
    </div>
</div>
@endsection