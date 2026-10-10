@extends('layouts.app')

@section('titulo', 'Registrar paciente - OdontoTrack')

@section('content')
<h1 class="h2 mb-4">Registrar paciente</h1>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('pacientes.store') }}">
            @csrf

            @include('pacientes._form', [
            'textoBoton' => 'Registrar paciente',
            ])
        </form>
    </div>
</div>
@endsection
