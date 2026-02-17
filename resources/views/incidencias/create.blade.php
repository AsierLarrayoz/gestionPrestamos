@extends('layout.layout')

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h3 class="card-title fw-bold">Reportar Nueva Incidencia</h3>
    </div>

    <div class="card-body">
        <form action="{{ route('incidencias.store') }}" method="POST">
            @csrf

            <div class="row mb-6">
                <div class="col-md-6 fv-row">
                    <label class="required form-label fw-bold">Activo Afectado</label>
                    <select name="activo_id" id="select_activo" class="form-select form-select-solid" data-control="select2" data-placeholder="Buscar activo..." required>
                        <option></option>
                        @foreach($activos as $activo)
                        <option value="{{ $activo->id }}" data-uuid="{{ $activo->uuid }}">
                            {{ $activo->tipo->tipo ?? '' }} {{ $activo->modelo->modelo ?? '' }} (S/N: {{ $activo->serial_number ?? 'N/A' }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 fv-row">
                    <label class="form-label fw-bold">Asociar a Préstamo (Opcional)</label>
                    <div class="d-flex gap-2">
                        <select name="prestamo_id" id="select_prestamo" class="form-select form-select-solid" data-control="select2" data-placeholder="Seleccionar préstamo activo...">
                            <option value="">-- Sin asignar --</option>
                            @foreach($prestamos as $prestamo)
                            <option value="{{ $prestamo->id }}" data-activo-id="{{ $prestamo->activo_id }}">
                                {{$prestamo->activo->tipo->tipo}} {{ $prestamo->activo->modelo->modelo ?? '' }} prestado por {{ $prestamo->user->name ?? 'Usuario' }}
                                prestado: {{$prestamo->fecha_prestado}}, devuelto: {{$prestamo->fecha_devuelto?? 'no devuelto'}}
                            </option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-icon btn-light" onclick="$('#select_prestamo').val(null).trigger('change');">
                            <i class="ki-outline ki-cross fs-2"></i>
                        </button>
                    </div>
                    <div class="form-text">Si seleccionas un activo prestado, intentaremos seleccionar esto automáticamente.</div>
                </div>
            </div>

            <div class="row mb-6">
                <div class="col-md-4 fv-row">
                    <label class="required form-label fw-bold">Fecha</label>
                    <input type="datetime-local" name="fecha_incidencia" class="form-control form-control-solid" value="{{ now()->format('Y-m-d\TH:i') }}" required />
                </div>
                <div class="col-md-8 fv-row">
                    <label class="required form-label fw-bold">Título</label>
                    <input type="text" name="titulo" class="form-control form-control-solid" placeholder="Resumen del problema" required />
                </div>
            </div>

            <div class="row mb-6">

                <div class="col-md-6 fv-row">
                    <label class="required form-label fw-bold">Nivel / Gravedad</label>
                    <div class="input-group">
                        <select name="nivel_id" id="select_nivel" class="form-select form-select-solid" required>
                            <option value="">Selecciona...</option>
                            @foreach($niveles as $nivel)
                            <option value="{{ $nivel->id }}">{{ $nivel->nivel }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-light-primary" onclick="agregarOpcion('nivel')">
                            <i class="ki-outline ki-plus fs-2"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-6 fv-row">
                    <label class="form-label fw-bold">Estado Inicial</label>
                    <div class="input-group">
                        <select name="estado_id" id="select_estado" class="form-select form-select-solid">
                            <option value="">Selecciona...</option>
                            @foreach($estados as $estado)
                            <option value="{{ $estado->id }}">{{ $estado->estado }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-light-primary" onclick="agregarOpcion('estado')">
                            <i class="ki-outline ki-plus fs-2"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mb-8 fv-row">
                <label class="form-label fw-bold">Descripción</label>
                <textarea name="descripcion" class="form-control form-control-solid" rows="4"></textarea>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-danger">Registrar Incidencia</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // 1. LÓGICA PARA AGREGAR NUEVOS NIVELES/ESTADOS (AJAX)
    function agregarOpcion(tipo) {
        let titulo = tipo === 'nivel' ? 'Nuevo Nivel de Gravedad' : 'Nuevo Estado';
        let ruta = tipo === 'nivel' ? '{{ route("niveles.quick_store") }}' : '{{ route("estados.quick_store") }}';
        let selectId = tipo === 'nivel' ? 'select_nivel' : 'select_estado';
        let campoNombre = tipo; // 'nivel' o 'estado'

        Swal.fire({
            title: titulo,
            input: 'text',
            inputLabel: 'Nombre',
            showCancelButton: true,
            confirmButtonText: 'Guardar',
            showLoaderOnConfirm: true,
            preConfirm: (valor) => {
                if (!valor) Swal.showValidationMessage('Debes escribir un nombre');

                // Preparamos datos
                let data = {
                    _token: '{{ csrf_token() }}'
                };
                data[campoNombre] = valor;

                return fetch(ruta, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => {
                        if (!response.ok) throw new Error(response.statusText);
                        return response.json();
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`Error: ${error}`);
                    });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Agregamos la nueva opción al Select y la seleccionamos
                let data = result.value;
                let nuevaOpcion = new Option(data[campoNombre], data.id, true, true);
                document.getElementById(selectId).add(nuevaOpcion);

                Swal.fire({
                    icon: 'success',
                    title: 'Añadido',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            }
        });
    }

    // 2. LÓGICA DE VINCULACIÓN INTELIGENTE (Activo -> Préstamo)
    $(document).ready(function() {
        // Cuando cambia el Activo...
        $('#select_activo').on('change', function() {
            let activoId = $(this).val();

            // Buscamos si hay un préstamo en el desplegable que coincida con este activo
            // El atributo data-activo-id lo pusimos en el HTML del option
            let prestamoEncontrado = false;

            $('#select_prestamo option').each(function() {
                if ($(this).data('activo-id') == activoId) {
                    $('#select_prestamo').val($(this).val()).trigger('change');
                    prestamoEncontrado = true;
                }
            });

            // Si no encontramos préstamo asociado, limpiamos el campo préstamo (opcional)
            if (!prestamoEncontrado) {
                $('#select_prestamo').val(null).trigger('change');
            }
        });
    });
</script>
@endsection