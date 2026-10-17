@extends('layouts.app')

@section('titulo', 'Editar cita - OdontoTrack')

@section('content')
<h1 class="h2 mb-4">Editar cita</h1>

<div class="card mb-3">
    <div class="card-body">
        <form method="POST" action="{{ route('citas.update', $cita) }}">
            @csrf
            @method('PUT')
            @include('citas._form', ['textoBoton' => 'Guardar cambios'])
        </form>
    </div>
</div>

{{-- Formulario aparte: no se pueden anidar formularios --}}
<div class="card border-danger">
    <div class="card-body d-flex justify-content-between align-items-center">
        <span>Cancelar esta cita (el registro se conserva con estado CANCELADA).</span>
        <form method="POST" action="{{ route('citas.cancelar', $cita) }}"
              onsubmit="return confirm('¿Seguro que deseas cancelar esta cita?');">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-outline-danger">Cancelar cita</button>
        </form>
    </div>
</div>
@endsection
