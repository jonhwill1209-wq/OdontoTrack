@extends('layouts.app')

@section('titulo', 'Nuevo consultorio')

@push('estilos')
<link href="{{ asset('css/modules/usuarios.css') }}" rel="stylesheet">
@endpush

@section('content')
<header class="ot-encabezado">
    <div>
        <h1>Nuevo consultorio</h1>
        <p>El consultorio quedará disponible de inmediato en la agenda.</p>
    </div>

    <a href="{{ route('consultorios.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left" aria-hidden="true"></i> Volver al listado
    </a>
</header>

<div class="card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('consultorios.store') }}" novalidate data-validar>
            @csrf

            @include('consultorios._form', [
                'textoBoton' => 'Registrar consultorio',
                'cancelar' => route('consultorios.index'),
            ])
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/modules/usuarios.js') }}"></script>
@endpush
