@extends('layout.layout')

@section('content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <h3 class="fw-bold">Editar Activo: <span class="text-primary">{{ $activo->serial_number ?? $activo->uuid }}</span></h3>
        </div>
        <div class="card-toolbar">
            <a href="{{ route('activos.index') }}" class="btn btn-light-primary btn-sm">
                <i class="ki-outline ki-arrow-left fs-2"></i> Cancelar
            </a>
        </div>
    </div>

    <div class="card-body">
        <form action="{{ route('activos.update', $activo->id) }}" method="POST" id="form_editar_activo">
            @csrf
            @method('PUT')

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">Número de Serie (S/N)</label>
                    <input type="text" name="serial_number"
                        class="form-control form-control-solid {{ $activo->serial_number ? 'bg-light-secondary' : '' }}"
                        value="{{ old('serial_number', $activo->serial_number) }}"
                        {{ $activo->serial_number ? 'readonly' : '' }} />

                    @if($activo->serial_number)
                    <div class="form-text text-muted">El número de serie no se puede modificar.</div>
                    @endif
                </div>
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">Código RFID</label>
                    <input type="text" name="rfid_code" class="form-control form-control-solid" value="{{ old('rfid_code', $activo->rfid_code) }}" />
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-semibold mb-2">Marca</label>
                    <div class="input-group input-group-solid">
                        <select id="marca_id" name="marca_id" class="form-select form-select-solid" data-control="select2">
                            <option></option>
                            @foreach($marcas as $marca)
                            <option value="{{ $marca->id }}" {{ (old('marca_id', $activo->modelo?->marca_id) == $marca->id) ? 'selected' : '' }}>
                                {{ $marca->marca }}
                            </option>
                            @endforeach
                        </select>
                        <button class="btn btn-icon btn-light-primary" type="button" data-bs-toggle="modal" data-bs-target="#modal_add_marca"><i class="ki-outline ki-plus fs-2"></i></button>
                    </div>
                </div>

                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-semibold mb-2">Modelo</label>
                    <div class="input-group input-group-solid">
                        <select id="modelo_id" name="modelo_id" class="form-select form-select-solid" data-control="select2">
                            <option></option>
                            @foreach($modelos as $modelo)
                            <option value="{{ $modelo->id }}" {{ (old('modelo_id', $activo->modelo_id) == $modelo->id) ? 'selected' : '' }}>
                                {{ $modelo->modelo }}
                            </option>
                            @endforeach
                        </select>
                        <button id="btn_modal_modelo" class="btn btn-icon btn-light-primary" type="button" data-bs-toggle="modal" data-bs-target="#modal_add_modelo"><i class="ki-outline ki-plus fs-2"></i></button>
                    </div>
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-semibold mb-2">Tipo de Activo</label>
                    <div class="input-group input-group-solid">
                        <select id="tipo_id" name="tipo_id" class="form-select form-select-solid" data-control="select2">
                            @foreach($tipos as $tipo)
                            <option value="{{ $tipo->id }}" {{ (old('tipo_id', $activo->tipo_id) == $tipo->id) ? 'selected' : '' }}>{{ $tipo->tipo }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-semibold mb-2">Estado de Salud</label>
                    <div class="input-group input-group-solid">
                        <select id="salud_id" name="salud_id" class="form-select form-select-solid" data-control="select2">
                            @foreach($salud as $s)
                            <option value="{{ $s->id }}" {{ (old('salud_id', $activo->salud_id) == $s->id) ? 'selected' : '' }}>{{ $s->salud }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="card bg-light-warning border-warning border-dashed p-6 mb-8">
                <div class="d-flex flex-stack">
                    <div class="d-flex align-items-center">
                        <i class="ki-outline ki-information-5 fs-2tx text-warning me-4"></i>
                        <div class="fw-semibold">
                            <h4 class="text-gray-900 fw-bold">Actualización de Inventario</h4>
                            <p class="fs-6 text-gray-700">Selecciona el almacén donde quieres aplicar el ajuste de cantidad.</p>
                        </div>
                    </div>
                </div>

                <div class="row g-9 mt-1">
                    <div class="col-md-6">
                        <label class="required fs-6 fw-semibold mb-2">Almacén Objetivo</label>
                        <select name="almacen_id" class="form-select form-select-solid" required>
                            @foreach($almacenes as $almacen)
                            <option value="{{ $almacen->id }}">{{ $almacen->almacen }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="required fs-6 fw-semibold mb-2">Cantidad (Nueva o Ajustada)</label>
                        <input type="number"
                            name="cantidad"
                            class="form-control form-control-solid {{ $activo->serial_number ? 'bg-light-secondary' : '' }}"
                            value="{{ old('cantidad', $activo->serial_number ? 1 : $activo->cantidad) }}"
                            min="0"
                            {{ $activo->serial_number ? 'readonly' : '' }}
                            required />

                        @if($activo->serial_number)
                        <div class="form-text text-muted">
                            <i class="ki-outline ki-information fs-7 text-info"></i>
                            Los activos serializados están limitados a 1 unidad.
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="text-center pt-10">
                <button type="submit" class="btn btn-warning w-200px">Actualizar Activo</button>
            </div>
        </form>
    </div>
</div>
@include('activos.modals')

@endsection

@section('scripts')
<script>
    // Lógica de carga de modelos (idéntica al create)
    $('#marca_id').on('change', function() {
        let marcaId = $(this).val();
        let selModelo = $('#modelo_id');
        if (marcaId) {
            fetch(`/get-modelos/${marcaId}`)
                .then(res => res.json())
                .then(data => {
                    selModelo.empty().append('<option></option>');
                    data.forEach(m => selModelo.append(new Option(m.modelo, m.id)));
                });
        }
    });

    // Función de guardado rápido AJAX (puedes moverla a un .js externo si prefieres)
    function guardarDatoRapido(entidad) {
        // ... misma lógica que en el create ...
    }
</script>
@endsection