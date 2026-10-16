@php
$inicioValor = old('inicio', $cita->inicio?->format('Y-m-d\TH:i') ?? request('inicio'));
$finValor = old('fin', $cita->fin?->format('Y-m-d\TH:i'));
$odontologoValor = old('id_odontologo', $cita->id_odontologo ?? $odontologoPorDefecto);
@endphp

<p class="text-secondary">Todos los campos son obligatorios, excepto el motivo.</p>

<div class="row g-3">
    <div class="col-md-6">
        <label for="id_paciente" class="form-label">Paciente *</label>
        <select id="id_paciente" name="id_paciente" required
                class="form-select @error('id_paciente') is-invalid @enderror">
            <option value="">Selecciona...</option>
            @foreach ($pacientes as $paciente)
            <option value="{{ $paciente->id_paciente }}" @selected(old('id_paciente', $cita->id_paciente) == $paciente->id_paciente)>
                {{ $paciente->apellidos }}, {{ $paciente->nombres }}
            </option>
            @endforeach
        </select>
        @error('id_paciente')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="id_odontologo" class="form-label">Odontólogo *</label>
        <select id="id_odontologo" name="id_odontologo" required
                class="form-select @error('id_odontologo') is-invalid @enderror">
            <option value="">Selecciona...</option>
            @foreach ($odontologos as $odontologo)
            <option value="{{ $odontologo->id_odontologo }}" @selected($odontologoValor == $odontologo->id_odontologo)>
                {{ $odontologo->usuario->nombre }}
            </option>
            @endforeach
        </select>
        @error('id_odontologo')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="id_consultorio" class="form-label">Consultorio *</label>
        <select id="id_consultorio" name="id_consultorio" required
                class="form-select @error('id_consultorio') is-invalid @enderror">
            <option value="">Selecciona...</option>
            @foreach ($consultorios as $consultorio)
            <option value="{{ $consultorio->id_consultorio }}" @selected(old('id_consultorio', $cita->id_consultorio) == $consultorio->id_consultorio)>
                {{ $consultorio->nombre }}
            </option>
            @endforeach
        </select>
        @error('id_consultorio')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6"></div>

    <div class="col-md-6">
        <label for="inicio" class="form-label">Inicio *</label>
        <input type="datetime-local" id="inicio" name="inicio" value="{{ $inicioValor }}" required
               class="form-control @error('inicio') is-invalid @enderror">
        @error('inicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="fin" class="form-label">Fin *</label>
        <input type="datetime-local" id="fin" name="fin" value="{{ $finValor }}" required
               class="form-control @error('fin') is-invalid @enderror">
        @error('fin')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="motivo" class="form-label">Motivo</label>
        <input type="text" id="motivo" name="motivo" maxlength="250" value="{{ old('motivo', $cita->motivo) }}"
               class="form-control @error('motivo') is-invalid @enderror">
        @error('motivo')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary">{{ $textoBoton ?? 'Guardar' }}</button>
    <a href="{{ route('citas.index') }}" class="btn btn-outline-secondary">Volver a la agenda</a>
</div>
