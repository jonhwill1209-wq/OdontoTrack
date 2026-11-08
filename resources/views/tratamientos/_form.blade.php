<div class="mb-3">
    <label for="diagnostico" class="form-label">Diagnóstico</label>
    <textarea id="diagnostico" name="diagnostico" rows="4" maxlength="5000" required
              class="form-control @error('diagnostico') is-invalid @enderror">{{ old('diagnostico', $tratamiento->diagnostico) }}</textarea>
    @error('diagnostico')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label for="plan" class="form-label">Plan de tratamiento</label>
    <textarea id="plan" name="plan" rows="4" maxlength="5000" required
              class="form-control @error('plan') is-invalid @enderror">{{ old('plan', $tratamiento->plan) }}</textarea>
    @error('plan')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="row g-3">
    <div class="col-md-6">
        <label for="fecha_inicio" class="form-label">Fecha de inicio</label>
        <input id="fecha_inicio" name="fecha_inicio" type="date" max="{{ today()->toDateString() }}"
               value="{{ old('fecha_inicio', $tratamiento->fecha_inicio?->format('Y-m-d') ?? today()->toDateString()) }}"
               required class="form-control @error('fecha_inicio') is-invalid @enderror">
        @error('fecha_inicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="estado" class="form-label">Estado</label>
        <select id="estado" name="estado" required class="form-select @error('estado') is-invalid @enderror">
            @foreach (['PLANIFICADO' => 'Planificado', 'EN_CURSO' => 'En curso', 'FINALIZADO' => 'Finalizado'] as $valor => $etiqueta)
                <option value="{{ $valor }}" @selected(old('estado', $tratamiento->estado ?: 'PLANIFICADO') === $valor)>{{ $etiqueta }}</option>
            @endforeach
        </select>
        @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary">{{ $textoBoton }}</button>
    <a href="{{ $tratamiento->exists ? route('tratamientos.show', $tratamiento) : route('expedientes.show', $expediente->paciente) }}"
       class="btn btn-outline-secondary">Cancelar</a>
</div>
