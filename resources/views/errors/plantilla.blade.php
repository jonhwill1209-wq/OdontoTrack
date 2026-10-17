@extends('layouts.app')

@section('content')
<section class="ot-error">
    <p class="ot-error-codigo">@yield('codigo')</p>
    <h1>@yield('titulo')</h1>
    <p class="ot-error-texto">@yield('mensaje')</p>
    @yield('detalle')
    <a href="{{ route('dashboard') }}" class="btn btn-primary">Volver al inicio</a>
</section>
@endsection
