@extends('layouts.app')

@section('titulo', 'Consultorios')

@push('estilos')
<link href="{{ asset('css/modules/usuarios.css') }}" rel="stylesheet">
@endpush

@section('content')
<header class="ot-encabezado">
    <div>
        <h1>Consultorios</h1>
        <p>Espacios disponibles para agendar citas: {{ $totales['activos'] }} activos de {{ $totales['total'] }} registrados.</p>
    </div>

    <a href="{{ route('consultorios.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg" aria-hidden="true"></i> Nuevo consultorio
    </a>
</header>

<form method="GET" action="{{ route('consultorios.index') }}" class="ot-panel ot-filtros" role="search">
    <div class="row g-3 align-items-end">
        <div class="col-12 col-md">
            <label for="buscar" class="form-label">Buscar</label>
            <input type="search" id="buscar" name="buscar" value="{{ $buscar }}" maxlength="80"
                   class="form-control" placeholder="Nombre o número">
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <label for="estado" class="form-label">Estado</label>
            <select id="estado" name="estado" class="form-select" data-autoenviar>
                <option value="1" @selected($estado === '1')>Activos</option>
                <option value="0" @selected($estado === '0')>Inactivos</option>
                <option value="todos" @selected($estado === 'todos')>Todos</option>
            </select>
        </div>

        <div class="col-12 col-md-auto d-flex gap-2">
            <button type="submit" class="btn btn-primary">Buscar</button>
            <a href="{{ route('consultorios.index') }}" class="btn btn-outline-secondary">Limpiar</a>
        </div>
    </div>
</form>

<p class="ot-resumen-lista">Resultados: {{ $consultorios->total() }}</p>

<div class="table-responsive ot-tabla mb-3">
    <table class="table align-middle mb-0">
        <thead>
            <tr>
                <th scope="col">Consultorio</th>
                <th scope="col">Citas pendientes</th>
                <th scope="col" class="d-none d-sm-table-cell">Citas en el historial</th>
                <th scope="col">Estado</th>
                <th scope="col">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($consultorios as $consultorio)
                <tr>
                    <td>{{ $consultorio->nombre }}</td>
                    <td>{{ $consultorio->citas_pendientes_count }}</td>
                    <td class="d-none d-sm-table-cell">{{ $consultorio->citas_count }}</td>
                    <td><x-estado-activo :activo="$consultorio->activo" /></td>
                    <td>
                        <div class="ot-tabla-acciones">
                            <a href="{{ route('consultorios.show', $consultorio) }}" class="btn btn-outline-primary btn-sm">Ver</a>
                            <a href="{{ route('consultorios.edit', $consultorio) }}" class="btn btn-outline-secondary btn-sm">Editar</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="ot-vacio">
                        <i class="bi bi-search" aria-hidden="true"></i>
                        <p>No se encontraron consultorios con esos filtros.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $consultorios->links('pagination::bootstrap-5') }}
@endsection

@push('scripts')
<script src="{{ asset('js/modules/usuarios.js') }}"></script>
@endpush
