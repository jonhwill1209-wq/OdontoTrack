@extends('layouts.app')

@push('estilos')
<link href="{{ asset('css/modules/usuarios.css') }}" rel="stylesheet">
@endpush

@php
    //Solo se ofrece regresar a páginas del propio sistema
    $anterior = url()->previous();
    $mostrarRegresar = $anterior !== url()->current() && Str::startsWith($anterior, url('/'));
@endphp

@section('content')
<section class="ot-error">
    <span class="ot-error-icono" aria-hidden="true">
        <i class="bi @yield('icono', 'bi-exclamation-circle')"></i>
    </span>
    <p class="ot-error-codigo">@yield('codigo')</p>
    <h1>@yield('titulo')</h1>
    <p class="ot-error-texto">@yield('mensaje')</p>
    @yield('detalle')

    <div class="ot-error-acciones">
        @if ($mostrarRegresar)
            <a href="{{ $anterior }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left" aria-hidden="true"></i> Regresar
            </a>
        @endif
        <a href="{{ route('dashboard') }}" class="btn btn-primary">Volver al inicio</a>
    </div>
</section>
@endsection
