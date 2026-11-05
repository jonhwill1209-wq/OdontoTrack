@php
    $obligatoria = $obligatoria ?? true;
    $etiqueta = $etiqueta ?? 'Contraseña';
@endphp

<div class="col-md-6">
    <label for="clave" class="form-label">{{ $etiqueta }}{{ $obligatoria ? ' *' : '' }}</label>
    <div class="input-group has-validation">
        <input type="password" id="clave" name="clave" maxlength="64" autocomplete="new-password"
               class="form-control @error('clave') is-invalid @enderror"
               aria-describedby="clave-requisitos" data-clave-segura
               data-msg-requerido="Ingresa una contraseña."
               @required($obligatoria)>
        <button type="button" class="btn ot-btn-ojo" data-mostrar-clave="clave" aria-label="Mostrar contraseña" aria-pressed="false">
            <i class="bi bi-eye" aria-hidden="true"></i>
        </button>
        <div id="clave-error" class="invalid-feedback">@error('clave'){{ $message }}@enderror</div>
    </div>

    @unless ($obligatoria)
        <div class="form-text">Déjala en blanco para conservar la contraseña actual.</div>
    @endunless

    <ul class="ot-requisitos" id="clave-requisitos" data-requisitos-de="clave">
        <li data-regla="longitud"><i class="bi bi-circle" aria-hidden="true"></i> De 8 a 64 caracteres, sin espacios</li>
        <li data-regla="mayuscula"><i class="bi bi-circle" aria-hidden="true"></i> Una letra mayúscula</li>
        <li data-regla="minuscula"><i class="bi bi-circle" aria-hidden="true"></i> Una letra minúscula</li>
        <li data-regla="numero"><i class="bi bi-circle" aria-hidden="true"></i> Un número</li>
        <li data-regla="simbolo"><i class="bi bi-circle" aria-hidden="true"></i> Un símbolo, por ejemplo ! # $ %</li>
    </ul>
</div>

<div class="col-md-6">
    <label for="clave_confirmation" class="form-label">Confirmar {{ Str::lower($etiqueta) }}{{ $obligatoria ? ' *' : '' }}</label>
    <div class="input-group has-validation">
        <input type="password" id="clave_confirmation" name="clave_confirmation" maxlength="64" autocomplete="new-password"
               class="form-control" data-igual-a="clave"
               data-msg-requerido="Confirma la contraseña."
               @required($obligatoria)>
        <button type="button" class="btn ot-btn-ojo" data-mostrar-clave="clave_confirmation" aria-label="Mostrar contraseña" aria-pressed="false">
            <i class="bi bi-eye" aria-hidden="true"></i>
        </button>
        <div id="clave_confirmation-error" class="invalid-feedback"></div>
    </div>
</div>
