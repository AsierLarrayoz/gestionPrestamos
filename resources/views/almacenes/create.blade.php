@extends('layout.layout')

@section('content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <h3 class="fw-bolder">Registrar Nuevo Almacén/Sede</h3>
        </div>
    </div>

    <div class="card-body">
        <form action="{{ route('almacenes.store') }}" method="POST">
            @csrf
            <div class="fv-row mb-10">
                <label class="required fs-6 fw-semibold mb-2">Nombre del Almacén</label>
                <input type="text" name="almacen" class="form-control form-control-solid @error('almacen') is-invalid @enderror"
                    placeholder="Ej. Almacén Central, Oficina Planta 1..." value="{{ old('almacen') }}" required />
                @error('almacen')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="text-center">
                <a href="{{ route('almacenes.index') }}" class="btn btn-light me-3">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar Almacén</button>
            </div>
        </form>
    </div>
</div>
@endsection