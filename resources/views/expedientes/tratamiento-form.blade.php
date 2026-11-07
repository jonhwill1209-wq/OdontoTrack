@foreach (['diagnostico'=>'Diagnóstico','plan'=>'Plan de tratamiento'] as $campo=>$label)
<label class="form-label" for="{{ $campo }}-{{ $t?->id_tratamiento ?? 'nuevo' }}">{{ $label }}</label>
<textarea class="form-control mb-2" id="{{ $campo }}-{{ $t?->id_tratamiento ?? 'nuevo' }}" name="{{ $campo }}" maxlength="5000" required>{{ old($campo,$t?->$campo) }}</textarea>
@endforeach
<label class="form-label">Estado <select class="form-select" name="estado" required>@foreach (['PLANIFICADO','EN_CURSO','FINALIZADO'] as $estado)<option @selected(old('estado',$t?->estado)===$estado)>{{ $estado }}</option>@endforeach</select></label>
<label class="form-label">Fecha de inicio <input class="form-control" type="date" name="fecha_inicio" value="{{ old('fecha_inicio',$t?->fecha_inicio?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required></label>
<button class="btn btn-primary" type="submit">Guardar tratamiento</button>
