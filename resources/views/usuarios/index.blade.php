@extends('layouts.app')

@section('titulo', 'Usuarios')

@push('estilos')
<link href="{{ asset('css/modules/usuarios.css') }}" rel="stylesheet">
@endpush

@section('content')
<header class="ot-encabezado">
    <div>
        <h1>Usuarios</h1>
        <p>Cuentas de acceso del personal: {{ $totales['activos'] }} activas de {{ $totales['total'] }} registradas.</p>
    </div>

    <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus" aria-hidden="true"></i> Nuevo usuario
    </a>
</header>

<form method="GET" action="{{ route('usuarios.index') }}" class="ot-panel ot-filtros" role="search">
    <div class="row g-3 align-items-end">
        <div class="col-12 col-md">
            <label for="buscar" class="form-label">Buscar</label>
            <input type="search" id="buscar" name="buscar" value="{{ $buscar }}" maxlength="100"
                   class="form-control" placeholder="Nombre o correo">
        </div>

        <div class="col-6 col-md-3">
            <label for="rol" class="form-label">Rol</label>
            <select id="rol" name="rol" class="form-select" data-autoenviar>
                <option value="">Todos</option>
                @foreach ($roles as $opcion)
                    <option value="{{ $opcion->value }}" @selected($rol === $opcion->value)>{{ $opcion->etiqueta() }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-6 col-md-2">
            <label for="estado" class="form-label">Estado</label>
            <select id="estado" name="estado" class="form-select" data-autoenviar>
                <option value="1" @selected($estado === '1')>Activos</option>
                <option value="0" @selected($estado === '0')>Inactivos</option>
                <option value="todos" @selected($estado === 'todos')>Todos</option>
            </select>
        </div>

        <div class="col-12 col-md-auto d-flex gap-2">
            <button type="submit" class="btn btn-primary">Buscar</button>
            <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">Limpiar</a>
        </div>
    </div>
</form>

<p class="ot-resumen-lista">Resultados: {{ $usuarios->total() }}</p>

<div class="table-responsive ot-tabla mb-3">
    <table class="table align-middle mb-0">
        <thead>
            <tr>
                <th scope="col">Nombre</th>
                <th scope="col" class="d-none d-md-table-cell">Correo</th>
                <th scope="col">Rol</th>
                <th scope="col">Estado</th>
                <th scope="col">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($usuarios as $usuario)
                <tr>
                    <td>
                        {{ $usuario->nombre }}
                        @if ($usuario->is(auth()->user()))
                            <span class="badge text-bg-light border ms-1">Tú</span>
                        @endif
                        <span class="ot-dato-secundario d-md-none">{{ $usuario->correo }}</span>
                    </td>
                    <td class="d-none d-md-table-cell">{{ $usuario->correo }}</td>
                    <td><span class="ot-rol ot-rol-{{ strtolower($usuario->rol) }}">{{ $usuario->rol_etiqueta }}</span></td>
                    <td><x-estado-activo :activo="$usuario->activo" /></td>
                    <td>
                        <div class="ot-tabla-acciones">
                            <a href="{{ route('usuarios.show', $usuario) }}" class="btn btn-outline-primary btn-sm">Ver</a>
                            <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-outline-secondary btn-sm">Editar</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="ot-vacio">
                        <i class="bi bi-search" aria-hidden="true"></i>
                        <p>No se encontraron usuarios con esos filtros.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $usuarios->links('pagination::bootstrap-5') }}
@endsection

@push('scripts')
<script src="{{ asset('js/modules/usuarios.js') }}"></script>
@endpush
