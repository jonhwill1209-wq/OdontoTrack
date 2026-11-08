@extends('layouts.app')

@section('titulo', 'Agenda de citas - OdontoTrack')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h2 mb-0">Agenda de citas</h1>
    <a href="{{ route('citas.create') }}" class="btn btn-primary">Nueva cita</a>
</div>

<div id="alerta-agenda" aria-live="polite"></div>

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
            Haz clic en una cita para ver su detalle, arrástrala para moverla o estira su borde inferior para cambiar la duración.
            Haz clic en un espacio vacío para agendar.
        </p>
    </div>
</div>

<div class="modal fade" id="modal-cita" tabindex="-1" aria-labelledby="modal-cita-titulo" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h5" id="modal-cita-titulo">Detalle de la cita</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <dl class="row mb-0">
                    <dt class="col-4">Paciente</dt><dd class="col-8" id="det-paciente"></dd>
                    <dt class="col-4">Odontólogo</dt><dd class="col-8" id="det-odontologo"></dd>
                    <dt class="col-4">Consultorio</dt><dd class="col-8" id="det-consultorio"></dd>
                    <dt class="col-4">Horario</dt><dd class="col-8" id="det-horario"></dd>
                    <dt class="col-4">Estado</dt><dd class="col-8" id="det-estado"></dd>
                    <dt class="col-4">Motivo</dt><dd class="col-8 mb-0" id="det-motivo"></dd>
                </dl>
            </div>
            <div class="modal-footer flex-wrap" id="det-acciones">
                <a href="#" id="det-editar" class="btn btn-primary">Editar</a>
                <form method="POST" id="det-form-realizar" class="d-inline"
                      onsubmit="return confirm('¿Marcar esta cita como realizada?');">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-success">Marcar realizada</button>
                </form>
                <form method="POST" id="det-form-cancelar" class="d-inline"
                      onsubmit="return confirm('¿Seguro que deseas cancelar esta cita?');">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-outline-danger">Cancelar cita</button>
                </form>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
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
    const csrf = @json(csrf_token());
    const contenedorAlerta = document.getElementById('alerta-agenda');
    const modalEl = document.getElementById('modal-cita');
    const modal = new bootstrap.Modal(modalEl);

    function mostrarAlerta(tipo, texto) {
        const div = document.createElement('div');
        div.className = 'alert alert-' + tipo + ' alert-dismissible';
        div.setAttribute('role', 'alert');
        div.textContent = texto;
        const cerrar = document.createElement('button');
        cerrar.type = 'button';
        cerrar.className = 'btn-close';
        cerrar.setAttribute('data-bs-dismiss', 'alert');
        cerrar.setAttribute('aria-label', 'Cerrar');
        div.appendChild(cerrar);
        contenedorAlerta.replaceChildren(div);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Envía el nuevo horario al servidor; si no hay disponibilidad, la cita vuelve a su lugar
    function guardarHorario(info) {
        const p = info.event.extendedProps;
        fetch(p.url_mover, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({
                inicio: info.event.start.toISOString(),
                fin: (info.event.end || info.event.start).toISOString()
            })
        })
        .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, datos: d }; }); })
        .then(function (res) {
            if (!res.ok) {
                info.revert();
                const errores = res.datos.errors ? Object.values(res.datos.errors).flat().join(' ') : null;
                mostrarAlerta('warning', res.datos.mensaje || errores || 'No se pudo reprogramar la cita.');
                return;
            }
            mostrarAlerta('success', res.datos.mensaje);
            calendario.refetchEvents();
        })
        .catch(function () {
            info.revert();
            mostrarAlerta('danger', 'Error de conexión: la cita no fue modificada.');
        });
    }

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

        editable: true,
        eventStartEditable: true,
        eventDurationEditable: true,
        snapDuration: '00:15:00',
        eventDrop: guardarHorario,
        eventResize: guardarHorario,

        eventClick: function (info) {
            info.jsEvent.preventDefault();
            const e = info.event;
            const p = e.extendedProps;
            const fmt = { dateStyle: 'full', timeStyle: 'short' };

            document.getElementById('det-paciente').textContent = e.title;
            document.getElementById('det-odontologo').textContent = p.odontologo;
            document.getElementById('det-consultorio').textContent = p.consultorio;
            document.getElementById('det-horario').textContent =
                e.start.toLocaleString('es-SV', fmt) + ' – ' + e.end.toLocaleTimeString('es-SV', { timeStyle: 'short' });
            document.getElementById('det-estado').textContent = p.estado;
            document.getElementById('det-motivo').textContent = p.motivo || 'Sin motivo registrado';

            const programada = !!p.url_editar;
            document.getElementById('det-editar').classList.toggle('d-none', !programada);
            document.getElementById('det-form-realizar').classList.toggle('d-none', !programada);
            document.getElementById('det-form-cancelar').classList.toggle('d-none', !programada);
            if (programada) {
                document.getElementById('det-editar').href = p.url_editar;
                document.getElementById('det-form-realizar').action = p.url_realizar;
                document.getElementById('det-form-cancelar').action = p.url_cancelar;
            }
            modal.show();
        },

        dateClick: function (info) {
            if (new Date(info.date) < new Date()) {
                mostrarAlerta('warning', 'No se pueden agendar citas en fechas u horas pasadas.');
                return;
            }
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
