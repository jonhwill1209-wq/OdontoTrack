@extends('layouts.app')

@section('titulo', 'Expediente clínico')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="h2 mb-1">Expediente clínico</h1>
        <p class="text-secondary mb-0">{{ $paciente->nombre_completo }} · Expediente #{{ $expediente->id_expediente }}</p>
    </div>
    <a href="{{ route('pacientes.show', $paciente) }}" class="btn btn-outline-secondary">Volver al paciente</a>
</div>

<section class="card mb-4">
    <div class="card-header"><h2 class="h5 mb-0">Anamnesis</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('expedientes.anamnesis.update', $expediente) }}">
            @csrf
            @method('PUT')
            <div class="row g-3">
                @foreach (['alergias' => 'Alergias', 'antecedentes' => 'Antecedentes', 'medicamentos' => 'Medicamentos actuales'] as $campo => $etiqueta)
                    <div class="col-md-4">
                        <label for="{{ $campo }}" class="form-label">{{ $etiqueta }}</label>
                        <textarea id="{{ $campo }}" name="{{ $campo }}" rows="4" maxlength="10000"
                                  class="form-control @error($campo) is-invalid @enderror">{{ old($campo, $expediente->anamnesis?->$campo) }}</textarea>
                        @error($campo)<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                @endforeach
            </div>
            <button type="submit" class="btn btn-primary mt-3">Guardar anamnesis</button>
        </form>
    </div>
</section>

<section class="card mb-4">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h2 class="h5 mb-0">Tratamientos</h2>
        <a href="{{ route('tratamientos.create', $expediente) }}" class="btn btn-sm btn-primary">Nuevo tratamiento</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Diagnóstico</th><th>Plan</th><th>Inicio</th><th>Estado</th><th></th></tr></thead>
            <tbody>
            @forelse ($expediente->tratamientos as $tratamiento)
                <tr>
                    <td>{{ \Illuminate\Support\Str::limit($tratamiento->diagnostico, 90) }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($tratamiento->plan, 90) }}</td>
                    <td>{{ $tratamiento->fecha_inicio?->format('d/m/Y') }}</td>
                    <td><span class="badge text-bg-info">{{ str_replace('_', ' ', ucfirst(strtolower($tratamiento->estado))) }}</span></td>
                    <td><a href="{{ route('tratamientos.show', $tratamiento) }}" class="btn btn-sm btn-outline-primary">Ver detalle</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-secondary py-4">Aún no hay tratamientos registrados.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>

<section class="card">
    <div class="card-header"><h2 class="h5 mb-0">Documentos e imágenes</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('expedientes.archivos.store', $expediente) }}" enctype="multipart/form-data" class="row g-3 align-items-end mb-4">
            @csrf
            <div class="col-md-8">
                <label for="archivo" class="form-label">Archivo (PDF, JPG o PNG; máximo 10 MB)</label>
                <input id="archivo" name="archivo" type="file" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png"
                       required class="form-control @error('archivo') is-invalid @enderror">
                @error('archivo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4"><button type="submit" class="btn btn-primary">Cargar archivo</button></div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Nombre</th><th>Tipo</th><th>Tamaño</th><th>Fecha</th><th>Acciones</th></tr></thead>
                <tbody>
                @forelse ($expediente->archivos->sortByDesc('subido_en') as $archivo)
                    <tr>
                        <td>{{ $archivo->nombre_original }}</td>
                        <td>{{ $archivo->mime_type }}</td>
                        <td>{{ number_format($archivo->tamano / 1024, 1) }} KB</td>
                        <td>{{ $archivo->subido_en?->format('d/m/Y H:i') }}</td>
                        <td class="d-flex gap-2">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('expedientes.archivos.download', $archivo) }}">Consultar</a>
                            <form method="POST" action="{{ route('expedientes.archivos.destroy', $archivo) }}"
                                  onsubmit="return confirm('¿Eliminar este archivo del expediente?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-secondary py-4">No hay archivos cargados.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
