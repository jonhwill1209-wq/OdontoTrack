@php
    //Cada opción aparece solo si su módulo ya registró la ruta y el rol tiene acceso
    $requierePerfilOdontologo = ['expedientes.index', 'reportes.index'];
    $opciones = [
        ['ruta' => 'dashboard', 'texto' => 'Inicio', 'icono' => 'bi-house-door', 'roles' => ['RECEPCIONISTA', 'ODONTOLOGO']],
        ['ruta' => 'pacientes.index', 'texto' => 'Pacientes', 'icono' => 'bi-people', 'roles' => ['RECEPCIONISTA', 'ODONTOLOGO']],
        ['ruta' => 'citas.index', 'texto' => 'Agenda de citas', 'icono' => 'bi-calendar-week', 'roles' => ['RECEPCIONISTA', 'ODONTOLOGO']],
        ['ruta' => 'expedientes.index', 'texto' => 'Expedientes', 'icono' => 'bi-folder2-open', 'roles' => ['ODONTOLOGO']],
        ['ruta' => 'odontograma.index', 'texto' => 'Odontograma', 'icono' => 'bi-grid-3x3-gap', 'roles' => ['ODONTOLOGO']],
        ['ruta' => 'reportes.index', 'texto' => 'Reportes', 'icono' => 'bi-bar-chart', 'roles' => ['RECEPCIONISTA', 'ODONTOLOGO']],
    ];
@endphp

<ul class="ot-menu">
    @foreach ($opciones as $opcion)
        @continue(! Route::has($opcion['ruta']) || ! in_array(auth()->user()->rol, $opcion['roles'], true))
        @continue(in_array($opcion['ruta'], $requierePerfilOdontologo, true) && auth()->user()->esOdontologo() && auth()->user()->odontologo === null)

        @php($modulo = Str::before($opcion['ruta'], '.'))
        @php($activo = request()->routeIs($modulo, $modulo . '.*'))

        <li>
            <a href="{{ route($opcion['ruta']) }}" @class(['ot-menu-enlace', 'activo' => $activo]) @if ($activo) aria-current="page" @endif>
                <i class="bi {{ $opcion['icono'] }}" aria-hidden="true"></i>
                <span>{{ $opcion['texto'] }}</span>
            </a>
        </li>
    @endforeach
</ul>
