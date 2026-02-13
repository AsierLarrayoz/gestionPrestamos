@extends('layout.layout')

@section('content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <h3 class="fw-bold">Registrar Nuevo Activo</h3>
        </div>
        <div class="card-toolbar">
            <a href="{{ route('activos.index') }}" class="btn btn-light-primary btn-sm">
                <i class="ki-outline ki-arrow-left fs-2"></i> Volver al listado
            </a>
        </div>
    </div>

    <div class="card-body">
        <form action="{{ route('activos.store') }}" method="POST" id="form_crear_activo">
            @csrf

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">Número de Serie (S/N)</label>
                    <input type="text" name="serial_number" class="form-control form-control-solid @error('serial_number') is-invalid @enderror" placeholder="Ej: SN-987654321" value="{{ old('serial_number') }}" />
                </div>
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">Código RFID</label>
                    <input type="text" name="rfid_code" class="form-control form-control-solid @error('rfid_code') is-invalid @enderror" placeholder="Ej: RFID-FOX-001" value="{{ old('rfid_code') }}" />
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-semibold mb-2">Marca</label>
                    <div class="input-group input-group-solid">
                        <select id="marca_id" name="marca_id" class="form-select form-select-solid" data-control="select2" data-placeholder="Seleccionar Marca">
                            <option></option>
                            @foreach($marcas as $marca)
                            <option value="{{ $marca->id }}">{{ $marca->marca }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-icon btn-light-primary" type="button" data-bs-toggle="modal" data-bs-target="#modal_add_marca">
                            <i class="ki-outline ki-plus fs-2"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-semibold mb-2">Modelo</label>
                    <div class="input-group input-group-solid">
                        <select id="modelo_id" name="modelo_id" class="form-select form-select-solid" data-control="select2" data-placeholder="Seleccionar Modelo" disabled>
                            <option></option>
                        </select>
                        <button id="btn_modal_modelo" class="btn btn-icon btn-light-primary" type="button" data-bs-toggle="modal" data-bs-target="#modal_add_modelo" disabled>
                            <i class="ki-outline ki-plus fs-2"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-semibold mb-2">Tipo de Activo</label>
                    <div class="input-group input-group-solid">
                        <select id="tipo_id" name="tipo_id" class="form-select form-select-solid" data-control="select2" data-placeholder="Ej: Portátil, Monitor...">
                            <option></option>
                            @foreach($tipos as $tipo)
                            <option value="{{ $tipo->id }}">{{ $tipo->tipo }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-icon btn-light-primary" type="button" data-bs-toggle="modal" data-bs-target="#modal_add_tipo">
                            <i class="ki-outline ki-plus fs-2"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-semibold mb-2">Estado de Salud</label>
                    <div class="input-group input-group-solid">
                        <select id="salud_id" name="salud_id" class="form-select form-select-solid" data-control="select2" data-placeholder="Ej: Excelente, Dañado...">
                            <option></option>
                            @foreach($salud as $s)
                            <option value="{{ $s->id }}">{{ $s->salud }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-icon btn-light-primary" type="button" data-bs-toggle="modal" data-bs-target="#modal_add_salud">
                            <i class="ki-outline ki-plus fs-2"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-semibold mb-2">Almacén de Destino</label>
                    <select name="almacen_id" class="form-select form-select-solid" data-control="select2" required>
                        @foreach($almacenes as $almacen)
                        <option value="{{ $almacen->id }}">{{ $almacen->almacen }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-semibold mb-2">Cantidad Inicial</label>
                    <input type="number" name="cantidad" class="form-control form-control-solid" value="1" min="1" required />

                    <div class="form-text text-muted fs-7">
                        <i class="ki-outline ki-information-5 text-primary fs-6"></i>
                        <strong>Nota:</strong> Si introduces un <strong>Número de Serie</strong>, el sistema ignorará esta cantidad y guardará automáticamente <strong>1 unidad</strong>.
                    </div>
                </div>
            </div>

            <div class="text-center pt-10">
                <button type="reset" class="btn btn-light me-3">Limpiar Formulario</button>
                <button type="submit" class="btn btn-primary w-200px">Registrar Activo</button>
            </div>
        </form>
    </div>
</div>
@include('activos.modals')
@endsection

@section('scripts')
<script>
    // 1. Carga dinámica de Modelos según Marca
    $('#marca_id').on('change', function() {
        let marcaId = $(this).val();
        let selModelo = $('#modelo_id');
        let btnModelo = $('#btn_modal_modelo');

        if (marcaId) {
            selModelo.prop('disabled', false);
            btnModelo.prop('disabled', false);
            fetch(`/get-modelos/${marcaId}`)
                .then(res => res.json())
                .then(data => {
                    selModelo.empty().append('<option></option>');
                    data.forEach(m => selModelo.append(new Option(m.modelo, m.id)));
                });
        } else {
            selModelo.prop('disabled', true).empty();
            btnModelo.prop('disabled', true);
        }
    });

    // 2. Función genérica para Guardado Rápido (AJAX)
    function guardarDatoRapido(entidad) {
        let url = "";
        let data = {
            _token: "{{ csrf_token() }}"
        };
        let selectId = "";
        let modalId = "";

        if (entidad === 'marca') {
            url = "{{ route('marcas.quickStore') }}";
            data.marca = $('#input_nueva_marca').val();
            selectId = '#marca_id';
            modalId = '#modal_add_marca';
        } else if (entidad === 'modelo') {
            url = "{{ route('modelos.quickStore') }}";
            data.modelo = $('#input_nuevo_modelo').val();
            data.marca_id = $('#marca_id').val();
            selectId = '#modelo_id';
            modalId = '#modal_add_modelo';
        } else if (entidad === 'tipo') {
            url = "/tipos/quick-store"; // Asegúrate de crear esta ruta
            data.tipo = $('#input_nuevo_tipo').val();
            selectId = '#tipo_id';
            modalId = '#modal_add_tipo';
        } else if (entidad === 'salud') {
            url = "/salud/quick-store"; // Asegúrate de crear esta ruta
            data.salud = $('#input_nueva_salud').val();
            selectId = '#salud_id';
            modalId = '#modal_add_salud';
        }

        $.post(url, data).done(function(res) {
            // Añadir al select y seleccionar automáticamente
            let nombreMostrar = res.marca || res.modelo || res.tipo || res.salud;
            let opt = new Option(nombreMostrar, res.id, true, true);
            $(selectId).append(opt).trigger('change');

            // Cerrar y limpiar
            $(modalId).modal('hide');
            $('.modal-body input').val('');
            Swal.fire('Éxito', 'Guardado correctamente', 'success');
        }).fail(() => Swal.fire('Error', 'No se pudo guardar. Revisa si ya existe.', 'error'));
    }
</script>
@endsection