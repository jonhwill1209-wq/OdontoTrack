@extends('layouts.app')
@section('titulo','Expediente clínico')
@section('content')
<h1 class="h3">Expediente clínico</h1>
<p>{{ $paciente->nombre_completo }} · Expediente #{{ $expediente->id_expediente }}</p>
<div class="d-flex flex-wrap gap-2 mb-4"><a class="btn btn-outline-secondary" href="{{ route('pacientes.show',$paciente) }}">Volver al paciente</a><a class="btn btn-outline-primary" href="{{ route('odontograma.show',$paciente) }}">Abrir odontograma e historial</a></div>
@if($errors->any())<div class="alert alert-danger" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<section class="card mb-4"><div class="card-body"><h2 class="h5">Anamnesis</h2>
<form method="POST" action="{{ route('expedientes.anamnesis',$paciente) }}">@csrf @method('PUT')
@foreach(['alergias'=>'Alergias','antecedentes'=>'Antecedentes','medicamentos'=>'Medicamentos'] as $campo=>$label)
<label class="form-label" for="{{ $campo }}">{{ $label }}</label><textarea class="form-control mb-3" id="{{ $campo }}" name="{{ $campo }}" maxlength="5000">{{ old($campo,$expediente->anamnesis?->$campo) }}</textarea>
@endforeach
<button class="btn btn-primary">Guardar anamnesis</button></form></div></section>
<section class="card mb-4"><div class="card-body"><h2 class="h5">Tratamientos y evolución clínica</h2>
@forelse($expediente->tratamientos as $t)
<article class="border rounded p-3 mb-3"><h3 class="h6">Tratamiento #{{ $t->id_tratamiento }} · {{ $t->estado }}</h3><p><strong>Diagnóstico:</strong> {{ $t->diagnostico }}</p><p><strong>Plan:</strong> {{ $t->plan }}</p>
<details><summary>Editar tratamiento</summary><form method="POST" action="{{ route('expedientes.tratamientos.update',[$paciente,$t]) }}">@csrf @method('PUT') @include('expedientes.tratamiento-form',['t'=>$t])</form></details>
<h4 class="h6 mt-3">Historial de evolución</h4>
@forelse($t->evoluciones->sortByDesc('fecha_registro') as $e)
<p>{{ $e->fecha_registro?->format('d/m/Y H:i') }} · {{ $e->odontologo?->usuario?->nombre }}<br>{{ $e->observacion }}</p>
@if($e->id_odontologo === auth()->user()->odontologo->id_odontologo)
<details class="mb-2"><summary>Editar observación propia</summary><form method="POST" action="{{ route('expedientes.evoluciones.update',[$paciente,$t,$e]) }}">@csrf @method('PUT')<label class="form-label">Observación<textarea class="form-control" name="observacion" maxlength="5000" required>{{ $e->observacion }}</textarea></label><button class="btn btn-outline-primary">Guardar corrección</button></form></details>
@endif
@empty<p class="text-muted">Sin evoluciones registradas.</p>@endforelse
<form method="POST" action="{{ route('expedientes.evoluciones.store',[$paciente,$t]) }}">@csrf<label class="form-label w-100">Nueva evolución<textarea class="form-control" name="observacion" maxlength="5000" required>{{ old('observacion') }}</textarea></label><button class="btn btn-primary">Registrar evolución</button></form></article>
@empty<p>No hay tratamientos registrados.</p>@endforelse
<details><summary>Nuevo tratamiento</summary><form method="POST" action="{{ route('expedientes.tratamientos.store',$paciente) }}">@csrf @include('expedientes.tratamiento-form',['t'=>null])</form></details>
</div></section>
<section class="card"><div class="card-body"><h2 class="h5">Archivos clínicos</h2><p>Radiografías JPG o PNG y documentos PDF. Máximo 5 MB por archivo.</p>
<form id="archivo-form" method="POST" enctype="multipart/form-data" action="{{ route('expedientes.archivos.store',$paciente) }}">@csrf
<label class="form-label" for="archivo">Archivo</label><input class="form-control mb-2" id="archivo" name="archivo" type="file" accept=".jpg,.jpeg,.png,.pdf" required>
<label class="form-label" for="descripcion">Descripción</label><input class="form-control mb-3" id="descripcion" name="descripcion" maxlength="500" value="{{ old('descripcion') }}" required><button class="btn btn-primary">Subir archivo</button></form>
<div class="table-responsive mt-3"><table class="table"><thead><tr><th>Archivo</th><th>Descripción</th><th>Fecha y autor</th><th>Tamaño</th><th>Consulta</th></tr></thead><tbody>
@forelse($expediente->archivos->sortByDesc('fecha') as $a)<tr><td>{{ $a->nombre_original }}</td><td>{{ $a->descripcion }}</td><td>{{ $a->fecha?->format('d/m/Y H:i') }} · {{ $a->odontologo?->usuario?->nombre }}</td><td>{{ number_format($a->tamano/1024,1) }} KB</td><td><a href="{{ route('expedientes.archivos.show',[$paciente,$a]) }}" target="_blank" rel="noopener">Ver</a> · <a href="{{ route('expedientes.archivos.show',[$paciente,$a,'descargar'=>1]) }}">Descargar</a></td></tr>
@empty<tr><td colspan="5">Sin archivos cargados.</td></tr>@endforelse
</tbody></table></div></div></section>
@endsection
@push('scripts')
<script>
document.getElementById('archivo').addEventListener('change', function() {
 const f=this.files[0]; this.setCustomValidity('');
 if(f && (!/\.(jpe?g|png|pdf)$/i.test(f.name) || !['image/jpeg','image/png','application/pdf'].includes(f.type))) this.setCustomValidity('Selecciona un JPG, PNG o PDF.');
 if(f && f.size>5*1024*1024) this.setCustomValidity('El archivo supera los 5 MB.');
 this.reportValidity();
});
</script>
@endpush
