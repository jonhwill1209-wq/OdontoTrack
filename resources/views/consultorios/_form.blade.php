<p class="text-secondary">Los campos con * son obligatorios.</p>

<div class="row g-3">
    <div class="col-md-8 col-lg-6">
        <label for="nombre" class="form-label">Nombre o número del consultorio *</label>
        <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $consultorio->nombre) }}"
               class="form-control @error('nombre') is-invalid @enderror"
               required minlength="2" maxlength="80" placeholder="Consultorio 4"
               pattern="[A-Za-zÀ-ÖØ-öø-ÿ0-9][A-Za-zÀ-ÖØ-öø-ÿ0-9 .#\-]*"
               data-msg-requerido="Ingresa el nombre o número del consultorio."
               data-msg-formato="Solo letras, números, espacios, puntos, guiones y #.">
        <div class="form-text">No puede repetirse. Así aparecerá en la agenda de citas.</div>
        <div id="nombre-error" class="invalid-feedback">@error('nombre'){{ $message }}@enderror</div>
    </div>
</div>

<div class="d-flex flex-wrap gap-2 mt-4">
    <button type="submit" class="btn btn-primary" data-texto-envio="Guardando...">{{ $textoBoton }}</button>
    <a href="{{ $cancelar }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
