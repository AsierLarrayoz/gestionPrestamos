@extends('layout.layout')

@section('content')
<div class="d-flex flex-column flex-center p-10">

    <div class="card card-flush w-md-600px py-5">
        <div class="card-body py-15 py-lg-20 text-center">

            @if(session('success'))
            <div class="alert alert-dismissible bg-light-success d-flex flex-column flex-sm-row p-5 mb-10">
                <i class="ki-outline ki-check-circle fs-2hx text-success me-4 mb-5 mb-sm-0"></i>
                <div class="d-flex flex-column text-start pe-0 pe-sm-10">
                    <h4 class="fw-semibold">Operación Correcta</h4>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-dismissible bg-light-danger d-flex flex-column flex-sm-row p-5 mb-10">
                <i class="ki-outline ki-cross-circle fs-2hx text-danger me-4 mb-5 mb-sm-0"></i>
                <div class="d-flex flex-column text-start pe-0 pe-sm-10">
                    <h4 class="fw-semibold">Atención</h4>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
            @endif

            <form action="{{ route('prestamos.store') }}" method="POST" id="form_scanner">
                @csrf

                <div class="fv-row mb-8 text-start">
                    <label class="form-label fw-bold">Ubicación Actual:</label>
                    <select name="almacen_id" class="form-select form-select-solid" required>
                        @foreach($almacenes as $almacen)
                        <option value="{{ $almacen->id }}">{{ $almacen->almacen }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="fv-row mb-10">
                    <input type="text"
                        id="input_codigo"
                        name="codigo"
                        class="form-control form-control-solid form-control-lg fs-2x text-center"
                        placeholder="Escanea aquí..."
                        autofocus
                        autocomplete="off" />
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <span class="indicator-label">Procesar</span>
                </button>
            </form>

        </div>
    </div>
</div>

@if(session('abrir_modal'))
<div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Gestión de Stock: {{ session('activoOp')->modelo->modelo ?? 'Item' }}</h3>
                <a href="{{ route('prestamos.create') }}" class="btn btn-icon btn-sm btn-active-light-primary ms-2"><i class="ki-outline ki-cross fs-1"></i></a>
            </div>

            <form action="{{ route('prestamos.store') }}" method="POST">
                @csrf
                <input type="hidden" name="codigo" value="{{ session('codigoActivo') }}">
                <input type="hidden" name="almacen_id" value="{{ session('almacenActual') }}">

                <div class="modal-body text-center">

                    <div class="d-flex justify-content-center gap-4 mb-5">
                        <div class="border p-3 rounded">
                            <small class="text-muted d-block">En Almacén</small>
                            <span class="fs-2 fw-bold text-success">{{ session('stockActual') }}</span>
                        </div>

                        @if(session('prestamosPendientes') && session('prestamosPendientes')->count() > 0)
                        <div class="border p-3 rounded bg-light-warning">
                            <small class="text-muted d-block">Total Prestado</small>
                            <span class="fs-2 fw-bold text-warning">{{ session('cantidadYaPrestada') }}</span>
                        </div>
                        @endif
                    </div>

                    {{-- DESPLEGABLE DE PRÉSTAMOS (Solo si hay préstamos activos) --}}
                    @if(session('prestamosPendientes') && session('prestamosPendientes')->count() > 0)
                    <div class="mb-5 text-start bg-light rounded p-4">
                        <label class="form-label fw-bold text-dark">Si vas a devolver, selecciona el préstamo:</label>
                        <select name="prestamo_id" id="select_prestamo" class="form-select form-select-solid border-warning">
                            @foreach(session('prestamosPendientes') as $prestamo)
                            <option value="{{ $prestamo->id }}" data-max="{{ $prestamo->cantidad_prestada }}">
                                {{ $prestamo->cantidad_prestada }} ud. (Prestado el {{ $prestamo->fecha_prestado->format('d/m/y') }}) - {{ $prestamo->descripcion ?? 'Sin descripción' }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    {{-- INPUTS DE CANTIDAD Y DESCRIPCIÓN --}}
                    <div class="row text-start mb-5">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Cantidad a operar:</label>
                            <input type="number" id="input_cantidad" name="cantidad_confirmada" class="form-control form-control-solid fs-1 text-center" value="1" min="0" required />
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Descripción / Destinatario (Solo al Prestar):</label>
                            <input type="text" name="descripcion" class="form-control form-control-solid fs-4" placeholder="Ej: Proyecto X - Asier" autocomplete="off" />
                        </div>
                    </div>

                    {{-- PANEL ROJO DE DEVOLUCIÓN PARCIAL (Oculto por defecto mediante JS) --}}
                    @if(session('prestamosPendientes') && session('prestamosPendientes')->count() > 0)
                    <div id="opciones_parciales" class="text-start bg-light-danger border border-danger p-4 rounded mb-5" style="display: none;">
                        <label class="form-label fw-bold text-danger mb-3">
                            <i class="ki-outline ki-warning fs-3 text-danger me-1"></i> ¡Devolución Parcial Detectada!
                        </label>
                        <p class="text-muted fs-7 mb-4">Faltan <span id="span_faltantes" class="fw-bold fs-5 text-dark"></span> unidades de este préstamo. ¿Qué hacemos con ellas?</p>

                        <div class="form-check form-check-custom form-check-solid mb-3">
                            <input class="form-check-input" type="radio" name="tipo_devolucion_parcial" value="dividir" id="radio_dividir" checked />
                            <label class="form-check-label text-gray-700" for="radio_dividir">
                                <strong>Dejar pendientes</strong> (El operario las devolverá más tarde)
                            </label>
                        </div>

                        <div class="form-check form-check-custom form-check-solid">
                            <input class="form-check-input" type="radio" name="tipo_devolucion_parcial" value="finalizar" id="radio_finalizar" />
                            <label class="form-check-label text-gray-700" for="radio_finalizar">
                                <strong>Cerrar préstamo con pérdidas</strong> (Equipos perdidos o rotos)
                            </label>
                        </div>
                    </div>
                    @endif

                </div>

                @if(Auth::user()->hasPermission('prestamos.escribir'))
                <div class="modal-footer justify-content-center">
                    <button type="submit" name="accion_confirmada" value="prestar" class="btn btn-primary">
                        <i class="ki-outline ki-plus fs-2"></i> Prestar Nuevo
                    </button>

                    @if(session('prestamosPendientes') && session('prestamosPendientes')->count() > 0)
                    <button type="submit" name="accion_confirmada" value="devolver" class="btn btn-danger">
                        <i class="ki-outline ki-arrow-left fs-2"></i> Devolver Seleccionado
                    </button>
                    @endif
                </div>
                @endif
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const inputCodigo = document.getElementById('input_codigo');
        const inputCantidad = document.getElementById('input_cantidad');
        const divOpciones = document.getElementById('opciones_parciales');
        const selectPrestamo = document.getElementById('select_prestamo');
        const spanFaltantes = document.getElementById('span_faltantes');

        // --- 1. FOCO INICIAL ---
        if (inputCantidad) {
            inputCantidad.focus();
            inputCantidad.select();
        } else if (inputCodigo) {
            inputCodigo.focus();
        }

        // --- 2. CONTROL DEL CLIC (Anti-robo de foco) ---
        document.addEventListener("click", function(e) {
            const tagsPermitidos = ['INPUT', 'SELECT', 'OPTION', 'BUTTON', 'LABEL'];
            if (tagsPermitidos.includes(e.target.tagName)) return;

            if (inputCantidad) {
                inputCantidad.focus();
            } else if (inputCodigo) {
                inputCodigo.focus();
            }
        });

        // --- 3. LÓGICA DE DEVOLUCIÓN PARCIAL MULTI-PRÉSTAMO ---
        if (inputCantidad && divOpciones) {
            function verificarParcial() {
                const valorTeclado = parseInt(inputCantidad.value);

                // Leemos el máximo de la opción que el operario haya seleccionado
                let maxPrestado = 0;
                if (selectPrestamo && selectPrestamo.options.length > 0) {
                    const opcionSeleccionada = selectPrestamo.options[selectPrestamo.selectedIndex];
                    maxPrestado = parseInt(opcionSeleccionada.getAttribute('data-max')) || 0;
                }

                // Si el valor es menor al máximo del préstamo seleccionado (ej: 15 < 20)
                if (!isNaN(valorTeclado) && valorTeclado >= 0 && valorTeclado < maxPrestado) {
                    spanFaltantes.textContent = maxPrestado - valorTeclado;
                    divOpciones.style.display = 'block';
                } else {
                    divOpciones.style.display = 'none';
                }
            }

            // Escuchar cambios en la cantidad
            inputCantidad.addEventListener('input', verificarParcial);

            // Escuchar cambios si el usuario selecciona otro préstamo en el desplegable
            if (selectPrestamo) {
                selectPrestamo.addEventListener('change', verificarParcial);
            }

            // Forzar verificación inicial
            verificarParcial();
        }
    });
</script>
@endsection