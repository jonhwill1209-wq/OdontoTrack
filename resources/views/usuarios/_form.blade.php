@php
    $esNuevo = ! $usuario->exists;
    $rolElegido = old('rol', $usuario->rol);
    $rolOdontologo = \App\Enums\Rol::Odontologo->value;
@endphp

<p class="text-secondary">Los campos con * son obligatorios.</p>

<div class="row g-3">
    <div class="col-md-6">
        <label for="nombre" class="form-label">Nombre completo *</label>
        <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $usuario->nombre) }}"
               class="form-control @error('nombre') is-invalid @enderror"
               required minlength="3" maxlength="100" autocomplete="name"
               pattern="[A-Za-zÀ-ÖØ-öø-ÿ][A-Za-zÀ-ÖØ-öø-ÿ.'\- ]*"
               data-msg-requerido="Ingresa el nombre completo."
               data-msg-formato="El nombre solo puede contener letras, espacios, puntos, apóstrofos y guiones.">
        <div id="nombre-error" class="invalid-feedback">@error('nombre'){{ $message }}@enderror</div>
    </div>

    <div class="col-md-6">
        <label for="correo" class="form-label">Correo electrónico *</label>
        <input type="email" id="correo" name="correo" value="{{ old('correo', $usuario->correo) }}"
               class="form-control @error('correo') is-invalid @enderror"
               required maxlength="150" autocomplete="email" placeholder="nombre@clinica.com"
               pattern="[^\s@]+@[^\s@]+\.[^\s@]{2,}"
               data-msg-requerido="Ingresa el correo electrónico."
               data-msg-formato="Ingresa un correo válido, por ejemplo nombre@clinica.com.">
        <div class="form-text">Con este correo la persona inicia sesión.</div>
        <div id="correo-error" class="invalid-feedback">@error('correo'){{ $message }}@enderror</div>
    </div>

    @if ($esNuevo)
        <div class="col-md-6">
            <label for="rol" class="form-label">Rol *</label>
            <select id="rol" name="rol" class="form-select @error('rol') is-invalid @enderror"
                    required data-msg-requerido="Selecciona el rol de la cuenta.">
                <option value="">Selecciona...</option>
                @foreach ($roles as $opcion)
                    <option value="{{ $opcion->value }}" @selected($rolElegido === $opcion->value)>{{ $opcion->etiqueta() }}</option>
                @endforeach
            </select>
            <div class="form-text">Define los módulos a los que tendrá acceso. No se puede cambiar después.</div>
            <div id="rol-error" class="invalid-feedback">@error('rol'){{ $message }}@enderror</div>
        </div>

        <div class="col-md-6" data-muestra-si="rol={{ $rolOdontologo }}" @if ($rolElegido !== $rolOdontologo) hidden @endif>
            <label for="numero_colegiado" class="form-label">Número de colegiado</label>
            <input type="text" id="numero_colegiado" name="numero_colegiado" value="{{ old('numero_colegiado') }}"
                   class="form-control text-uppercase @error('numero_colegiado') is-invalid @enderror"
                   maxlength="40" placeholder="JVPO-1001"
                   pattern="[A-Za-z0-9][A-Za-z0-9\-]{2,39}"
                   data-msg-formato="Solo letras, números y guiones, por ejemplo JVPO-1001.">
            <div class="form-text">Opcional. También se puede completar después en Odontólogos.</div>
            <div id="numero_colegiado-error" class="invalid-feedback">@error('numero_colegiado'){{ $message }}@enderror</div>
        </div>
    @else
        <div class="col-md-6">
            <span class="form-label d-block">Rol</span>
            <span class="ot-rol ot-rol-{{ strtolower($usuario->rol) }}">{{ $usuario->rol_etiqueta }}</span>
            <div class="form-text">El rol no se puede cambiar después de crear la cuenta.</div>
        </div>
    @endif
</div>

<h2 class="h3 mt-4 mb-1">{{ $esNuevo ? 'Contraseña de acceso' : 'Restablecer contraseña' }}</h2>
<p class="text-secondary small mb-3">
    {{ $esNuevo ? 'Comparte la contraseña con la persona por un medio seguro.' : 'Úsalo cuando la persona olvide su contraseña.' }}
</p>

<div class="row g-3">
    @include('usuarios._campos-clave', [
        'obligatoria' => $esNuevo,
        'etiqueta' => $esNuevo ? 'Contraseña' : 'Nueva contraseña',
    ])
</div>

<div class="d-flex flex-wrap gap-2 mt-4">
    <button type="submit" class="btn btn-primary" data-texto-envio="Guardando...">{{ $textoBoton }}</button>
    <a href="{{ $cancelar }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
