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

            @include('partials.alertas')

            <form method="POST" action="{{ route('login.attempt') }}" novalidate>
                @csrf

                <div class="mb-3">
                    <label for="correo" class="form-label">Correo electrónico</label>
                    <input type="email" id="correo" name="correo" value="{{ old('correo') }}"
                           @class(['form-control', 'is-invalid' => $errors->has('correo')])
                           placeholder="usuario@correo.com" autocomplete="username" required autofocus>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Contraseña</label>
                    <div class="input-group">
                        <input type="password" id="password" name="password"
                               @class(['form-control', 'is-invalid' => $errors->has('password')])
                               autocomplete="current-password" required>
                        <button type="button" class="btn ot-btn-ojo" id="verClave" aria-label="Mostrar contraseña" aria-pressed="false">
                            <i class="bi bi-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 ot-btn-entrar">Iniciar sesión</button>
            </form>

            <p class="ot-login-ayuda">¿Olvidaste tu contraseña? Pide a administración que la restablezca.</p>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    //Muestra u oculta la contraseña escrita
    document.getElementById('verClave').addEventListener('click', function () {
        const campo = document.getElementById('password');
        const mostrar = campo.type === 'password';

        campo.type = mostrar ? 'text' : 'password';
        this.setAttribute('aria-pressed', mostrar);
        this.setAttribute('aria-label', mostrar ? 'Ocultar contraseña' : 'Mostrar contraseña');
        this.querySelector('i').className = mostrar ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
</script>
@endpush
