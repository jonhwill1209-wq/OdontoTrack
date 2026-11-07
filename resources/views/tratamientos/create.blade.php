@extends('layouts.app')

@section('titulo', 'Nuevo tratamiento')

@section('content')
<div class="mb-4">
    <h1 class="h2">Nuevo tratamiento</h1>
    <p class="text-secondary mb-0">Paciente: {{ $expediente->paciente->nombre_completo }}</p>
</div>
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('tratamientos.store', $expediente) }}">
        @csrf
        @php($tratamiento = new \App\Models\Tratamiento)
        @include('tratamientos._form', ['textoBoton' => 'Guardar tratamiento'])
    </form>
</div></div>
@endsection
