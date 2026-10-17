@extends('layouts.app')

@section('titulo', 'Inicio')

@php
    $usuario = auth()->user();
    $esOdontologo = $usuario->esOdontologo();
    $fecha = today()->locale('es')->translatedFormat('l j \d\e F');
@endphp

@section('content')
<header class="ot-encabezado">
    <div>
        <h1>Hola, {{ $usuario->nombre }}</h1>
        <p>{{ $esOdontologo ? 'Resumen de tu jornada' : 'Resumen general de la clínica' }} para hoy, {{ $fecha }}.</p>
    </div>

    @if ($accesos)
        <nav class="ot-accesos" aria-label="Accesos rápidos">
            @foreach ($accesos as $acceso)
                <a href="{{ route($acceso['ruta']) }}" class="btn btn-outline-secondary">
                    <i class="bi {{ $acceso['icono'] }}" aria-hidden="true"></i> {{ $acceso['texto'] }}
                </a>
            @endforeach
        </nav>
    @endif
</header>

<section class="row g-3 mb-4" aria-label="Resumen">
    <div class="col-md-4">
        <article class="ot-indicador">
            <h2>Pacientes</h2>
            <i class="bi bi-people" aria-hidden="true"></i>
            <p class="ot-indicador-valor">{{ $resumen['pacientes'] }}</p>
            <p class="ot-indicador-detalle">registrados</p>
        </article>
    </div>
    <div class="col-md-4">
        <article class="ot-indicador">
            <h2>{{ $esOdontologo ? 'Mis citas de hoy' : 'Citas de hoy' }}</h2>
            <i class="bi bi-calendar-check" aria-hidden="true"></i>
            <p class="ot-indicador-valor">{{ $resumen['citasHoy'] }}</p>
            <p class="ot-indicador-detalle">programadas</p>
        </article>
    </div>
    <div class="col-md-4">
        @if ($esOdontologo)
            <article class="ot-indicador">
                <h2>Tratamientos</h2>
                <i class="bi bi-clipboard2-pulse" aria-hidden="true"></i>
                <p class="ot-indicador-valor">{{ $resumen['tratamientos'] }}</p>
                <p class="ot-indicador-detalle">en seguimiento</p>
            </article>
        @else
            <article class="ot-indicador">
                <h2>Próximos 7 días</h2>
                <i class="bi bi-calendar-week" aria-hidden="true"></i>
                <p class="ot-indicador-valor">{{ $resumen['proximas'] }}</p>
                <p class="ot-indicador-detalle">citas programadas</p>
            </article>
        @endif
    </div>
</section>

<section class="ot-panel">
    <div class="ot-panel-cabecera">
        <h2>{{ $esOdontologo ? 'Mi agenda de hoy' : 'Citas de hoy' }}</h2>
        @if (Route::has('citas.create'))
            <a href="{{ route('citas.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg" aria-hidden="true"></i> Nueva cita
            </a>
        @endif
    </div>

    @if ($citasHoy->isEmpty())
        <div class="ot-vacio">
            <i class="bi bi-calendar2-check" aria-hidden="true"></i>
            <p>No hay citas para hoy.</p>
        </div>
    @else
        <div class="table-responsive ot-tabla">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th scope="col">Hora</th>
                        <th scope="col">Paciente</th>
                        @unless ($esOdontologo)
                            <th scope="col">Odontólogo</th>
                        @endunless
                        <th scope="col">Consultorio</th>
                        <th scope="col">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($citasHoy as $cita)
                        <tr>
                            <td class="text-nowrap">
                                <strong>{{ $cita->inicio->format('g:i A') }}</strong>
                                <span class="ot-hora-fin">a {{ $cita->fin->format('g:i A') }}</span>
                            </td>
                            <td>{{ $cita->paciente?->nombre_completo }}</td>
                            @unless ($esOdontologo)
                                <td>{{ $cita->odontologo?->usuario?->nombre }}</td>
                            @endunless
                            <td>{{ $cita->consultorio?->nombre }}</td>
                            <td>
                                <span class="ot-estado ot-estado-{{ strtolower($cita->estado) }}">{{ ucfirst(strtolower($cita->estado)) }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</section>
@endsection
