@extends('layouts.app')

@section('titulo', 'Nuevo usuario')

@push('estilos')
<link href="{{ asset('css/modules/usuarios.css') }}" rel="stylesheet">
@endpush

@section('content')
<header class="ot-encabezado">
    <div>
        <h1>Nuevo usuario</h1>
        <p>Crea una cuenta de acceso para el personal de la clínica.</p>
    </div>

    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left" aria-hidden="true"></i> Volver al listado
    </a>
</header>

<div class="card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('usuarios.store') }}" novalidate data-validar>
            @csrf

            @include('usuarios._form', [
                'textoBoton' => 'Crear usuario',
                'cancelar' => route('usuarios.index'),
            ])
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/modules/usuarios.js') }}"></script>
@endpush
