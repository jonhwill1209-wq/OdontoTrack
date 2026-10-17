@extends('layouts.app')

@section('titulo', 'Pacientes - OdontoTrack')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2 mb-0">Pacientes</h1>

    @if (auth()->user()->esRecepcionista())
    <a href="{{ route('pacientes.create') }}" class="btn btn-primary">
        Registrar paciente
    </a>
    @endif
</div>

<form method="GET" action="{{ route('pacientes.index') }}"
      class="card card-body mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-md-6">
            <label for="buscar" class="form-label">Buscar paciente</label>
            <input type="search"
                   id="buscar"
                   name="buscar"
                   class="form-control"
                   value="{{ $buscar }}"
                   maxlength="100"
                   placeholder="Nombre, apellido o teléfono">
        </div>

        <div class="col-md-3">
            <label for="estado" class="form-label">Estado</label>
            <select id="estado" name="estado" class="form-select">
                <option value="1" @selected($estado === '1')>
                Activos
                </option>
                <option value="0" @selected($estado === '0')>
                Inactivos
                </option>
                <option value="todos" @selected($estado === 'todos')>
                Todos
                </option>
            </select>
        </div>

        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Buscar</button>
            <a href="{{ route('pacientes.index') }}"
               class="btn btn-outline-secondary">
                Limpiar
            </a>
        </div>
    </div>
</form>

<p class="text-secondary">
    Pacientes encontrados: {{ $pacientes->total() }}
</p>

<div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
        <thead>
        <tr>
            <th scope="col">Nombre completo</th>
            <th scope="col">Teléfono</th>
            <th scope="col">Correo</th>
            <th scope="col">Estado</th>
            <th scope="col">Acciones</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($pacientes as $paciente)
        <tr>
            <td>{{ $paciente->nombre_completo }}</td>
            <td>{{ $paciente->telefono ?: 'Sin registrar' }}</td>
            <td>{{ $paciente->correo ?: 'Sin registrar' }}</td>
            <td>
                            <span class="badge {{ $paciente->activo ? 'bg-success' : 'bg-secondary' }}">
                                {{ $paciente->activo ? 'Activo' : 'Inactivo' }}
                            </span>
            </td>
            <td>
                <a href="{{ route('pacientes.show', $paciente) }}"
                   class="btn btn-outline-primary btn-sm">
                    Ver
                </a>

                @if (auth()->user()->esRecepcionista())
                <a href="{{ route('pacientes.edit', $paciente) }}"
                   class="btn btn-outline-secondary btn-sm">
                    Editar
                </a>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center py-4">
                No se encontraron pacientes.
            </td>
        </tr>
        @endforelse
        </tbody>
    </table>
</div>

{{ $pacientes->links('pagination::bootstrap-5') }}
@endsection
