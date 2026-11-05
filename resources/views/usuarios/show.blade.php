@extends('layouts.app')

@section('titulo', 'Ficha de usuario')

@push('estilos')
<link href="{{ asset('css/modules/usuarios.css') }}" rel="stylesheet">
@endpush

@section('content')
<header class="ot-encabezado">
    <div>
        <h1>{{ $usuario->nombre }}</h1>
        <p>
            <span class="ot-rol ot-rol-{{ strtolower($usuario->rol) }}">{{ $usuario->rol_etiqueta }}</span>
            <x-estado-activo :activo="$usuario->activo" class="ms-1" />
        </p>
    </div>

    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left" aria-hidden="true"></i> Volver al listado
    </a>
</header>

<div class="row g-3">
    <div class="col-lg-8">
        <section class="ot-panel h-100">
            <div class="ot-panel-cabecera">
                <h2>Datos de la cuenta</h2>
            </div>

            <dl class="row ot-ficha mb-0">
                <dt class="col-sm-4">Número de usuario</dt>
                <dd class="col-sm-8">{{ $usuario->id_usuario }}</dd>

                <dt class="col-sm-4">Nombre</dt>
                <dd class="col-sm-8">{{ $usuario->nombre }}</dd>

                <dt class="col-sm-4">Correo de acceso</dt>
                <dd class="col-sm-8">{{ $usuario->correo }}</dd>

                <dt class="col-sm-4">Rol</dt>
                <dd class="col-sm-8">{{ $usuario->rol_etiqueta }}</dd>

                @if ($usuario->odontologo)
                    <dt class="col-sm-4">Número de colegiado</dt>
                    <dd class="col-sm-8">{{ $usuario->odontologo->numero_colegiado ?: 'Sin registrar' }}</dd>
                @endif

                <dt class="col-sm-4">Acceso al sistema</dt>
                <dd class="col-sm-8 mb-0">
                    {{ $usuario->activo ? 'Puede iniciar sesión.' : 'Cuenta desactivada: no puede iniciar sesión.' }}
                </dd>
            </dl>
        </section>
    </div>

    <div class="col-lg-4">
        <section class="ot-panel h-100">
            <div class="ot-panel-cabecera">
                <h2>Acciones</h2>
            </div>

            <div class="d-grid gap-2">
                <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-primary">
                    <i class="bi bi-pencil-square" aria-hidden="true"></i> Editar datos o contraseña
                </a>

                @if ($usuario->odontologo)
                    <a href="{{ route('odontologos.show', $usuario->odontologo) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-person-badge" aria-hidden="true"></i> Ver perfil profesional
                    </a>
                @endif

                @if ($usuario->is(auth()->user()))
                    <p class="ot-nota mb-0">Esta es tu cuenta. Para evitar que el sistema quede sin acceso, no puedes desactivarla.</p>
                @elseif ($usuario->activo)
                    <form method="POST" action="{{ route('usuarios.desactivar', $usuario) }}"
                          data-confirmar="¿Desactivar la cuenta de {{ $usuario->nombre }}? No podrá iniciar sesión y su sesión abierta se cerrará.">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-person-slash" aria-hidden="true"></i> Desactivar cuenta
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('usuarios.activar', $usuario) }}"
                          data-confirmar="¿Reactivar la cuenta de {{ $usuario->nombre }}?">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline-success w-100">
                            <i class="bi bi-person-check" aria-hidden="true"></i> Reactivar cuenta
                        </button>
                    </form>
                @endif
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/modules/usuarios.js') }}"></script>
@endpush
