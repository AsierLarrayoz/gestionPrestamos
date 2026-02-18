@extends('layout.layout')

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h3 class="card-title fw-bold">Nueva Reserva de Material</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('reservas.store') }}" method="POST">
            @csrf

            <div class="row mb-6">
                <div class="col-md-6 fv-row">
                    <label class="required form-label fw-bold">¿Qué necesitas?</label>
                    <select name="tipo_id" class="form-select form-select-solid" data-control="select2" data-placeholder="Selecciona el tipo de material">
                        <option></option>
                        @foreach($tipos as $tipo)
                        <option value="{{ $tipo->id }}">{{ $tipo->tipo }}</option>
                        @endforeach
                    </select>
                    <div class="text-muted fs-7 mt-1">Ej: Audioguías, Portátiles, Cables...</div>
                </div>

                <div class="col-md-6 fv-row">
                    <label class="required form-label fw-bold">Cantidad Estimada</label>
                    <input type="number" name="cantidad" class="form-control form-control-solid" value="1" min="1" />
                </div>
            </div>

            <div class="row mb-6">
                <div class="col-md-6 fv-row">
                    <label class="required form-label fw-bold">Desde cuándo</label>
                    <input type="datetime-local" name="fecha_inicio" class="form-control form-control-solid" required />
                </div>

                <div class="col-md-6 fv-row">
                    <label class="required form-label fw-bold">Hasta cuándo</label>
                    <input type="datetime-local" name="fecha_fin" class="form-control form-control-solid" required />
                </div>
            </div>

            <div class="mb-6">
                <label class="form-label fw-bold">Motivo / Evento</label>
                <textarea name="descripcion" class="form-control form-control-solid" rows="3" placeholder="Ej: Visita guiada grupo escolar..."></textarea>
            </div>

            <div class="d-flex justify-content-end">
                @if(Auth::user()->hasPermission('reservas.escribir'))
                <a href="{{ route('reservas.index') }}" class="btn btn-light me-3">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="ki-outline ki-calendar-add fs-2"></i> Planificar Reserva
                </button>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection