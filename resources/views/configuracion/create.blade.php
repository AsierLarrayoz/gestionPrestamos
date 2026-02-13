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

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-semibold mb-2">Rol del Sistema</label>
                    <select name="rol_id" class="form-select form-select-solid @error('rol_id') is-invalid @enderror" data-control="select2" data-placeholder="Selecciona un rol">
                        <option value=""></option>
                        @foreach($roles as $rol)
                        <option value="{{ $rol->id }}" {{ old('rol_id') == $rol->id ? 'selected' : '' }}>
                            {{ $rol->rol }}
                        </option>
                        @endforeach
                    </select>
                    @error('rol_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
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