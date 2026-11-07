@extends('layouts.app')

@section('titulo', 'Expedientes clínicos')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="h2 mb-1">Expedientes clínicos</h1>
        <p class="text-secondary mb-0">Consulta y actualiza la información clínica de cada paciente.</p>
    </div>
</div>

<form method="GET" action="{{ route('expedientes.index') }}" class="card card-body mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-md-9">
            <label for="buscar" class="form-label">Buscar paciente</label>
            <input id="buscar" name="buscar" type="search" class="form-control" maxlength="100"
                   value="{{ $buscar }}" placeholder="Nombre o apellido">
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Buscar</button>
            <a href="{{ route('expedientes.index') }}" class="btn btn-outline-secondary">Limpiar</a>
        </div>
    </div>
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
            <tr>
                <th scope="col">Paciente</th>
                <th scope="col">Teléfono</th>
                <th scope="col">Estado</th>
                <th scope="col">Acción</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($pacientes as $paciente)
                <tr>
                    <td>{{ $paciente->nombre_completo }}</td>
                    <td>{{ $paciente->telefono ?: 'No registrado' }}</td>
                    <td><span class="badge {{ $paciente->activo ? 'text-bg-success' : 'text-bg-secondary' }}">
                        {{ $paciente->activo ? 'Activo' : 'Inactivo' }}
                    </span></td>
                    <td><a class="btn btn-sm btn-outline-primary" href="{{ route('expedientes.show', $paciente) }}">
                        Abrir expediente
                    </a></td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-secondary py-4">No hay pacientes que coincidan con la búsqueda.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $pacientes->links('pagination::bootstrap-5') }}</div>
@endsection
