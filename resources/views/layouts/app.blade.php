<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Inicio') | OdontoTrack</title>
    <link rel="icon" href="{{ asset('img/odontotrack.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/base.css') }}" rel="stylesheet">
    @stack('estilos')
</head>
<body>
@auth
    <div class="ot-app">
        <aside class="ot-lateral">
            <div class="offcanvas-lg offcanvas-start ot-lateral-panel" tabindex="-1" id="menuLateral" aria-label="Menú principal">
                <div class="ot-lateral-cabecera">
                    @include('partials.marca')
                    <button type="button" class="btn-close d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#menuLateral" aria-label="Cerrar menú"></button>
                </div>

                <nav class="ot-lateral-cuerpo">
                    @include('partials.menu')
                </nav>

                <form method="POST" action="{{ route('logout') }}" class="ot-lateral-pie">
                    @csrf
                    <button type="submit" class="ot-menu-enlace ot-salir">
                        <i class="bi bi-box-arrow-left" aria-hidden="true"></i>
                        <span>Cerrar sesión</span>
                    </button>
                </form>
            </div>
        </aside>

        <div class="ot-principal">
            <header class="ot-barra">
                <button type="button" class="btn ot-btn-menu d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#menuLateral" aria-controls="menuLateral" aria-label="Abrir menú">
                    <i class="bi bi-list" aria-hidden="true"></i>
                </button>
                <div class="d-lg-none">@include('partials.marca')</div>

                <div class="ot-usuario">
                    <i class="bi bi-person-circle" aria-hidden="true"></i>
                    <div>
                        <strong>{{ auth()->user()->nombre }}</strong>
                        <small>{{ auth()->user()->esOdontologo() ? 'Odontólogo' : 'Recepcionista' }}</small>
                    </div>
                </div>
            </header>

            <main class="ot-contenido">
                @include('partials.alertas')
                @yield('content')
            </main>
        </div>
    </div>
@else
    <main class="ot-invitado">
        @yield('content')
    </main>
@endauth

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
