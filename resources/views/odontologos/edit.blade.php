@extends('layouts.app')

@section('titulo', 'Editar odontólogo')

@push('estilos')
<link href="{{ asset('css/modules/usuarios.css') }}" rel="stylesheet">
@endpush

@section('content')
<header class="ot-encabezado">
    <div>
        <h1>Editar odontólogo</h1>
        <p>{{ $odontologo->usuario->nombre }}</p>
    </div>

    <a href="{{ route('odontologos.show', $odontologo) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left" aria-hidden="true"></i> Volver a la ficha
    </a>
</header>

<div class="card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('odontologos.update', $odontologo) }}" novalidate data-validar>
            @csrf
            @method('PUT')

            @include('odontologos._form', [
                'textoBoton' => 'Guardar cambios',
                'cancelar' => route('odontologos.show', $odontologo),
            ])
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/modules/usuarios.js') }}"></script>
@endpush
