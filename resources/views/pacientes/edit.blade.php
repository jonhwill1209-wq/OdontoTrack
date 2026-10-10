@extends('layouts.app')

@section('titulo', 'Editar paciente - OdontoTrack')

@section('content')
<h1 class="h2 mb-4">Editar paciente</h1>

<div class="card">
    <div class="card-body">
        <form method="POST"
              action="{{ route('pacientes.update', $paciente) }}">
            @csrf
            @method('PUT')

            @include('pacientes._form', [
            'paciente' => $paciente,
            'textoBoton' => 'Guardar cambios',
            ])
        </form>
    </div>
</div>
@endsection
