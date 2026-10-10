@php
$paciente = $paciente ?? new \App\Models\Paciente();

$campos = [
'nombres' => ['Nombres', 'text', 100],
'apellidos' => ['Apellidos', 'text', 100],
'telefono' => ['Teléfono', 'tel', 25],
'correo' => ['Correo electrónico', 'email', 150],
];
@endphp

<p class="text-secondary">Los campos con * son obligatorios.</p>

<div class="row g-3">
    @foreach ($campos as $campo => [$etiqueta, $tipo, $limite])
    <div class="col-md-6">
        <label for="{{ $campo }}" class="form-label">
            {{ $etiqueta }}
            @if (in_array($campo, ['nombres', 'apellidos']))
            *
            @endif
        </label>

        <input type="{{ $tipo }}"
               id="{{ $campo }}"
               name="{{ $campo }}"
               value="{{ old($campo, $paciente->$campo) }}"
               maxlength="{{ $limite }}"
               class="form-control @error($campo) is-invalid @enderror"
               @required(in_array($campo, ['nombres', 'apellidos']))>

        @error($campo)
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    @endforeach

    <div class="col-md-6">
        <label for="fecha_nacimiento" class="form-label">
            Fecha de nacimiento
        </label>

        <input type="date"
               id="fecha_nacimiento"
               name="fecha_nacimiento"
               value="{{ old('fecha_nacimiento', $paciente->fecha_nacimiento?->format('Y-m-d')) }}"
               max="{{ now()->subDay()->format('Y-m-d') }}"
               class="form-control @error('fecha_nacimiento') is-invalid @enderror">

        @error('fecha_nacimiento')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary">
        {{ $textoBoton ?? 'Guardar' }}
    </button>

    <a href="{{ route('pacientes.index') }}"
       class="btn btn-outline-secondary">
        Cancelar
    </a>
</div>
