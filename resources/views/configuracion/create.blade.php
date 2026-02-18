@extends('layout.layout')

@section('content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <h3 class="card-title fw-bolder">Crear Nuevo Usuario</h3>
    </div>

    <div class="card-body">
        <form action="{{ route('configuracion.store') }}" method="POST">
            @csrf

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-semibold mb-2">Nombre</label>
                    <input type="text" name="name" class="form-control form-control-solid" required />
                </div>
                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-semibold mb-2">Email</label>
                    <input type="email" name="email" class="form-control form-control-solid" required />
                </div>
            </div>

            <div class="fv-row mb-8">
                <label class="fs-6 fw-bold mb-2">Rol Principal (Opcional)</label>
                <select name="role_id" class="form-select form-select-solid" data-control="select2" data-placeholder="Sin Rol">
                    <option value="">Sin Rol (Usar solo permisos manuales)</option>
                    @foreach($roles as $rol)
                    <option value="{{ $rol->id }}">{{ $rol->name }}</option>
                    @endforeach
                </select>
                <div class="form-text">El rol asigna un paquete de permisos base. Puedes añadir excepciones abajo.</div>
            </div>

            <div class="mb-8">
                <div class="d-flex align-items-center collapsible py-3 toggle collapsed mb-0" data-bs-toggle="collapse" data-bs-target="#kt_permisos_manuales">
                    <div class="btn btn-sm btn-icon btn-active-color-primary ms-n3 me-2">
                        <i class="ki-outline ki-plus-square fs-2 toggle-off"></i>
                        <i class="ki-outline ki-minus-square fs-2 toggle-on"></i>
                    </div>
                    <h4 class="text-gray-700 fw-bold cursor-pointer mb-0">Permisos Directos / Adicionales</h4>
                </div>

                <div id="kt_permisos_manuales" class="collapse mt-5">
                    <div class="alert alert-primary d-flex align-items-center p-5 mb-5">
                        <i class="ki-outline ki-shield-tick fs-2hx text-primary me-4"></i>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1 text-primary">Permisos Específicos</h4>
                            <span>Marca estos permisos SOLO si quieres dar acceso a algo que el Rol no incluye.</span>
                        </div>
                    </div>

                    {{-- TABLA DE PERMISOS (Idéntica a la de Roles) --}}
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
                                @php
                                $modulos = [
                                'usuarios' => 'Usuarios', 'activos' => 'Activos',
                                'almacenes' => 'Almacenes', 'incidencias' => 'Incidencias',
                                'prestamos' => 'Préstamos', 'reservas' => 'Reservas', 'logs' => 'Logs'
                                ];
                                $grupos = $permisos->groupBy(function($i) { return explode('.', $i->name)[0]; });
                                @endphp

                                @foreach($grupos as $key => $grupo)
                                <tr>
                                    <td>{{ $modulos[$key] ?? ucfirst($key) }}</td>

                                    {{-- LECTURA --}}
                                    <td class="text-center bg-light bg-opacity-10">
                                        @if($p = $grupo->firstWhere('name', $key.'.leer'))
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $p->id }}">
                                        @endif
                                    </td>

                                    {{-- ESCRITURA --}}
                                    <td class="text-center bg-light bg-opacity-10">
                                        @if($p = $grupo->firstWhere('name', $key.'.escribir'))
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $p->id }}">
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
                    <label class="required fs-6 fw-semibold mb-2">Contraseña</label>
                    <input type="password" name="password" class="form-control form-control-solid" required />
                </div>
                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-semibold mb-2">Confirmar</label>
                    <input type="password" name="password_confirmation" class="form-control form-control-solid" required />
                </div>
            </div>

            <div class="text-center pt-10">
                <button type="submit" class="btn btn-primary">Guardar Usuario</button>
            </div>
        </form>
    </div>
</div>
@endsection