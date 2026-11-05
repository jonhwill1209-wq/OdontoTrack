@extends('layouts.app')

@section('titulo', 'Ficha del odontólogo')

@push('estilos')
<link href="{{ asset('css/modules/usuarios.css') }}" rel="stylesheet">
@endpush

@php
    $usuario = $odontologo->usuario;
@endphp

@section('content')
<header class="ot-encabezado">
    <div>
        <h1>{{ $usuario->nombre }}</h1>
        <p>
            Colegiado {{ $odontologo->numero_colegiado ?: 'sin registrar' }}
            <x-estado-activo :activo="$usuario->activo" class="ms-2" />
        </p>
    </div>

    <a href="{{ route('odontologos.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left" aria-hidden="true"></i> Volver al listado
    </a>
</header>

<section class="row g-3 mb-4" aria-label="Resumen de citas">
    <div class="col-md-4">
        <article class="ot-indicador">
            <h2>Citas pendientes</h2>
            <i class="bi bi-calendar-event" aria-hidden="true"></i>
            <p class="ot-indicador-valor">{{ $resumen['pendientes'] }}</p>
            <p class="ot-indicador-detalle">programadas desde hoy</p>
        </article>
    </div>
    <div class="col-md-4">
        <article class="ot-indicador">
            <h2>Citas realizadas</h2>
            <i class="bi bi-calendar-check" aria-hidden="true"></i>
            <p class="ot-indicador-valor">{{ $resumen['realizadas'] }}</p>
            <p class="ot-indicador-detalle">en todo el historial</p>
        </article>
    </div>
    <div class="col-md-4">
        <article class="ot-indicador">
            <h2>Citas canceladas</h2>
            <i class="bi bi-calendar-x" aria-hidden="true"></i>
            <p class="ot-indicador-valor">{{ $resumen['canceladas'] }}</p>
            <p class="ot-indicador-detalle">en todo el historial</p>
        </article>
    </div>
</section>

<div class="row g-3">
    <div class="col-lg-8">
        <section class="ot-panel mb-3">
            <div class="ot-panel-cabecera">
                <h2>Datos del profesional</h2>
            </div>

            <dl class="row ot-ficha mb-0">
                <dt class="col-sm-4">Nombre</dt>
                <dd class="col-sm-8">{{ $usuario->nombre }}</dd>

                <dt class="col-sm-4">Número de colegiado</dt>
                <dd class="col-sm-8">{{ $odontologo->numero_colegiado ?: 'Sin registrar' }}</dd>

                <dt class="col-sm-4">Correo de acceso</dt>
                <dd class="col-sm-8">{{ $usuario->correo }}</dd>

                <dt class="col-sm-4">Acceso al sistema</dt>
                <dd class="col-sm-8 mb-0">
                    {{ $usuario->activo ? 'Puede iniciar sesión.' : 'Desactivado: no puede iniciar sesión.' }}
                </dd>
            </dl>
        </section>

        <section class="ot-panel">
            <div class="ot-panel-cabecera">
                <h2>Próximas citas</h2>
                @if (Route::has('citas.index'))
                    <a href="{{ route('citas.index') }}" class="btn btn-outline-secondary btn-sm">Abrir agenda</a>
                @endif
            </div>

            <x-citas-proximas :citas="$proximas" columna="consultorio" />
        </section>
    </div>

    <div class="col-lg-4">
        <section class="ot-panel">
            <div class="ot-panel-cabecera">
                <h2>Acciones</h2>
            </div>

            <div class="d-grid gap-2">
                <a href="{{ route('odontologos.edit', $odontologo) }}" class="btn btn-primary">
                    <i class="bi bi-pencil-square" aria-hidden="true"></i> Editar datos
                </a>

                <a href="{{ route('usuarios.show', $usuario) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-person-gear" aria-hidden="true"></i> Ver cuenta de usuario
                </a>

                @if ($usuario->activo)
                    <form method="POST" action="{{ route('odontologos.desactivar', $odontologo) }}"
                          data-confirmar="¿Desactivar a {{ $usuario->nombre }}? Ya no podrá iniciar sesión y su sesión abierta se cerrará.">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-person-slash" aria-hidden="true"></i> Desactivar odontólogo
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('odontologos.activar', $odontologo) }}"
                          data-confirmar="¿Reactivar a {{ $usuario->nombre }}?">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline-success w-100">
                            <i class="bi bi-person-check" aria-hidden="true"></i> Reactivar odontólogo
                        </button>
                    </form>
                @endif
            </div>

            <p class="ot-nota mt-3 mb-0">
                Desactivar no borra su historial: sus citas, notas de evolución y registros del odontograma se conservan.
            </p>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/modules/usuarios.js') }}"></script>
@endpush
