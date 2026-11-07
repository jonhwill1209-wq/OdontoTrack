@extends('layouts.app')

@section('titulo', 'Editar tratamiento')

@section('content')
<div class="mb-4">
    <h1 class="h2">Editar tratamiento</h1>
    <p class="text-secondary mb-0">Paciente: {{ $tratamiento->expediente->paciente->nombre_completo }}</p>
</div>
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('tratamientos.update', $tratamiento) }}">
        @csrf @method('PUT')
        @include('tratamientos._form', ['textoBoton' => 'Guardar cambios'])
    </form>
</div></div>
@endsection
