@extends('layouts.app')

@section('titulo', 'Odontólogos')

@push('estilos')
<link href="{{ asset('css/modules/usuarios.css') }}" rel="stylesheet">
@endpush

@section('content')
<header class="ot-encabezado">
    <div>
        <h1>Odontólogos</h1>
        <p>Profesionales que atienden en la clínica: {{ $totales['activos'] }} activos de {{ $totales['total'] }} registrados.</p>
    </div>

    <a href="{{ route('odontologos.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus" aria-hidden="true"></i> Nuevo odontólogo
    </a>
</header>

<form method="GET" action="{{ route('odontologos.index') }}" class="ot-panel ot-filtros" role="search">
    <div class="row g-3 align-items-end">
        <div class="col-12 col-md">
            <label for="buscar" class="form-label">Buscar</label>
            <input type="search" id="buscar" name="buscar" value="{{ $buscar }}" maxlength="100"
                   class="form-control" placeholder="Nombre, correo o número de colegiado">
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
            <a href="{{ route('odontologos.index') }}" class="btn btn-outline-secondary">Limpiar</a>
        </div>
    </div>
</form>

<p class="ot-resumen-lista">Resultados: {{ $odontologos->total() }}</p>

<div class="table-responsive ot-tabla mb-3">
    <table class="table align-middle mb-0">
        <thead>
            <tr>
                <th scope="col">Nombre</th>
                <th scope="col">N.º de colegiado</th>
                <th scope="col" class="d-none d-lg-table-cell">Correo</th>
                <th scope="col">Citas pendientes</th>
                <th scope="col">Estado</th>
                <th scope="col">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($odontologos as $odontologo)
                <tr>
                    <td>
                        {{ $odontologo->usuario->nombre }}
                        <span class="ot-dato-secundario d-lg-none">{{ $odontologo->usuario->correo }}</span>
                    </td>
                    <td>{{ $odontologo->numero_colegiado ?: 'Sin registrar' }}</td>
                    <td class="d-none d-lg-table-cell">{{ $odontologo->usuario->correo }}</td>
                    <td>{{ $odontologo->citas_pendientes_count }}</td>
                    <td><x-estado-activo :activo="$odontologo->usuario->activo" /></td>
                    <td>
                        <div class="ot-tabla-acciones">
                            <a href="{{ route('odontologos.show', $odontologo) }}" class="btn btn-outline-primary btn-sm">Ver</a>
                            <a href="{{ route('odontologos.edit', $odontologo) }}" class="btn btn-outline-secondary btn-sm">Editar</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="ot-vacio">
                        <i class="bi bi-search" aria-hidden="true"></i>
                        <p>No se encontraron odontólogos con esos filtros.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $odontologos->links('pagination::bootstrap-5') }}
@endsection

@push('scripts')
<script src="{{ asset('js/modules/usuarios.js') }}"></script>
@endpush
