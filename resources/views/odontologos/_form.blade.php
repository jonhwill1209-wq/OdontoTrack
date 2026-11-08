@php
    $esNuevo = ! $odontologo->exists;
    $usuario = $odontologo->usuario ?? new \App\Models\Usuario();
@endphp

<p class="text-secondary">Los campos con * son obligatorios.</p>

<div class="row g-3">
    <div class="col-md-6">
        <label for="nombre" class="form-label">Nombre completo *</label>
        <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $usuario->nombre) }}"
               class="form-control @error('nombre') is-invalid @enderror"
               required minlength="3" maxlength="100" autocomplete="name" placeholder="Dra. Ana Martínez"
               pattern="\p{L}[\p{L}\p{M}.'\- ]*"
               data-msg-requerido="Ingresa el nombre completo."
               data-msg-formato="El nombre solo puede contener letras, espacios, puntos, apóstrofos y guiones.">
        <div id="nombre-error" class="invalid-feedback">@error('nombre'){{ $message }}@enderror</div>
    </div>

    <div class="col-md-6">
        <label for="numero_colegiado" class="form-label">Número de colegiado</label>
        <input type="text" id="numero_colegiado" name="numero_colegiado"
               value="{{ old('numero_colegiado', $odontologo->numero_colegiado) }}"
               class="form-control text-uppercase @error('numero_colegiado') is-invalid @enderror"
               maxlength="40" placeholder="JVPO-1001"
               pattern="[A-Za-z0-9][A-Za-z0-9\-]{2,39}"
               data-msg-formato="Solo letras, números y guiones, por ejemplo JVPO-1001.">
        <div class="form-text">Opcional, pero no puede repetirse entre odontólogos.</div>
        <div id="numero_colegiado-error" class="invalid-feedback">@error('numero_colegiado'){{ $message }}@enderror</div>
    </div>

    <div class="col-md-6">
        <label for="correo" class="form-label">Correo electrónico *</label>
        <input type="email" id="correo" name="correo" value="{{ old('correo', $usuario->correo) }}"
               class="form-control @error('correo') is-invalid @enderror"
               required maxlength="150" autocomplete="email" placeholder="nombre@clinica.com"
               pattern="[^\s@]+@[^\s@]+\.[^\s@]{2,}"
               data-msg-requerido="Ingresa el correo electrónico."
               data-msg-formato="Ingresa un correo válido, por ejemplo nombre@clinica.com.">
        <div class="form-text">Con este correo el odontólogo inicia sesión.</div>
        <div id="correo-error" class="invalid-feedback">@error('correo'){{ $message }}@enderror</div>
    </div>
</div>

<h2 class="h3 mt-4 mb-1">{{ $esNuevo ? 'Contraseña de acceso' : 'Restablecer contraseña' }}</h2>
<p class="text-secondary small mb-3">
    {{ $esNuevo ? 'Se crea la cuenta con rol Odontólogo. Comparte la contraseña por un medio seguro.' : 'Úsalo cuando el odontólogo olvide su contraseña.' }}
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
