@extends('layouts.app')

@section('titulo', 'Reportes')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="h2 mb-1">Reporte de citas</h1>
        <p class="text-secondary mb-0">Indicadores y citas registradas en el período seleccionado.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reportes.pdf', ['desde' => $desde, 'hasta' => $hasta]) }}" class="btn btn-outline-danger">Descargar PDF</a>
        <a href="{{ route('reportes.excel', ['desde' => $desde, 'hasta' => $hasta]) }}" class="btn btn-outline-success">Exportar Excel</a>
    </div>
</div>

<form method="GET" action="{{ route('reportes.index') }}" class="card card-body mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <label for="desde" class="form-label">Desde</label>
            <input id="desde" name="desde" type="date" value="{{ $desde }}" required class="form-control">
        </div>
        <div class="col-md-4">
            <label for="hasta" class="form-label">Hasta</label>
            <input id="hasta" name="hasta" type="date" value="{{ $hasta }}" required class="form-control">
        </div>
        <div class="col-md-4"><button type="submit" class="btn btn-primary">Aplicar período</button></div>
    </div>
</form>

<section class="row g-3 mb-4" aria-label="Indicadores de citas">
    @foreach (['total' => 'Total de citas', 'programadas' => 'Programadas', 'realizadas' => 'Realizadas', 'canceladas' => 'Canceladas'] as $key => $label)
        <div class="col-6 col-xl-3"><article class="card h-100"><div class="card-body">
            <h2 class="h6 text-secondary">{{ $label }}</h2><p class="display-6 mb-0">{{ $totales[$key] }}</p>
        </div></article></div>
    @endforeach
</section>

<section class="card mb-4">
    <div class="card-header"><h2 class="h5 mb-0">Citas por día</h2></div>
    <div class="card-body"><div style="height:320px"><canvas id="graficaCitas" aria-label="Gráfico de cantidad de citas por día" role="img"></canvas></div></div>
</section>

<section class="card">
    <div class="card-header"><h2 class="h5 mb-0">Detalle de citas ({{ $citas->count() }})</h2></div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Fecha y hora</th><th>Paciente</th><th>Odontólogo</th><th>Consultorio</th><th>Motivo</th><th>Estado</th></tr></thead>
            <tbody>
            @forelse ($citas as $cita)
                <tr>
                    <td>{{ $cita->inicio->format('d/m/Y H:i') }}</td>
                    <td>{{ $cita->paciente?->nombre_completo }}</td>
                    <td>{{ $cita->odontologo?->usuario?->nombre }}</td>
                    <td>{{ $cita->consultorio?->nombre }}</td>
                    <td>{{ $cita->motivo ?: '—' }}</td>
                    <td>{{ ucfirst(strtolower($cita->estado)) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-secondary py-4">No hay citas para este período.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filas = @json($porDia);
    new Chart(document.getElementById('graficaCitas'), {
        type: 'bar',
        data: {
            labels: filas.map(fila => fila.fecha),
            datasets: [{ label: 'Citas', data: filas.map(fila => fila.total), backgroundColor: '#3085a8' }]
        },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
    });
});
</script>
@endpush
