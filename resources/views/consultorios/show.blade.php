@extends('layouts.app')

@section('titulo', 'Ficha del consultorio')

@push('estilos')
<link href="{{ asset('css/modules/usuarios.css') }}" rel="stylesheet">
@endpush

@php
    $sinCitas = (int) $consultorio->citas_count === 0;
@endphp

@section('content')
<header class="ot-encabezado">
    <div>
        <h1>{{ $consultorio->nombre }}</h1>
        <p><x-estado-activo :activo="$consultorio->activo" /></p>
    </div>

    <a href="{{ route('consultorios.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left" aria-hidden="true"></i> Volver al listado
    </a>
</header>

<section class="row g-3 mb-4" aria-label="Resumen de citas">
    <div class="col-md-6">
        <article class="ot-indicador">
            <h2>Citas pendientes</h2>
            <i class="bi bi-calendar-event" aria-hidden="true"></i>
            <p class="ot-indicador-valor">{{ $consultorio->citas_pendientes_count }}</p>
            <p class="ot-indicador-detalle">programadas desde hoy</p>
        </article>
    </div>
    <div class="col-md-6">
        <article class="ot-indicador">
            <h2>Citas en el historial</h2>
            <i class="bi bi-archive" aria-hidden="true"></i>
            <p class="ot-indicador-valor">{{ $consultorio->citas_count }}</p>
            <p class="ot-indicador-detalle">de cualquier estado</p>
        </article>
    </div>
</section>

<div class="row g-3">
    <div class="col-lg-8">
        <section class="ot-panel">
            <div class="ot-panel-cabecera">
                <h2>Próximas citas</h2>
                @if (Route::has('citas.index'))
                    <a href="{{ route('citas.index') }}" class="btn btn-outline-secondary btn-sm">Abrir agenda</a>
                @endif
            </div>

            <x-citas-proximas :citas="$proximas" columna="odontologo" />
        </section>
    </div>

    <div class="col-lg-4">
        <section class="ot-panel">
            <div class="ot-panel-cabecera">
                <h2>Acciones</h2>
            </div>

            <div class="d-grid gap-2">
                <a href="{{ route('consultorios.edit', $consultorio) }}" class="btn btn-primary">
                    <i class="bi bi-pencil-square" aria-hidden="true"></i> Editar nombre
                </a>

                @if ($consultorio->activo)
                    <form method="POST" action="{{ route('consultorios.desactivar', $consultorio) }}"
                          data-confirmar="¿Desactivar {{ $consultorio->nombre }}? Dejará de ofrecerse para nuevas citas.">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-slash-circle" aria-hidden="true"></i> Desactivar consultorio
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('consultorios.activar', $consultorio) }}"
                          data-confirmar="¿Reactivar {{ $consultorio->nombre }}?">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline-success w-100">
                            <i class="bi bi-check-circle" aria-hidden="true"></i> Reactivar consultorio
                        </button>
                    </form>
                @endif

                @if ($sinCitas)
                    <form method="POST" action="{{ route('consultorios.destroy', $consultorio) }}"
                          data-confirmar="¿Eliminar definitivamente {{ $consultorio->nombre }}? Esta acción no se puede deshacer.">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-trash3" aria-hidden="true"></i> Eliminar definitivamente
                        </button>
                    </form>
                @endif
            </div>

            <p class="ot-nota mt-3 mb-0">
                @if ($sinCitas)
                    Como no tiene citas registradas, también se puede eliminar.
                @else
                    Tiene citas en el historial, por eso solo se puede desactivar y no eliminar.
                @endif
            </p>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/modules/usuarios.js') }}"></script>
@endpush
