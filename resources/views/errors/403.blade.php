@extends('errors.plantilla')

@section('titulo', 'Acceso denegado')
@section('codigo', '403')
@section('mensaje', ($exception ?? null)?->getMessage() ?: 'No tienes permiso para acceder a esta sección.')

@section('detalle')
    @auth
        <p class="ot-error-detalle">
            Iniciaste sesión como <strong>{{ auth()->user()->esOdontologo() ? 'Odontólogo' : 'Recepcionista' }}</strong>
            y esta sección es para otro rol.
        </p>
    @endauth
@endsection
