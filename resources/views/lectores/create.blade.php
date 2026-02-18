@extends('layout.layout')

@section('content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <h3 class="fw-bold">Registrar Nuevo Lector</h3>
        </div>
        <div class="card-toolbar">
            <a href="{{ route('lectores.index') }}" class="btn btn-light-primary btn-sm">
                <i class="ki-outline ki-arrow-left fs-2"></i> Volver al listado
            </a>
        </div>
    </div>

    <div class="card-body">
        <form action="{{ route('lectores.store') }}" method="POST" id="form_crear_lector">
            @csrf

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">Nombre</label>
                    <input type="text" name="nombre" class="form-control form-control-solid @error('nombre') is-invalid @enderror" placeholder="Pon el nombre aqui..." />
                </div>
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">Identificador unico</label>
                    <input type="text" name="identificador_unico" class="form-control form-control-solid @error('identificador_unico') is-invalid @enderror" placeholder="123456..." />
                </div>
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">Tipo</label>
                    <input type="text" name="tipo" class="form-control form-control-solid @error('tipo') is-invalid @enderror" placeholder="Interno/externo.." />
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">Asociar a almacen</label>
                    <div class="input-group input-group-solid flex-nowrap">
                        <div class="overflow-hidden flex-grow-1">
                            <select id="almacen_id" name="almacen_id" class="form-select form-select-solid rounded-start" data-control="select2" data-placeholder="Seleccionar Almacen">
                                <option></option>
                                @foreach($almacenes as $almacen)
                                <option value="{{ $almacen->id }}">{{ $almacen->almacen }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn btn-icon btn-light-primary px-5" type="button" data-bs-toggle="modal" data-bs-target="#modal_add_almacen">
                            <i class="ki-outline ki-plus fs-2"></i>
                        </button>
                    </div>
                </div>


            </div>

            <div class="text-center pt-10">
                <button type="reset" class="btn btn-light me-3">Limpiar Formulario</button>
                <button type="submit" class="btn btn-primary w-200px">Registrar Lector</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')

@endsection