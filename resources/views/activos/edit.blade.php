@extends('layout.layout')

@section('content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <h3 class="fw-bold">Editar Activo: <span class="text-primary">{{ $activo->tipo?->tipo ?? 'Sin Tipo' }}</span></h3>
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
                        value="{{ $activo->serial_number ?? 'Sin número de serie' }}"
                        readonly />
                </div>
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">Código RFID</label>
                    <input type="text" name="rfid_code" class="form-control form-control-solid" value="{{ old('rfid_code', $activo->rfid_code) }}" />
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-semibold mb-2">Marca</label>
                    <div class="input-group input-group-solid flex-nowrap">
                        <div class="overflow-hidden flex-grow-1">
                            <select id="marca_id" name="marca_id" class="form-select form-select-solid rounded-start" data-control="select2" data-placeholder="Seleccionar Marca">
                                <option></option>
                                @foreach($marcas as $marca)
                                <option value="{{ $marca->id }}" {{ (old('marca_id', $activo->modelo?->marca_id) == $marca->id) ? 'selected' : '' }}>
                                    {{ $marca->marca }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn btn-icon btn-light-primary px-5" type="button" data-bs-toggle="modal" data-bs-target="#modal_add_marca">
                            <i class="ki-outline ki-plus fs-2"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-semibold mb-2">Modelo</label>
                    <div class="input-group input-group-solid flex-nowrap">
                        <div class="overflow-hidden flex-grow-1">
                            <select id="modelo_id" name="modelo_id" class="form-select form-select-solid rounded-start" data-control="select2" data-placeholder="Seleccionar Modelo">
                                <option></option>
                                @foreach($modelos as $modelo)
                                <option value="{{ $modelo->id }}" {{ (old('modelo_id', $activo->modelo_id) == $modelo->id) ? 'selected' : '' }}>
                                    {{ $modelo->modelo }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <button id="btn_modal_modelo" class="btn btn-icon btn-light-primary px-5" type="button" data-bs-toggle="modal" data-bs-target="#modal_add_modelo">
                            <i class="ki-outline ki-plus fs-2"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-semibold mb-2">Tipo de Activo</label>
                    <div class="input-group input-group-solid flex-nowrap">
                        <div class="overflow-hidden flex-grow-1">
                            <select id="tipo_id" name="tipo_id" class="form-select form-select-solid" data-control="select2">
                                @foreach($tipos as $tipo)
                                <option value="{{ $tipo->id }}" {{ (old('tipo_id', $activo->tipo_id) == $tipo->id) ? 'selected' : '' }}>{{ $tipo->tipo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button id="btn_modal_modelo" class="btn btn-icon btn-light-primary px-5" type="button" data-bs-toggle="modal" data-bs-target="#modal_add_tipo">
                            <i class="ki-outline ki-plus fs-2"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-semibold mb-2">Estado de Salud</label>
                    <div class="input-group input-group-solid flex-nowrap">
                        <div class="overflow-hidden flex-grow-1">
                            <select id="salud_id" name="salud_id" class="form-select form-select-solid" data-control="select2">
                                @foreach($salud as $s)
                                <option value="{{ $s->id }}" {{ (old('salud_id', $activo->salud_id) == $s->id) ? 'selected' : '' }}>{{ $s->salud }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn btn-icon btn-light-primary px-5" type="button" data-bs-toggle="modal" data-bs-target="#modal_add_salud">
                            <i class="ki-outline ki-plus fs-2"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="card bg-light-secondary border-dashed p-6 mb-8">
                <div class="d-flex align-items-center mb-5">
                    <i class="ki-outline ki-delivery-3 fs-2tx text-primary me-4"></i>
                    <div>
                        <h4 class="text-gray-900 fw-bold">Distribución de Stock por Almacén</h4>
                        <p class="fs-6 text-gray-600">Modifica las cantidades directamente en cada sede. El total global se recalculará automáticamente.</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-row-bordered table-row-gray-300 align-middle">
                        <thead>
                            <tr class="fw-bold text-muted">
                                <th class="min-w-200px">Almacén / Sede</th>
                                <th class="min-w-150px text-center">Cantidad Actual</th>
                                <th class="min-w-150px text-end">Nueva Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($almacenes as $almacen)
                            @php
                            // Buscamos si el activo ya está en este almacén
                            $relacion = $activo->almacenes->where('id', $almacen->id)->first();
                            $cantidadActual = $relacion ? $relacion->pivot->cantidad : 0;
                            @endphp
                            <tr>
                                <td>
                                    <span class="text-gray-800 fw-bold">{{ $almacen->almacen }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light fs-7">{{ $cantidadActual }} uds</span>
                                </td>
                                <td class="text-end">
                                    <input type="number"
                                        name="stock_almacenes[{{ $almacen->id }}]"
                                        class="form-control form-control-sm form-control-solid text-end w-100px ms-auto"
                                        value="{{ old('stock_almacenes.' . $almacen->id, $cantidadActual) }}"
                                        min="0"
                                        {{ $activo->serial_number ? 'readonly' : '' }} />
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
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
    // Carga de modelos al cambiar la marca
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

    // Función de guardado rápido AJAX (Completa)
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
            url = "/tipos/quick-store";
            data.tipo = $('#input_nuevo_tipo').val();
            selectId = '#tipo_id';
            modalId = '#modal_add_tipo';
        } else if (entidad === 'salud') {
            url = "/salud/quick-store";
            data.salud = $('#input_nueva_salud').val();
            selectId = '#salud_id';
            modalId = '#modal_add_salud';
        }

        $.post(url, data).done(function(res) {
            let nombreMostrar = res.marca || res.modelo || res.tipo || res.salud;
            let opt = new Option(nombreMostrar, res.id, true, true);
            $(selectId).append(opt).trigger('change');
            $(modalId).modal('hide');
            $('.modal-body input').val('');
            Swal.fire('Éxito', 'Guardado correctamente', 'success');
        }).fail(() => Swal.fire('Error', 'No se pudo guardar.', 'error'));
    }
</script>
@endsection