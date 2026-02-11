@extends('layout.layout')
@section('content')
<div class="container">
    <h1>Bienvenido al Sistema de Préstamos</h1>
    <p>Estás logueado como: <strong>{{ auth()->check() ? auth()->user()->name : 'Invitado' }}</strong></p>
</div>
@endsection