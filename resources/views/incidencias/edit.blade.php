@extends('layout.layout')

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h3 class="card-title fw-bold">Gestionar Incidencia #{{ $incidencia->id }}</h3>
        <div class="card-toolbar">
            <a href="{{ route('incidencias.index') }}" class="btn btn-sm btn-light">
                <i class="ki-outline ki-arrow-left fs-2"></i> Volver
            </a>
        </div>
    </div>

    <div class="card-body">
        <form action="{{ route('incidencias.update', $incidencia->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="alert alert-secondary d-flex align-items-center p-5 mb-10">
                <i class="ki-outline ki-information-5 fs-2hx text-primary me-4"></i>
                <div class="d-flex flex-column">
                    <h4 class="mb-1 text-primary">Activo: {{ $incidencia->activo->tipo->tipo ?? '' }} {{ $incidencia->activo->modelo->modelo ?? '' }}</h4>
                    <span class="mb-1 text-dark">Reportado por <strong>{{ $incidencia->user->name }}</strong> el {{ \Carbon\Carbon::parse($incidencia->fecha_incidencia)->format('d/m/Y H:i') }}</span>
                </div>
            </div>

            <div class="row mb-6">
                <div class="col-md-12 fv-row">
                    <label class="required form-label fw-bold">Título</label>
                    <input type="text" name="titulo" class="form-control form-control-solid" value="{{ $incidencia->titulo }}" required />
                </div>
            </div>

            <div class="row mb-6">
                <div class="col-md-6 fv-row">
                    <label class="required form-label fw-bold">Nivel / Gravedad</label>
                    <div class="input-group">
                        <select name="nivel_id" id="select_nivel" class="form-select form-select-solid" required>
                            @foreach($niveles as $nivel)
                            <option value="{{ $nivel->id }}" selected="{{$incidencia->nivel->nivel}}">
                                {{ $nivel->nivel }}
                            </option>
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
                            @foreach($estados as $estado)
                            <option value="{{ $estado->id }}" selected="{{$incidencia->estado->estado}}">
                                {{ $estado->estado }}
                            </option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-light-primary" onclick="agregarOpcion('estado')">
                            <i class="ki-outline ki-plus fs-2"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mb-8 fv-row">
                <label class="form-label fw-bold">Descripción / Notas de reparación</label>
                <textarea name="descripcion" class="form-control form-control-solid" rows="5">{{ $incidencia->descripcion }}</textarea>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="ki-outline ki-check-circle fs-2"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
<script>
    function agregarOpcion(tipo) {
        let titulo = tipo === 'nivel' ? 'Nuevo Nivel de Gravedad' : 'Nuevo Estado';
        let ruta = tipo === 'nivel' ? '{{ route("niveles.quick_store") }}' : '{{ route("estados.quick_store") }}';
        let selectId = tipo === 'nivel' ? 'select_nivel' : 'select_estado';
        let campoNombre = tipo;

        Swal.fire({
            title: titulo,
            input: 'text',
            inputLabel: 'Nombre',
            showCancelButton: true,
            confirmButtonText: 'Guardar',
            showLoaderOnConfirm: true,
            preConfirm: (valor) => {
                if (!valor) Swal.showValidationMessage('Debes escribir un nombre');
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
</script>
@endsection