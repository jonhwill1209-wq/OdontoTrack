@extends('layouts.app')

@section('titulo', 'Odontograma')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h1 class="h3 mb-1">Odontograma</h1>
        <p class="text-muted mb-0">
            {{ $paciente->nombre_completo }}
            · Expediente #{{ $expediente->id_expediente }}
        </p>
    </div>

    <a href="{{ route('pacientes.show', $paciente) }}"
       class="btn btn-outline-secondary">
        Volver al paciente
    </a>
</div>

@php
$actuales = $registros
->unique(fn ($registro) => $registro->pieza . '|' . $registro->cara)
->groupBy('pieza');
@endphp

<div class="card mb-4">
    <div class="card-body">
        <h2 class="h5">Piezas dentales</h2>
        <p class="text-muted">
            Selecciona una pieza para completar el formulario.
            Se muestra la última condición registrada por cada cara.
        </p>

        @foreach (['Superior' => $superiores, 'Inferior' => $inferiores] as $arcada => $piezas)
        <h3 class="h6 mt-3">{{ $arcada }}</h3>

        <div class="d-flex flex-nowrap gap-2 overflow-auto pb-3">
            @foreach ($piezas as $pieza)
            @php
            $estados = $actuales->get($pieza, collect());
            @endphp

            <div class="border rounded p-2 flex-shrink-0 text-center">
                <button type="button"
                        class="btn btn-outline-primary pieza-boton mb-2"
                        data-pieza="{{ $pieza }}"
                        aria-label="Seleccionar pieza {{ $pieza }}">
                    {{ $pieza }}
                </button>

                @forelse ($estados as $estado)
                <div class="small mb-1">
                                    <span class="text-muted">
                                        {{ $caras[$estado->cara] ?? $estado->cara }}
                                    </span>
                    <br>
                    <span class="badge text-bg-secondary">
                                        {{ $condiciones[$estado->condicion] ?? $estado->condicion }}
                                    </span>
                </div>
                @empty
                <div class="small text-muted">
                    Sin registro
                </div>
                @endforelse
            </div>
            @endforeach
        </div>
        @endforeach
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h2 class="h5 mb-3">Registrar condición</h2>

        <form action="{{ route('odontograma.store', $paciente) }}"
              method="POST">
            @csrf

            <div class="row g-3">
                <div class="col-md-4">
                    <label for="pieza" class="form-label">
                        Pieza dental
                    </label>

                    <select id="pieza"
                            name="pieza"
                            class="form-select @error('pieza') is-invalid @enderror"
                            required>
                        <option value="">Selecciona una pieza</option>

                        @foreach (array_merge($superiores, $inferiores) as $pieza)
                        <option value="{{ $pieza }}"
                                @selected(old('pieza') === $pieza)>
                        {{ $pieza }}
                        </option>
                        @endforeach
                    </select>

                    @error('pieza')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="cara" class="form-label">
                        Cara dental
                    </label>

                    <select id="cara"
                            name="cara"
                            class="form-select @error('cara') is-invalid @enderror"
                            required>
                        <option value="">Selecciona una cara</option>

                        @foreach ($caras as $valor => $etiqueta)
                        <option value="{{ $valor }}"
                                @selected(old('cara') === $valor)>
                        {{ $etiqueta }}
                        </option>
                        @endforeach
                    </select>

                    @error('cara')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="condicion" class="form-label">
                        Condición
                    </label>

                    <select id="condicion"
                            name="condicion"
                            class="form-select @error('condicion') is-invalid @enderror"
                            required>
                        <option value="">Selecciona una condición</option>

                        @foreach ($condiciones as $valor => $etiqueta)
                        <option value="{{ $valor }}"
                                @selected(old('condicion') === $valor)>
                        {{ $etiqueta }}
                        </option>
                        @endforeach
                    </select>

                    @error('condicion')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <p class="small text-muted mt-3">
                Se guardará un nuevo registro con tu usuario y la fecha actual.
                Los registros anteriores se conservarán en el historial.
            </p>

            <button type="submit" class="btn btn-primary">
                Guardar registro
            </button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h2 class="h5 mb-3">Historial del odontograma</h2>

        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Pieza</th>
                    <th>Cara</th>
                    <th>Condición</th>
                    <th>Odontólogo</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($registros as $registro)
                <tr>
                    <td>
                        {{ $registro->registrado_en?->format('d/m/Y H:i') }}
                    </td>
                    <td>{{ $registro->pieza }}</td>
                    <td>
                        {{ $caras[$registro->cara] ?? $registro->cara }}
                    </td>
                    <td>
                        {{ $condiciones[$registro->condicion] ?? $registro->condicion }}
                    </td>
                    <td>
                        {{ $registro->odontologo?->usuario?->nombre ?? 'No disponible' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        Este paciente todavía no tiene registros de odontograma.
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.pieza-boton').forEach((boton) => {
        boton.addEventListener('click', () => {
            document.getElementById('pieza').value = boton.dataset.pieza;
            document.getElementById('cara').focus();
        });
    });
</script>
@endpush
