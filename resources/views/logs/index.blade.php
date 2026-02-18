@extends('layout.layout')

@section('content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <h3 class="fw-bolder">Logs del Sistema</h3>
        </div>
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('logs.index') }}" class="mb-8">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Fecha</label>
                    <input type="date" name="fecha" class="form-control form-control-solid" value="{{ $fecha }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Usuario</label>
                    <select name="user_id" class="form-select form-select-solid" data-control="select2">
                        <option value="">Todos</option>
                        @foreach($usuarios as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-row-bordered gy-4 gs-7">
                <thead>
                    <tr class="fw-bold fs-6 text-gray-800">
                        <th>Hora</th>
                        <th>Usuario</th>
                        <th>Método</th>
                        <th>URL</th>
                        <th>Estado</th>
                        <th>Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td>{{ $log->created_at->format('H:i:s') }}</td>
                        <td>{{ $log->user->name ?? 'Invitado' }}</td>
                        <td>
                            <span class="badge {{ $log->method == 'POST' ? 'badge-light-warning' : 'badge-light-info' }}">
                                {{ $log->method }}
                            </span>
                        </td>
                        <td class="text-truncate" style="max-width: 250px;">{{ $log->url }}</td>
                        <td>
                            <span class="badge {{ $log->status >= 400 ? 'badge-light-danger' : 'badge-light-success' }}">
                                {{ $log->status }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary"
                                onclick="verDetalles('{{ $log->request_id }}')">
                                <i class="ki-outline ki-eye fs-2"></i>
                            </button>

                            <div id="data-{{ $log->request_id }}" class="d-none">
                                <h5>Payload (JSON)</h5>
                                <pre class="bg-dark text-white p-4 rounded">{{ json_encode($log->payload, JSON_PRETTY_PRINT) }}</pre>
                                @if($log->error)
                                <h5 class="mt-4 text-danger">Excepción</h5>
                                <pre class="bg-light-danger p-4 rounded">{{ $log->error }}</pre>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $logs->links() }}
    </div>
</div>

<div class="modal fade" id="modalDetalles" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Detalles de la Petición</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                    <i class="ki-outline ki-cross fs-1"></i>
                </div>
            </div>
            <div class="modal-body" id="modalBody"></div>
        </div>
    </div>
</div>

<script>
    function verDetalles(id) {
        const content = document.getElementById('data-' + id).innerHTML;
        document.getElementById('modalBody').innerHTML = content;
        new bootstrap.Modal(document.getElementById('modalDetalles')).show();
    }
</script>
@endsection