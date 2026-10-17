@extends('layouts.app')

@section('titulo', 'Expediente clínico')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h1 class="h3 mb-1">Expediente clínico</h1>
        <p class="text-muted mb-0">
            {{ $paciente->nombre_completo }} · Expediente #{{ $expediente->id_expediente }}
        </p>
    </div>

    <a href="{{ route('pacientes.show', $paciente) }}" class="btn btn-outline-secondary">
        Volver al paciente
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h2 class="h5 mb-3">Anamnesis</h2>

        <form method="POST" action="{{ route('expedientes.anamnesis.update', $paciente) }}">
            @csrf
            @method('PUT')

            <div class="row g-3">
                @foreach ([
                    'alergias' => 'Alergias',
                    'antecedentes' => 'Antecedentes médicos',
                    'medicamentos' => 'Medicamentos habituales',
                ] as $campo => $etiqueta)
                <div class="col-md-4">
                    <label for="{{ $campo }}" class="form-label">{{ $etiqueta }}</label>
                    <textarea id="{{ $campo }}" name="{{ $campo }}" rows="4"
                              class="form-control @error($campo) is-invalid @enderror">{{ old($campo, $expediente->anamnesis?->$campo) }}</textarea>
                    @error($campo)
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary mt-3">Guardar anamnesis</button>
        </form>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h2 class="h5 mb-3">Registrar tratamiento</h2>

        <form method="POST" action="{{ route('expedientes.tratamientos.store', $paciente) }}">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="diagnostico" class="form-label">Diagnóstico</label>
                    <textarea id="diagnostico" name="diagnostico" rows="3" required
                              class="form-control @error('diagnostico') is-invalid @enderror">{{ old('diagnostico') }}</textarea>
                    @error('diagnostico')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="plan" class="form-label">Plan de tratamiento</label>
                    <textarea id="plan" name="plan" rows="3" required
                              class="form-control @error('plan') is-invalid @enderror">{{ old('plan') }}</textarea>
                    @error('plan')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="estado" class="form-label">Estado</label>
                    <select id="estado" name="estado" required class="form-select @error('estado') is-invalid @enderror">
                        @foreach ([
                            'PLANIFICADO' => 'Planificado',
                            'EN_CURSO' => 'En curso',
                            'FINALIZADO' => 'Finalizado',
                        ] as $valor => $etiqueta)
                        <option value="{{ $valor }}" @selected(old('estado', 'PLANIFICADO') === $valor)>{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                    @error('estado')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="fecha_inicio" class="form-label">Fecha de inicio</label>
                    <input id="fecha_inicio" name="fecha_inicio" type="date" required
                           value="{{ old('fecha_inicio', now()->toDateString()) }}"
                           class="form-control @error('fecha_inicio') is-invalid @enderror">
                    @error('fecha_inicio')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Registrar tratamiento</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h2 class="h5 mb-3">Tratamientos y evolución clínica</h2>

        @forelse ($expediente->tratamientos as $tratamiento)
        <div class="border rounded p-3 mb-3">
            <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                <div>
                    <h3 class="h6 mb-1">Tratamiento #{{ $tratamiento->id_tratamiento }}</h3>
                    <p class="mb-1"><strong>Diagnóstico:</strong> {{ $tratamiento->diagnostico }}</p>
                    <p class="mb-1"><strong>Plan:</strong> {{ $tratamiento->plan }}</p>
                    <p class="mb-0 text-muted">
                        Inicio: {{ $tratamiento->fecha_inicio?->format('d/m/Y') }} · Estado: {{ str_replace('_', ' ', $tratamiento->estado) }}
                    </p>
                </div>
            </div>

            <details class="mt-3">
                <summary class="text-primary">Editar tratamiento</summary>
                <form method="POST" action="{{ route('expedientes.tratamientos.update', [$paciente, $tratamiento]) }}" class="row g-3 mt-1">
                    @csrf
                    @method('PUT')
                    <div class="col-md-6">
                        <label class="form-label" for="diagnostico-{{ $tratamiento->id_tratamiento }}">Diagnóstico</label>
                        <textarea id="diagnostico-{{ $tratamiento->id_tratamiento }}" name="diagnostico" rows="2" required class="form-control">{{ $tratamiento->diagnostico }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="plan-{{ $tratamiento->id_tratamiento }}">Plan</label>
                        <textarea id="plan-{{ $tratamiento->id_tratamiento }}" name="plan" rows="2" required class="form-control">{{ $tratamiento->plan }}</textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="estado-{{ $tratamiento->id_tratamiento }}">Estado</label>
                        <select id="estado-{{ $tratamiento->id_tratamiento }}" name="estado" class="form-select" required>
                            @foreach (['PLANIFICADO' => 'Planificado', 'EN_CURSO' => 'En curso', 'FINALIZADO' => 'Finalizado'] as $valor => $etiqueta)
                            <option value="{{ $valor }}" @selected($tratamiento->estado === $valor)>{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="fecha_inicio-{{ $tratamiento->id_tratamiento }}">Fecha de inicio</label>
                        <input id="fecha_inicio-{{ $tratamiento->id_tratamiento }}" type="date" name="fecha_inicio" required value="{{ $tratamiento->fecha_inicio?->toDateString() }}" class="form-control">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-outline-primary btn-sm">Guardar cambios</button>
                    </div>
                </form>
            </details>

            <div class="mt-3">
                <h4 class="h6">Evolución clínica</h4>
                <ul class="list-group list-group-flush mb-3">
                    @forelse ($tratamiento->evoluciones as $evolucion)
                    <li class="list-group-item px-0">
                        <div>{{ $evolucion->observacion }}</div>
                        <small class="text-muted">
                            {{ $evolucion->fecha_registro?->format('d/m/Y H:i') }} ·
                            {{ $evolucion->odontologo?->usuario?->nombre ?? 'Odontólogo no disponible' }}
                        </small>
                    </li>
                    @empty
                    <li class="list-group-item px-0 text-muted">No hay notas de evolución.</li>
                    @endforelse
                </ul>

                <form method="POST" action="{{ route('expedientes.evoluciones.store', [$paciente, $tratamiento]) }}">
                    @csrf
                    <label class="form-label" for="observacion-{{ $tratamiento->id_tratamiento }}">Agregar nota de evolución</label>
                    <textarea id="observacion-{{ $tratamiento->id_tratamiento }}" name="observacion" rows="2" required class="form-control"></textarea>
                    <button type="submit" class="btn btn-outline-primary btn-sm mt-2">Guardar nota</button>
                </form>
            </div>
        </div>
        @empty
        <p class="text-muted mb-0">Este paciente todavía no tiene tratamientos registrados.</p>
        @endforelse
    </div>
</div>
@endsection
