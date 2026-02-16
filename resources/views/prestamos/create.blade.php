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
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const input = document.getElementById('input_codigo');

        // Enfocar al cargar
        input.focus();

        // "Trampa de Foco": Si hace clic fuera (excepto en el select), vuelve al input
        document.addEventListener("click", function(e) {
            if (e.target.tagName !== 'SELECT' && e.target.tagName !== 'OPTION') {
                input.focus();
            }
        });
    });
</script>
@endsection