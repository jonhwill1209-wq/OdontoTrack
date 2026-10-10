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
                {{ $paciente->fecha_nacimiento?->format('d/m/Y') ?? 'No registrada' }}
            </dd>

            <dt class="col-sm-4">Teléfono</dt>
            <dd class="col-sm-8">
                {{ $paciente->telefono ?: 'No registrado' }}
            </dd>

            <dt class="col-sm-4">Correo electrónico</dt>
            <dd class="col-sm-8">
                {{ $paciente->correo ?: 'No registrado' }}
            </dd>

            <dt class="col-sm-4">Estado</dt>
            <dd class="col-sm-8">
                @if ($paciente->activo)
                <span class="badge bg-success">Activo</span>
                @else
                <span class="badge bg-secondary">Inactivo</span>
                @endif
            </dd>
        </dl>
    </div>
</div>

@if (auth()->user()?->esRecepcionista())
<div class="d-flex flex-wrap gap-2 mt-3">
    <a href="{{ route('pacientes.edit', $paciente) }}"
       class="btn btn-primary">
        Editar paciente
    </a>

    @if ($paciente->activo)
    <form method="POST"
          action="{{ route('pacientes.destroy', $paciente) }}"
          onsubmit="return confirm('¿Deseas desactivar este paciente? Se conservarán sus datos y su expediente.');">
        @csrf
        @method('DELETE')

        <button type="submit" class="btn btn-outline-danger">
            Desactivar paciente
        </button>
    </form>
    @else
    <form method="POST"
          action="{{ route('pacientes.activar', $paciente) }}"
          onsubmit="return confirm('¿Deseas reactivar este paciente?');">
        @csrf
        @method('PATCH')

        <button type="submit" class="btn btn-success">
            Reactivar paciente
        </button>
    </form>
    @endif
</div>
@endif
@endsection
