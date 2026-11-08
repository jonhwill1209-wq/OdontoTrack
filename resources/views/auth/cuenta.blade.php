@extends('layouts.app')

@section('titulo', 'Mi cuenta')

@push('estilos')
<link href="{{ asset('css/modules/usuarios.css') }}" rel="stylesheet">
@endpush

@section('content')
<header class="ot-encabezado">
    <div>
        <h1>Mi cuenta</h1>
        <p>Consulta tus datos de acceso y cambia tu contraseña.</p>
    </div>
</header>

<div class="row g-3">
    <div class="col-lg-5">
        <section class="ot-panel h-100">
            <div class="ot-panel-cabecera">
                <h2>Mis datos</h2>
            </div>

            <dl class="ot-ficha mb-0">
                <dt>Nombre</dt>
                <dd>{{ $usuario->nombre }}</dd>

                <dt>Correo de acceso</dt>
                <dd>{{ $usuario->correo }}</dd>

                <dt>Rol</dt>
                <dd><span class="ot-rol ot-rol-{{ strtolower($usuario->rol) }}">{{ $usuario->rol_etiqueta }}</span></dd>

                @if ($usuario->odontologo)
                    <dt>Número de colegiado</dt>
                    <dd>{{ $usuario->odontologo->numero_colegiado ?: 'Sin registrar' }}</dd>
                @endif
            </dl>

            <p class="ot-nota mt-2 mb-0">Si necesitas corregir tu nombre o tu correo, pídelo a administración.</p>
        </section>
    </div>

    <div class="col-lg-7">
        <section class="ot-panel">
            <div class="ot-panel-cabecera">
                <h2>Cambiar contraseña</h2>
            </div>

            <form method="POST" action="{{ route('cuenta.clave') }}" novalidate data-validar>
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="clave_actual" class="form-label">Contraseña actual *</label>
                        <div class="input-group has-validation">
                            <input type="password" id="clave_actual" name="clave_actual" autocomplete="current-password"
                                   class="form-control @error('clave_actual') is-invalid @enderror"
                                   required maxlength="255" data-msg-requerido="Ingresa tu contraseña actual.">
                            <button type="button" class="btn ot-btn-ojo" data-mostrar-clave="clave_actual" aria-label="Mostrar contraseña" aria-pressed="false">
                                <i class="bi bi-eye" aria-hidden="true"></i>
                            </button>
                            <div id="clave_actual-error" class="invalid-feedback">@error('clave_actual'){{ $message }}@enderror</div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-0">
                    @include('usuarios._campos-clave', ['obligatoria' => true, 'etiqueta' => 'Nueva contraseña'])
                </div>

                <p class="ot-nota mt-3">Al cambiarla, cualquier otra sesión abierta con tu cuenta se cerrará automáticamente.</p>

                <button type="submit" class="btn btn-primary" data-texto-envio="Actualizando...">Actualizar contraseña</button>
            </form>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/modules/usuarios.js') }}"></script>
@endpush
