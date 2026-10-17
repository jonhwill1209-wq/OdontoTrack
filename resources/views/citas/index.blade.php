@extends('layouts.app')

@section('titulo', 'Agenda de citas - OdontoTrack')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h2 mb-0">Agenda de citas</h1>
    <a href="{{ route('citas.create') }}" class="btn btn-primary">Nueva cita</a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label for="filtro-odontologo" class="form-label">Odontólogo</label>
                <select id="filtro-odontologo" class="form-select">
                    <option value="">Todos</option>
                    @foreach ($odontologos as $odontologo)
                    <option value="{{ $odontologo->id_odontologo }}">{{ $odontologo->usuario->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="filtro-consultorio" class="form-label">Consultorio</label>
                <select id="filtro-consultorio" class="form-select">
                    <option value="">Todos</option>
                    @foreach ($consultorios as $consultorio)
                    <option value="{{ $consultorio->id_consultorio }}">{{ $consultorio->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div id="calendario"></div>
        <p class="small text-secondary mt-3 mb-0">
            Azul: programada · Gris: cancelada · Verde: realizada.
            Haz clic en una cita programada para editarla o cancelarla, o en un espacio vacío para agendar.
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filtroOdontologo = document.getElementById('filtro-odontologo');
    const filtroConsultorio = document.getElementById('filtro-consultorio');
    const urlEventos = @json(route('citas.eventos'));
    const urlCrear = @json(route('citas.create'));

    const calendario = new FullCalendar.Calendar(document.getElementById('calendario'), {
        initialView: 'timeGridWeek',
        locale: 'es',
        allDaySlot: false,
        nowIndicator: true,
        height: 'auto',
        slotMinTime: '07:00:00',
        slotMaxTime: '20:00:00',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: { today: 'Hoy', month: 'Mes', week: 'Semana', day: 'Día' },

        events: function (info, ok, fail) {
            const params = new URLSearchParams({ start: info.startStr, end: info.endStr });
            if (filtroOdontologo.value) params.set('id_odontologo', filtroOdontologo.value);
            if (filtroConsultorio.value) params.set('id_consultorio', filtroConsultorio.value);

            fetch(urlEventos + '?' + params.toString(), { headers: { 'Accept': 'application/json' } })
                .then(function (r) { if (!r.ok) throw new Error('Error al cargar citas'); return r.json(); })
                .then(ok)
                .catch(fail);
        },

        eventDidMount: function (info) {
            const p = info.event.extendedProps;
            info.el.title = p.odontologo + ' · ' + p.consultorio + (p.motivo ? ' · ' + p.motivo : '');
        },

        eventClick: function (info) {
            info.jsEvent.preventDefault();
            const url = info.event.extendedProps.url_editar;
            if (url) window.location.href = url;
        },

        dateClick: function (info) {
            const inicio = info.allDay ? info.dateStr + 'T08:00' : info.dateStr.slice(0, 16);
            window.location.href = urlCrear + '?inicio=' + encodeURIComponent(inicio);
        }
    });

    calendario.render();

    [filtroOdontologo, filtroConsultorio].forEach(function (select) {
        select.addEventListener('change', function () { calendario.refetchEvents(); });
    });
});
</script>
@endpush
