@extends('layout.layout')

@section('content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <h3 class="fw-bolder">Editar Usuario: {{ $usuario->name }}</h3>
        </div>
        <div class="card-toolbar">
            <a href="{{ route('configuracion.index') }}" class="btn btn-light-primary btn-sm">
                <i class="ki-outline ki-arrow-left fs-2"></i> Volver al listado
            </a>
        </div>
    </div>

    <div class="card-body">
        <form action="{{ route('configuracion.update', $usuario->id) }}" method="POST" id="kt_user_edit_form">
            @csrf
            @method('PUT')

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-semibold mb-2">Nombre Completo</label>
                    <input type="text" name="name" class="form-control form-control-solid @error('name') is-invalid @enderror"
                        value="{{ old('name', $usuario->name) }}" required />
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-semibold mb-2">Correo Electrónico</label>
                    <input type="email" name="email" class="form-control form-control-solid @error('email') is-invalid @enderror"
                        value="{{ old('email', $usuario->email) }}" required />
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="fv-row mb-8">
                <label class="fs-6 fw-bold mb-2 required">Rol / Perfil de Permisos</label>

                <select name="permisos_id" id="permisos_id" class="form-select form-select-solid" data-control="select2" data-placeholder="Selecciona un rol">
                    <option></option>
                    @foreach($permisos as $permiso)
                    <option value="{{ $permiso->id }}" selected="$usuario->permisos->nombre_rol">
                        {{ $permiso->nombre_rol }}
                    </option>
                    @endforeach
                </select>

                <div id="info-permisos" class="mt-5 p-5 bg-light rounded d-none">
                    <div class="fw-bold mb-2 text-gray-800">Permisos incluidos en este rol:</div>
                    <div id="lista-permisos" class="d-flex flex-wrap gap-2">
                    </div>
                </div>

                <div class="form-text text-muted">Al elegir un rol, el usuario heredará automáticamente todos sus permisos.</div>
            </div>

            <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6 mb-8">
                <i class="ki-outline ki-information-5 fs-2tx text-warning me-4"></i>
                <div class="d-flex flex-stack flex-grow-1">
                    <div class="fw-semibold">
                        <h4 class="text-gray-900 fw-bold">Actualización de seguridad</h4>
                        <div class="fs-6 text-gray-700">Deja los siguientes campos en blanco si NO deseas cambiar la contraseña del usuario.</div>
                    </div>
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">Nueva Contraseña (Opcional)</label>
                    <input type="password" name="password" class="form-control form-control-solid @error('password') is-invalid @enderror"
                        placeholder="Ingresa nueva contraseña" />
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">Confirmar Nueva Contraseña</label>
                    <input type="password" name="password_confirmation" class="form-control form-control-solid"
                        placeholder="Repite la nueva contraseña" />
                </div>
            </div>

            <div class="text-center pt-10">
                <button type="reset" class="btn btn-light me-3">Revertir cambios</button>
                <button type="submit" class="btn btn-primary">
                    <span class="indicator-label">Actualizar Usuario</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection