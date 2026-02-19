@extends('layout.layout')

@section('content')
<div class="card card-flush">
    <div class="card-header align-items-center py-5 gap-2 gap-md-5">
        <div class="card-title">
            <h3 class="fw-bold">Roles y Permisos</h3>
        </div>
        <div class="card-toolbar">
            @if(Auth::user()->hasPermission('roles.escribir'))
            <a href="{{ route('permisos.create') }}" class="btn btn-primary">
                <i class="ki-outline ki-shield-tick fs-2"></i> Crear Nuevo Rol
            </a>
            @endif
        </div>
    </div>

    <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                        <th class="min-w-150px">Nombre del Rol</th>
                        <th class="min-w-100px">Última Actualización</th>
                        <th class="text-end min-w-100px">Acciones</th>
                    </tr>
                </thead>
                <tbody class="fw-semibold text-gray-600">
                    @foreach($roles as $rol)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40px me-3">
                                    <span class="symbol-label bg-light-primary text-primary fw-bold">
                                        {{ substr($rol->name, 0, 1) }}
                                    </span>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="text-gray-800 fw-bold mb-1">{{ $rol->name }}</span>
                                    <span class="text-muted fs-7">{{ $rol->label }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            {{ $rol->updated_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="text-end">
                            <a href="{{ route('permisos.edit', $rol->id) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                <i class="ki-outline ki-pencil fs-2"></i>
                            </a>

                            {{-- No dejar borrar al Super Admin (ID 1) --}}
                            @if($rol->id !== 1 && Auth::user()->hasPermission('usuarios.escribir'))
                            <form action="{{ route('permisos.destroy', $rol->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Borrar este rol?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm"
                                    {{-- Usamos la relación count() --}}
                                    {{ $rol->permisos->count() > 0 ? '' : '' }}
                                    title="Eliminar Rol">
                                    <i class="ki-outline ki-trash fs-2"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection