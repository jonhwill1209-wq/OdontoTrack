@props(['citas', 'columna' => 'consultorio'])

@if ($citas->isEmpty())
    <div class="ot-vacio">
        <i class="bi bi-calendar2-check" aria-hidden="true"></i>
        <p>No hay citas programadas a futuro.</p>
    </div>
@else
    <div class="table-responsive ot-tabla">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th scope="col">Fecha</th>
                    <th scope="col">Hora</th>
                    <th scope="col">Paciente</th>
                    <th scope="col">{{ $columna === 'odontologo' ? 'Odontólogo' : 'Consultorio' }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($citas as $cita)
                    <tr>
                        <td class="text-nowrap">{{ $cita->inicio->locale('es')->translatedFormat('D j \d\e M Y') }}</td>
                        <td class="text-nowrap">
                            <strong>{{ $cita->inicio->format('g:i A') }}</strong>
                            <span class="ot-hora-fin">a {{ $cita->fin->format('g:i A') }}</span>
                        </td>
                        <td>{{ $cita->paciente?->nombre_completo }}</td>
                        <td>{{ $columna === 'odontologo' ? $cita->odontologo?->usuario?->nombre : $cita->consultorio?->nombre }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
