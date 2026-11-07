<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de citas</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #23313b; }
        h1 { font-size: 20px; margin-bottom: 4px; }
        p { margin-top: 0; color: #52616b; }
        .resumen { margin: 18px 0; }
        .resumen span { display: inline-block; margin-right: 18px; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d5dde2; padding: 6px; text-align: left; }
        th { background: #eaf2f6; }
        tr:nth-child(even) { background: #f7f9fa; }
    </style>
</head>
<body>
<h1>OdontoTrack · Reporte de citas</h1>
<p>Período: {{ \Illuminate\Support\Carbon::parse($desde)->format('d/m/Y') }} al {{ \Illuminate\Support\Carbon::parse($hasta)->format('d/m/Y') }}</p>
<div class="resumen">
    <span>Total: <strong>{{ $totales['total'] }}</strong></span>
    <span>Programadas: <strong>{{ $totales['programadas'] }}</strong></span>
    <span>Realizadas: <strong>{{ $totales['realizadas'] }}</strong></span>
    <span>Canceladas: <strong>{{ $totales['canceladas'] }}</strong></span>
</div>
<table>
    <thead><tr><th>Fecha</th><th>Hora</th><th>Paciente</th><th>Odontólogo</th><th>Consultorio</th><th>Motivo</th><th>Estado</th></tr></thead>
    <tbody>
    @forelse ($citas as $cita)
        <tr>
            <td>{{ $cita->inicio->format('d/m/Y') }}</td>
            <td>{{ $cita->inicio->format('H:i') }}</td>
            <td>{{ $cita->paciente?->nombre_completo }}</td>
            <td>{{ $cita->odontologo?->usuario?->nombre }}</td>
            <td>{{ $cita->consultorio?->nombre }}</td>
            <td>{{ $cita->motivo ?: '—' }}</td>
            <td>{{ $cita->estado }}</td>
        </tr>
    @empty
        <tr><td colspan="7">No hay citas en este período.</td></tr>
    @endforelse
    </tbody>
</table>
</body>
</html>
