@extends('layouts.app')

@section('titulo', 'Editar consultorio')

@push('estilos')
<link href="{{ asset('css/modules/usuarios.css') }}" rel="stylesheet">
@endpush

@section('content')
<header class="ot-encabezado">
    <div>
        <h1>Editar consultorio</h1>
        <p>{{ $consultorio->nombre }}</p>
    </div>

    <a href="{{ route('consultorios.show', $consultorio) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left" aria-hidden="true"></i> Volver a la ficha
    </a>
</header>

<div class="card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('consultorios.update', $consultorio) }}" novalidate data-validar>
            @csrf
            @method('PUT')

            @include('consultorios._form', [
                'textoBoton' => 'Guardar cambios',
                'cancelar' => route('consultorios.show', $consultorio),
            ])
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/modules/usuarios.js') }}"></script>
@endpush
