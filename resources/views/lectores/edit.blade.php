@extends('layout.layout')

@section('content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <h3 class="fw-bold">Actualizar Lector: {{ $lector->nombre }}</h3>
        </div>
        <div class="card-toolbar">
            <a href="{{ route('lectores.index') }}" class="btn btn-light-primary btn-sm">
                <i class="ki-outline ki-arrow-left fs-2"></i> Volver al listado
            </a>
        </div>
    </div>

    <div class="card-body">
        {{-- CAMBIO 1: El action usa $lector->id y añadimos @method('PUT') --}}
        <form action="{{ route('lectores.update', $lector->id) }}" method="POST" id="form_edit_lector">
            @csrf
            @method('PUT')

            <div class="row g-9 mb-8">
                <div class="col-md-4 fv-row">
                    <label class="fs-6 fw-semibold mb-2">Nombre</label>
                    {{-- CAMBIO 2: Doble llave {{ }} para los valores --}}
                    <input type="text" name="nombre" class="form-control form-control-solid" value="{{ $lector->nombre }}" />
                </div>
                <div class="col-md-4 fv-row">
                    <label class="fs-6 fw-semibold mb-2">Identificador único</label>
                    <input type="text" name="identificador_unico" class="form-control form-control-solid" value="{{ $lector->identificador_unico }}" readonly />
                </div>
                <div class="col-md-4 fv-row">
                    <label class="fs-6 fw-semibold mb-2">Tipo</label>
                    <input type="text" name="tipo" class="form-control form-control-solid" value="{{ $lector->tipo }}" />
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">Asociar a almacén</label>
                    <select name="almacen_id" class="form-select form-select-solid" data-control="select2">
                        @foreach($almacenes as $almacen)
                        <option value="{{ $almacen->id }}" {{ $lector->almacen_id == $almacen->id ? 'selected' : '' }}>
                            {{ $almacen->almacen }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="text-center pt-10">
                <button type="submit" class="btn btn-primary w-200px">Actualizar Lector</button>
            </div>
        </form>
    </div>
</div>
@endsection