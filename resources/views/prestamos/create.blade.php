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
    <div class="modal-dialog modal-dialog-centered">
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

                        @if(session('prestamoExistente'))
                        <div class="border p-3 rounded bg-light-warning">
                            <small class="text-muted d-block">Prestado (Pendiente)</small>
                            <span class="fs-2 fw-bold text-warning">{{ session('cantidadYaPrestada') }}</span>
                        </div>
                        @endif
                    </div>

                    <label class="form-label fw-bold">Cantidad a operar:</label>
                    <input type="number" id="input_cantidad" name="cantidad_confirmada" class="form-control form-control-solid fs-1 text-center mb-5" value="1" min="0" autofocus required />

                    {{-- NUEVO BLOQUE: Opciones de devolución parcial (Oculto por defecto mediante JS) --}}
                    @if(session('prestamoExistente'))
                    <div id="opciones_parciales" class="text-start bg-light-danger border border-danger p-4 rounded mb-5" style="display: none;">
                        <label class="form-label fw-bold text-danger mb-3">
                            <i class="ki-outline ki-warning fs-3 text-danger me-1"></i> ¡Devolución Parcial Detectada!
                        </label>
                        <p class="text-muted fs-7 mb-4">Faltan <span id="span_faltantes" class="fw-bold fs-5 text-dark"></span> unidades. ¿Qué hacemos con ellas?</p>

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
                    {{-- FIN NUEVO BLOQUE --}}

                </div>

                @if(Auth::user()->hasPermission('prestamos.escribir'))
                <div class="modal-footer justify-content-center">
                    <button type="submit" name="accion_confirmada" value="prestar" class="btn btn-primary">
                        <i class="ki-outline ki-plus fs-2"></i> Prestar
                    </button>

                    @if(session('prestamoExistente'))
                    <button type="submit" name="accion_confirmada" value="devolver" class="btn btn-danger">
                        <i class="ki-outline ki-arrow-left fs-2"></i> Devolver
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

        // --- 3. LÓGICA DE DEVOLUCIÓN PARCIAL (Corregida para evitar errores de sintaxis) ---
        // Lo envolvemos en comillas para que el IDE no falle, y usamos parseInt para asegurarnos de que es un número.
        // Si no hay sesión, Blade imprimirá "0".
        const maxPrestado = parseInt("{{ session('cantidadYaPrestada', 0) }}") || 0;

        if (inputCantidad && divOpciones) {
            const spanFaltantes = document.getElementById('span_faltantes');

            // Función que comprueba si debe mostrar el panel rojo
            function verificarParcial() {
                const valorTeclado = parseInt(inputCantidad.value);

                // Si el valor es un número, mayor o igual a 0 y menor que el total prestado (Ej: 35 < 50)
                if (!isNaN(valorTeclado) && valorTeclado >= 0 && valorTeclado < maxPrestado) {
                    spanFaltantes.textContent = maxPrestado - valorTeclado; // Faltan 15
                    divOpciones.style.display = 'block';
                } else {
                    divOpciones.style.display = 'none';
                }
            }

            // Que escuche cada vez que tocas las flechitas o tecleas
            inputCantidad.addEventListener('input', verificarParcial);

            // Que compruebe inmediatamente al cargar la pantalla
            verificarParcial();
        }
    });
</script>
@endsection