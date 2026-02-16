@extends('layout.layout')

@section('content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <h3 class="fw-bolder">Crear Nuevo Usuario</h3>
        </div>
        <div class="card-toolbar">
            <a href="{{ route('configuracion.index') }}" class="btn btn-light-primary btn-sm">
                <i class="ki-outline ki-arrow-left fs-2"></i> Volver al listado
            </a>
        </div>
    </div>

    <div class="card-body">
        <form action="{{ route('configuracion.store') }}" method="POST" id="kt_user_create_form">
            @csrf

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-semibold mb-2">Nombre Completo</label>
                    <input type="text" name="name" class="form-control form-control-solid @error('name') is-invalid @enderror"
                        placeholder="Ej. Juan Pérez" value="{{ old('name') }}" required />
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-semibold mb-2">Correo Electrónico</label>
                    <input type="email" name="email" class="form-control form-control-solid @error('email') is-invalid @enderror"
                        placeholder="usuario@empresa.com" value="{{ old('email') }}" required />
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="fv-row mb-8">
                <label class="fs-6 fw-bold mb-5">Permisos de Acceso</label>

                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-9">
                    <div class="col">
                        <label class="form-check form-check-custom form-check-solid me-10">
                            <input class="form-check-input h-20px w-20px" type="checkbox" name="permiso_usuarios" value="1" {{ old('permiso_usuarios') ? 'checked' : '' }} />
                            <span class="form-check-label fw-semibold text-gray-700">Gestión de Usuarios</span>
                        </label>
                    </div>

                    <div class="col">
                        <label class="form-check form-check-custom form-check-solid me-10">
                            <input class="form-check-input h-20px w-20px" type="checkbox" name="permiso_activos" value="1" {{ old('permiso_activos', '1') == '1' ? 'checked' : '' }} />
                            <span class="form-check-label fw-semibold text-gray-700">Gestión de Activos</span>
                        </label>
                    </div>

                    <div class="col">
                        <label class="form-check form-check-custom form-check-solid me-10">
                            <input class="form-check-input h-20px w-20px" type="checkbox" name="permiso_almacenes" value="1" {{ old('permiso_almacenes', '1') == '1' ? 'checked' : '' }} />
                            <span class="form-check-label fw-semibold text-gray-700">Gestión de Almacenes</span>
                        </label>
                    </div>

                    <div class="col">
                        <label class="form-check form-check-custom form-check-solid me-10">
                            <input class="form-check-input h-20px w-20px" type="checkbox" name="permiso_incidencias" value="1" {{ old('permiso_incidencias', '1') == '1' ? 'checked' : '' }} />
                            <span class="form-check-label fw-semibold text-gray-700">Incidencias</span>
                        </label>
                    </div>

                    <div class="col">
                        <label class="form-check form-check-custom form-check-solid me-10">
                            <input class="form-check-input h-20px w-20px" type="checkbox" name="permiso_prestamos" value="1" {{ old('permiso_prestamos', '1') == '1' ? 'checked' : '' }} />
                            <span class="form-check-label fw-semibold text-gray-700">Préstamos</span>
                        </label>
                    </div>
                </div>
                <div class="form-text text-muted mt-3">Selecciona los módulos a los que este usuario tendrá acceso.</div>
            </div>

            <hr class="my-10 text-gray-200">

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-semibold mb-2">Contraseña</label>
                    <div class="position-relative mb-3">
                        <input type="password" name="password" class="form-control form-control-solid @error('password') is-invalid @enderror"
                            placeholder="Mínimo 8 caracteres" required />
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-semibold mb-2">Confirmar Contraseña</label>
                    <input type="password" name="password_confirmation" class="form-control form-control-solid"
                        placeholder="Repite la contraseña" required />
                </div>
            </div>

            <div class="text-center pt-10">
                <button type="reset" class="btn btn-light me-3">Descartar</button>
                <button type="submit" class="btn btn-primary">
                    <span class="indicator-label">Guardar Usuario</span>
                </button>
            </div>
        </form>
    </div>
</div>
@include('activos.modals')
@endsection