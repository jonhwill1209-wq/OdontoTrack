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
        ['ruta' => 'usuarios.index', 'texto' => 'Usuarios', 'icono' => 'bi-person-gear', 'roles' => ['RECEPCIONISTA'], 'seccion' => 'Administración'],
        ['ruta' => 'odontologos.index', 'texto' => 'Odontólogos', 'icono' => 'bi-person-badge', 'roles' => ['RECEPCIONISTA'], 'seccion' => 'Administración'],
        ['ruta' => 'consultorios.index', 'texto' => 'Consultorios', 'icono' => 'bi-door-open', 'roles' => ['RECEPCIONISTA'], 'seccion' => 'Administración'],
        ['ruta' => 'cuenta.edit', 'texto' => 'Mi cuenta', 'icono' => 'bi-person-lock', 'roles' => ['RECEPCIONISTA', 'ODONTOLOGO'], 'seccion' => 'Cuenta'],
    ];

    $usuarioActual = auth()->user();
    $visibles = array_filter($opciones, fn ($opcion) => Route::has($opcion['ruta'])
        && in_array($usuarioActual->rol, $opcion['roles'], true)
        && ! (in_array($opcion['ruta'], $requierePerfilOdontologo, true) && $usuarioActual->esOdontologo() && $usuarioActual->odontologo === null));
    $seccionAnterior = null;
@endphp

<ul class="ot-menu">
    @foreach ($visibles as $opcion)
        @php($seccion = $opcion['seccion'] ?? null)

        @if ($seccion && $seccion !== $seccionAnterior)
            <li class="px-3 pt-3 pb-1 small text-uppercase fw-semibold text-secondary" role="presentation">{{ $seccion }}</li>
        @endif

        @php($seccionAnterior = $seccion)
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
