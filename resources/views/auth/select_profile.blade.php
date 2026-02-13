@extends('layout.layout_invitados')

@section('content')
<div class="d-flex flex-column flex-column-fluid align-items-center justify-content-center p-10">
    <h1 class="text-dark fw-bolder mb-10">Selecciona tu usuario</h1>

    <div class="row g-5 g-xl-10 w-100 justify-content-center">
        <div class="d-flex flex-column flex-column-fluid align-items-center justify-content-center p-10">
            <div class="w-100 w-md-400px mb-10">
                <form action="/" method="GET" class="position-relative">
                    <i class="ki-outline ki-magnifier fs-2 position-absolute top-50 translate-middle-y ms-4"></i>
                    <input type="text" name="buscar" class="form-control form-control-solid ps-12"
                        placeholder="Buscar empleado..." value="{{ request('buscar') }}">
                </form>
            </div>

            <div class="row g-5 g-xl-10 w-100 justify-content-center">
                @forelse($usuarios as $user)
                <div class="col-md-3">
                </div>
                @empty
                <div class="col-12 text-center text-gray-500">
                    No se han encontrado empleados con ese nombre.
                </div>
                @endforelse
            </div>

            <div class="mt-10">
                {{ $usuarios->links() }}
            </div>
        </div>
        @foreach($usuarios as $user)
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 text-center p-5">
                <div class="symbol symbol-100px symbol-circle mb-4 mx-auto">
                    {{-- Un placeholder con la inicial del nombre --}}
                    <div class="symbol-label fs-2hx fw-bold bg-light-primary text-primary">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                </div>

                <h4 class="text-gray-800 fw-bold">{{ $user->name }}</h4>
                <span class="badge badge-light-success mb-5">{{ $user->rol->rol }}</span>

                {{--login oficial de Breeze --}}
                <a href="{{ route('login', ['email' => $user->email]) }}" class="btn btn-sm btn-primary">
                    Entrar en esta cuenta
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection