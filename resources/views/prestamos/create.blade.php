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
                    <input type="number" name="cantidad_confirmada" class="form-control form-control-solid fs-1 text-center mb-5" value="1" min="1" autofocus required />

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
        const input = document.getElementById('input_codigo');

        input.focus();

        document.addEventListener("click", function(e) {
            if (e.target.tagName !== 'SELECT' && e.target.tagName !== 'OPTION') {
                input.focus();
            }
        });
    });
</script>
@endsection