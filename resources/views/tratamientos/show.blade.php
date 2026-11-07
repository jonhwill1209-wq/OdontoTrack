@extends('layouts.app')

@section('titulo', 'Detalle de tratamiento')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <h1 class="h2 mb-1">Tratamiento clínico</h1>
        <p class="text-secondary mb-0">
            Paciente: <a href="{{ route('expedientes.show', $tratamiento->expediente->paciente) }}">{{ $tratamiento->expediente->paciente->nombre_completo }}</a>
            · Inicio {{ $tratamiento->fecha_inicio?->format('d/m/Y') }}
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('tratamientos.edit', $tratamiento) }}" class="btn btn-outline-primary">Editar</a>
        <form method="POST" action="{{ route('tratamientos.destroy', $tratamiento) }}"
              onsubmit="return confirm('¿Eliminar este tratamiento? Solo se permite si aún no tiene evoluciones.');">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-outline-danger">Eliminar</button>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-6"><section class="card h-100"><div class="card-body">
        <h2 class="h5">Diagnóstico</h2><p class="mb-0">{!! nl2br(e($tratamiento->diagnostico)) !!}</p>
    </div></section></div>
    <div class="col-lg-6"><section class="card h-100"><div class="card-body">
        <h2 class="h5">Plan · {{ str_replace('_', ' ', ucfirst(strtolower($tratamiento->estado))) }}</h2>
        <p class="mb-0">{!! nl2br(e($tratamiento->plan)) !!}</p>
    </div></section></div>
</div>

<section class="card mb-4">
    <div class="card-header"><h2 class="h5 mb-0">Evoluciones clínicas</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('tratamientos.evoluciones.store', $tratamiento) }}" class="mb-4">
            @csrf
            <label for="observacion" class="form-label">Registrar evolución</label>
            <textarea id="observacion" name="observacion" rows="3" maxlength="5000" required
                      class="form-control @error('observacion') is-invalid @enderror">{{ old('observacion') }}</textarea>
            @error('observacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <button type="submit" class="btn btn-primary mt-2">Guardar evolución</button>
        </form>
        <div class="list-group list-group-flush">
            @forelse ($tratamiento->evoluciones->sortByDesc('fecha_registro') as $evolucion)
                <article class="list-group-item px-0">
                    <div class="d-flex flex-wrap justify-content-between gap-2">
                        <strong>{{ $evolucion->odontologo?->usuario?->nombre ?? 'Odontólogo' }}</strong>
                        <time class="text-secondary">{{ $evolucion->fecha_registro?->format('d/m/Y H:i') }}</time>
                    </div>
                    <p class="mb-0 mt-2">{!! nl2br(e($evolucion->observacion)) !!}</p>
                </article>
            @empty
                <p class="text-secondary mb-0">Todavía no hay evoluciones registradas.</p>
            @endforelse
        </div>
    </div>
</section>
<a href="{{ route('expedientes.show', $tratamiento->expediente->paciente) }}" class="btn btn-outline-secondary">Volver al expediente</a>
@endsection
