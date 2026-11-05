@extends('layouts.app')

@section('titulo', 'Iniciar sesión')

@section('content')
<div class="ot-login">
    <section class="ot-login-marca">
        @include('partials.marca')
        <p class="ot-login-lema">Sistema Integral de Citas y Expedientes Odontológicos</p>
        <p class="ot-login-texto">Gestiona pacientes, citas y expedientes clínicos desde un solo lugar.</p>
        <img src="{{ asset('img/ilustracion-login.svg') }}" alt="" class="ot-login-ilustracion" width="320" height="320">
    </section>

    <section class="ot-login-panel">
        <div class="ot-login-caja">
            <h1>Inicio de sesión</h1>
            <p class="ot-login-texto">Ingresa tus credenciales para acceder al sistema.</p>

            @if (session()->has('url.intended') && ! $errors->any() && ! session('error'))
                <div class="alert alert-info ot-alerta" role="status">
                    <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
                    <div>Inicia sesión para continuar con la página que intentabas abrir.</div>
                </div>
            @endif

            @include('partials.alertas')

            <form method="POST" action="{{ route('login.attempt') }}" novalidate data-validar>
                @csrf

                <div class="mb-3">
                    <label for="correo" class="form-label">Correo electrónico</label>
                    <input type="email" id="correo" name="correo" value="{{ old('correo') }}"
                           @class(['form-control', 'is-invalid' => $errors->has('correo')])
                           placeholder="usuario@correo.com" autocomplete="username" required autofocus maxlength="150"
                           pattern="[^\s@]+@[^\s@]+\.[^\s@]{2,}"
                           data-msg-requerido="Ingresa tu correo."
                           data-msg-formato="El correo no tiene un formato válido.">
                    <div id="correo-error" class="invalid-feedback"></div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Contraseña</label>
                    <div class="input-group has-validation">
                        <input type="password" id="password" name="password"
                               @class(['form-control', 'is-invalid' => $errors->has('password')])
                               autocomplete="current-password" required maxlength="255"
                               data-msg-requerido="Ingresa tu contraseña."
                               data-aviso-mayus="avisoMayus">
                        <button type="button" class="btn ot-btn-ojo" data-mostrar-clave="password" aria-label="Mostrar contraseña" aria-pressed="false">
                            <i class="bi bi-eye" aria-hidden="true"></i>
                        </button>
                        <div id="password-error" class="invalid-feedback"></div>
                    </div>
                    <div id="avisoMayus" class="form-text text-warning-emphasis d-none" role="status">
                        <i class="bi bi-capslock-fill" aria-hidden="true"></i> Bloq Mayús está activado.
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 ot-btn-entrar" data-texto-envio="Ingresando...">Iniciar sesión</button>
            </form>

            <p class="ot-login-ayuda">¿Olvidaste tu contraseña? Pide a administración que la restablezca.</p>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/modules/usuarios.js') }}"></script>
@endpush
