@extends('layouts.app')

@section('titulo', 'Ficha del paciente - OdontoTrack')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2 mb-0">Ficha del paciente</h1>

    <a href="{{ route('pacientes.index') }}"
       class="btn btn-outline-secondary">
        Volver al listado
    </a>
</div>

<div class="card">
    <div class="card-body">
        <h2 class="h4 mb-4">{{ $paciente->nombre_completo }}</h2>

        <dl class="row mb-0">
            <dt class="col-sm-4">Número de paciente</dt>
            <dd class="col-sm-8">{{ $paciente->id_paciente }}</dd>

            <dt class="col-sm-4">Nombres</dt>
            <dd class="col-sm-8">{{ $paciente->nombres }}</dd>

            <dt class="col-sm-4">Apellidos</dt>
            <dd class="col-sm-8">{{ $paciente->apellidos }}</dd>

            <dt class="col-sm-4">Fecha de nacimiento</dt>
            <dd class="col-sm-8">
                {{ $paciente->fecha_nacimiento?->format('d/m/Y') ?? 'Sin registrar' }}
            </dd>

            <dt class="col-sm-4">Teléfono</dt>
            <dd class="col-sm-8">
                {{ $paciente->telefono ?: 'Sin registrar' }}
            </dd>

            <dt class="col-sm-4">Correo electrónico</dt>
            <dd class="col-sm-8">
                {{ $paciente->correo ?: 'Sin registrar' }}
            </dd>

            <dt class="col-sm-4">Estado</dt>
            <dd class="col-sm-8">
                    <span class="badge {{ $paciente->activo ? 'bg-success' : 'bg-secondary' }}">
                        {{ $paciente->activo ? 'Activo' : 'Inactivo' }}
                    </span>
            </dd>
        </dl>
    </div>
</div>
@endsection
