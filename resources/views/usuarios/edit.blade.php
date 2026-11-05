@extends('layouts.app')

@section('titulo', 'Editar usuario')

@push('estilos')
<link href="{{ asset('css/modules/usuarios.css') }}" rel="stylesheet">
@endpush

@section('content')
<header class="ot-encabezado">
    <div>
        <h1>Editar usuario</h1>
        <p>{{ $usuario->nombre }} · {{ $usuario->correo }}</p>
    </div>

    <a href="{{ route('usuarios.show', $usuario) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left" aria-hidden="true"></i> Volver a la ficha
    </a>
</header>

<div class="card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('usuarios.update', $usuario) }}" novalidate data-validar>
            @csrf
            @method('PUT')

            @include('usuarios._form', [
                'textoBoton' => 'Guardar cambios',
                'cancelar' => route('usuarios.show', $usuario),
            ])
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/modules/usuarios.js') }}"></script>
@endpush
