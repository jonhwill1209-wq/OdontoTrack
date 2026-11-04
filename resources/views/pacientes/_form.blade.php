@php
$paciente = $paciente ?? new \App\Models\Paciente();

$campos = [
'nombres' => ['Nombres', 'text', 100],
'apellidos' => ['Apellidos', 'text', 100],
'telefono' => ['Teléfono', 'tel', 9],
'correo' => ['Correo electrónico', 'email', 150],
];
@endphp

<p class="text-secondary">Los campos con * son obligatorios.</p>

<div class="row g-3">
    @foreach ($campos as $campo => [$etiqueta, $tipo, $limite])
    @php
    $esNombre = in_array($campo, ['nombres', 'apellidos'], true);
    @endphp

    <div class="col-md-6">
        <label for="{{ $campo }}" class="form-label">
            {{ $etiqueta }}
            @if ($esNombre)
            <span aria-hidden="true">*</span>
            @endif
        </label>

        <input
            type="{{ $tipo }}"
            id="{{ $campo }}"
            name="{{ $campo }}"
            value="{{ old($campo, $paciente->$campo) }}"
            maxlength="{{ $limite }}"
            class="form-control @error($campo) is-invalid @enderror"
            aria-invalid="{{ $errors->has($campo) ? 'true' : 'false' }}"
            @required($esNombre)

            @if ($esNombre)
            pattern="\p{L}[\p{L}\p{M}]*(?: +\p{L}[\p{L}\p{M}]*)*"
            title="Usa solamente letras y espacios."
            @endif

            @if ($campo === 'nombres')
        autocomplete="given-name"
        @elseif ($campo === 'apellidos')
        autocomplete="family-name"
        @elseif ($campo === 'telefono')
        autocomplete="tel"
        inputmode="tel"
        pattern="[0-9]{4}-?[0-9]{4}"
        placeholder="7123-4567"
        title="Ingresa 8 dígitos, con guion opcional: 7123-4567 o 71234567."
        aria-describedby="telefono-ayuda"
        @elseif ($campo === 'correo')
        autocomplete="email"
        placeholder="nombre@ejemplo.com"
        @endif
        >

        @if ($campo === 'telefono')
        <div id="telefono-ayuda" class="form-text">
            Ocho dígitos, con o sin guion.
        </div>
        @endif

        @error($campo)
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    @endforeach

    <div class="col-md-6">
        <label for="fecha_nacimiento" class="form-label">
            Fecha de nacimiento
        </label>

        <input
            type="date"
            id="fecha_nacimiento"
            name="fecha_nacimiento"
            value="{{ old('fecha_nacimiento', $paciente->fecha_nacimiento?->format('Y-m-d')) }}"
            max="{{ today()->subDay()->format('Y-m-d') }}"
            autocomplete="bday"
            class="form-control @error('fecha_nacimiento') is-invalid @enderror"
            aria-invalid="{{ $errors->has('fecha_nacimiento') ? 'true' : 'false' }}"
        >

        @error('fecha_nacimiento')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="d-flex flex-wrap gap-2 mt-4">
    <button type="submit" class="btn btn-primary">
        {{ $textoBoton ?? 'Guardar' }}
    </button>

    <a href="{{ route('pacientes.index') }}"
       class="btn btn-outline-secondary">
        Cancelar
    </a>
</div>
