@extends('layout.layout')
@section('content')
<div class="p-4">
    <h2 class="mb-4">Resumen del Sistema</h2>
    <h4 class="text-muted">Últimos 5 Préstamos</h4>

    <table class="table mt-4">
        <thead>
            <tr>
                <th>Activo</th>
                <th>Usuario</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stats['ultimos_prestamos'] as $prestamo)
            <tr>
                <td>{{ $prestamo->activo->almacen ?? 'Equipo Generico' }}</td>
                <td>{{ $prestamo->usuario->name }}</td>
                <td>{{ $prestamo->created_at->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center text-muted">
                    No hay préstamos registrados todavía.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection