@extends('layout.layout')

@section('content')
<div class="d-flex flex-column flex-center p-10">

    <div class="card card-flush w-lg-800px py-5">
        <div class="card-header border-0">
            <div class="card-title">
                <h2><i class="ki-outline ki-pencil fs-1 me-2 text-primary"></i> Gestión Manual</h2>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('prestamos.create') }}" class="btn btn-sm btn-light-primary">
                    <i class="ki-outline ki-scan-barcode fs-3"></i> Usar Escáner
                </a>
            </div>
        </div>

        <div class="card-body pt-0">
            @if(session('success'))
            <div class="alert alert-dismissible bg-light-success border border-success d-flex flex-column flex-sm-row p-5 mb-10">
                <i class="ki-outline ki-check-circle fs-2hx text-success me-4 mb-5 mb-sm-0"></i>
                <div class="d-flex flex-column pe-0 pe-sm-10">
                    <h4 class="fw-semibold">Operación Correcta</h4>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-dismissible bg-light-danger border border-danger d-flex flex-column flex-sm-row p-5 mb-10">
                <i class="ki-outline ki-cross-circle fs-2hx text-danger me-4 mb-5 mb-sm-0"></i>
                <div class="d-flex flex-column pe-0 pe-sm-10">
                    <h4 class="fw-semibold">Atención</h4>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
            @endif

            <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-8 fs-4 fw-bold">
                <li class="nav-item">
                    <a class="nav-link active text-primary" data-bs-toggle="tab" href="#tab_prestar">
                        <i class="ki-outline ki-plus-square fs-2 me-2"></i> Prestar Activo
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger" data-bs-toggle="tab" href="#tab_devolver">
                        <i class="ki-outline ki-minus-square fs-2 me-2"></i> Devolver Activo
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="tab_prestar" role="tabpanel">
                    <form action="{{ route('prestamos.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="accion_confirmada" value="prestar">

                        <div class="row mb-8">
                            <div class="col-md-6 fv-row">
                                <label class="form-label fw-bold required">Almacén de Origen</label>
                                <select name="almacen_id" id="select_almacen_prestar" class="form-select form-select-solid" required>
                                    @foreach($almacenes as $almacen)
                                    <option value="{{ $almacen->id }}">{{ $almacen->almacen }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 fv-row">
                                <label class="form-label fw-bold required">Cantidad</label>
                                <input type="number" id="input_cantidad_prestar" name="cantidad_confirmada" class="form-control form-control-solid" value="1" min="1" required />
                            </div>
                        </div>

                        <div class="fv-row mb-8">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-bold required mb-0">Activo a Prestar</label>
                                <span id="badge_stock_disponible" class="badge badge-light-success fw-bold" style="display: none;"></span>
                            </div>
                            <select name="codigo" id="select_activo_prestar" class="form-select form-select-solid" data-control="select2" data-placeholder="Busca un activo disponible..." required>
                                <option></option>
                            </select>
                        </div>

                        <div class="fv-row mb-10">
                            <label class="form-label fw-bold">Descripción / Destinatario (Opcional)</label>
                            <input type="text" name="descripcion" class="form-control form-control-solid" placeholder="Ej: Entregado a Asier" autocomplete="off" />
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary"><i class="ki-outline ki-plus fs-2"></i> Confirmar Préstamo</button>
                        </div>
                    </form>
                </div>

                <div class="tab-pane fade" id="tab_devolver" role="tabpanel">
                    <form action="{{ route('prestamos.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="accion_confirmada" value="devolver">
                        <input type="hidden" name="codigo" id="hidden_codigo_devolver">

                        <div class="row mb-8">
                            <div class="col-md-6 fv-row">
                                <label class="form-label fw-bold required">Almacén de Destino</label>
                                <select name="almacen_id" class="form-select form-select-solid" required>
                                    @foreach($almacenes as $almacen)
                                    <option value="{{ $almacen->id }}">{{ $almacen->almacen }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 fv-row">
                                <label class="form-label fw-bold required">Cantidad que devuelve</label>
                                <input type="number" id="input_cantidad_devolver" name="cantidad_confirmada" class="form-control form-control-solid" value="1" min="0" required />
                            </div>
                        </div>

                        <div class="fv-row mb-10">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-bold required mb-0">Seleccionar el Préstamo Pendiente</label>
                                <span id="badge_max_prestamo" class="badge badge-light-warning fw-bold" style="display: none;"></span>
                            </div>
                            <select name="prestamo_id" id="select_prestamo_manual" class="form-select form-select-solid border-warning" data-control="select2" data-placeholder="Busca por usuario, descripción o activo..." required>
                                <option></option>
                                @foreach($prestamosActivos as $prestamo)
                                <option value="{{ $prestamo->id }}"
                                    data-uuid="{{ $prestamo->activo->uuid }}"
                                    data-max="{{ $prestamo->cantidad_prestada }}"
                                    data-serialized="{{ $prestamo->activo->is_serialized ? 1 : 0 }}">
                                    {{ $prestamo->cantidad_prestada }} ud. de {{ $prestamo->activo->tipo->tipo ?? '' }} {{ $prestamo->activo->modelo->modelo ?? '' }}
                                    (A: {{ $prestamo->user->name ?? 'Usuario' }} | Desc: {{ $prestamo->descripcion ?? '---' }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="panel_parcial_manual" class="text-start bg-light-danger border border-danger p-4 rounded mb-10" style="display: none;">
                            <label class="form-label fw-bold text-danger mb-3">
                                <i class="ki-outline ki-warning fs-3 text-danger me-1"></i> ¡Devolución Parcial!
                            </label>
                            <p class="text-muted fs-7 mb-4">Faltan <span id="span_faltantes_manual" class="fw-bold fs-5 text-dark"></span> unidades de este préstamo.</p>
                            <div class="form-check form-check-custom form-check-solid mb-3">
                                <input class="form-check-input" type="radio" name="tipo_devolucion_parcial" value="dividir" id="radio_dividir_m" checked />
                                <label class="form-check-label text-gray-700" for="radio_dividir_m"><strong>Dejar pendientes</strong></label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input" type="radio" name="tipo_devolucion_parcial" value="finalizar" id="radio_finalizar_m" />
                                <label class="form-check-label text-gray-700" for="radio_finalizar_m"><strong>Cerrar préstamo con pérdidas</strong></label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-danger"><i class="ki-outline ki-arrow-left fs-2"></i> Confirmar Devolución</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@php
$activosMapeados = $activos->map(function($a) {
return [
'uuid' => $a->uuid,
'texto' => ($a->tipo->tipo ?? 'Sin Tipo') . ' - ' . ($a->modelo->marca->marca ?? '') . ' ' . ($a->modelo->modelo ?? 'Genérico') . ($a->is_serialized ? ' (S/N: '.$a->serial_number.')' : ''),
'is_serialized' => $a->is_serialized,
'stock_por_almacen' => $a->almacenes->pluck('pivot.cantidad', 'id')
];
});
@endphp
<input type="hidden" id="data_activos_json" value="{{ json_encode($activosMapeados) }}">
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const jsonData = document.getElementById('data_activos_json').value;
        const activosDisponibles = JSON.parse(jsonData);

        // --- PRESTAR ---
        const selectAlmacenPrestar = $('#select_almacen_prestar');
        const selectActivoPrestar = $('#select_activo_prestar');
        const inputCantidadPrestar = document.getElementById('input_cantidad_prestar');
        const badgeStock = document.getElementById('badge_stock_disponible');

        selectAlmacenPrestar.on('change', function() {
            const almacenId = $(this).val();
            selectActivoPrestar.empty().append('<option></option>');
            activosDisponibles.forEach(activo => {
                const stockEnAlmacen = activo.stock_por_almacen[almacenId] || 0;
                if (stockEnAlmacen > 0) {
                    const option = new Option(activo.texto, activo.uuid, false, false);
                    option.setAttribute('data-serialized', activo.is_serialized ? '1' : '0');
                    option.setAttribute('data-stock', stockEnAlmacen);
                    selectActivoPrestar.append(option);
                }
            });
            selectActivoPrestar.trigger('change');
        });

        selectActivoPrestar.on('change', function() {
            const option = $(this).find(':selected');
            const stock = option.attr('data-stock');
            const isSerialized = option.attr('data-serialized') === '1';

            if (stock) {
                badgeStock.textContent = 'Stock disponible: ' + stock;
                badgeStock.style.display = 'block';
            } else {
                badgeStock.style.display = 'none';
            }

            if (isSerialized) {
                inputCantidadPrestar.value = 1;
                inputCantidadPrestar.setAttribute('readonly', true);
            } else {
                inputCantidadPrestar.removeAttribute('readonly');
            }
        });

        // --- DEVOLVER ---
        const selectPrestamoManual = $('#select_prestamo_manual');
        const badgeMax = document.getElementById('badge_max_prestamo');
        const inputCantidadDevolver = document.getElementById('input_cantidad_devolver');
        const hiddenCodigoDevolver = document.getElementById('hidden_codigo_devolver');
        const panelParcialManual = document.getElementById('panel_parcial_manual');
        const spanFaltantesManual = document.getElementById('span_faltantes_manual');

        function actualizarPanelDevolucion() {
            const option = selectPrestamoManual.find(':selected');
            if (!option.val()) return;
            const maxPrestado = parseInt(option.attr('data-max')) || 0;
            const valorTeclado = parseInt(inputCantidadDevolver.value);
            if (!isNaN(valorTeclado) && valorTeclado >= 0 && valorTeclado < maxPrestado) {
                spanFaltantesManual.textContent = maxPrestado - valorTeclado;
                panelParcialManual.style.display = 'block';
            } else {
                panelParcialManual.style.display = 'none';
            }
        }

        selectPrestamoManual.on('change', function() {
            const option = $(this).find(':selected');
            const max = option.attr('data-max');
            const isSerialized = option.attr('data-serialized') === '1';

            hiddenCodigoDevolver.value = option.attr('data-uuid');

            if (max) {
                badgeMax.textContent = 'Cantidad prestada: ' + max;
                badgeMax.style.display = 'block';
                inputCantidadDevolver.value = max;
            }

            if (isSerialized) {
                inputCantidadDevolver.value = 1;
                inputCantidadDevolver.setAttribute('readonly', true);
            } else {
                inputCantidadDevolver.removeAttribute('readonly');
            }
            actualizarPanelDevolucion();
        });

        inputCantidadDevolver.addEventListener('input', actualizarPanelDevolucion);
        selectAlmacenPrestar.trigger('change');
    });
</script>
@endsection