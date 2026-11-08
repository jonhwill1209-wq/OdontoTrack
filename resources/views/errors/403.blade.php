@extends('errors.plantilla')

@php
    $mensaje = ($exception ?? null)?->getMessage();

    //Los mensajes genéricos del framework llegan en inglés
    if (blank($mensaje) || in_array($mensaje, ['This action is unauthorized.', 'Forbidden'], true)) {
        $mensaje = 'No tienes permiso para acceder a esta sección.';
    }
@endphp

@section('titulo', 'Acceso denegado')
@section('codigo', '403')
@section('icono', 'bi-shield-lock')
@section('mensaje', $mensaje)

@section('detalle')
    @auth
        <p class="ot-error-detalle">
            Iniciaste sesión como <strong>{{ auth()->user()->rol_etiqueta }}</strong>.
            Si necesitas este acceso, solicítalo a administración.
        </p>
    @endauth
@endsection
