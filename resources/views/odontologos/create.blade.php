@extends('layouts.app')

@section('titulo', 'Nuevo odontólogo')

@push('estilos')
<link href="{{ asset('css/modules/usuarios.css') }}" rel="stylesheet">
@endpush

@section('content')
<header class="ot-encabezado">
    <div>
        <h1>Nuevo odontólogo</h1>
        <p>Registra al profesional y su cuenta de acceso en un solo paso.</p>
    </div>

    <a href="{{ route('odontologos.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left" aria-hidden="true"></i> Volver al listado
    </a>
</header>

<div class="card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('odontologos.store') }}" novalidate data-validar>
            @csrf

            @include('odontologos._form', [
                'textoBoton' => 'Registrar odontólogo',
                'cancelar' => route('odontologos.index'),
            ])
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/modules/usuarios.js') }}"></script>
@endpush
