<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo') | OdontoTrack</title>
    <link rel="icon" href="{{ asset('img/odontotrack.svg') }}" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/base.css') }}" rel="stylesheet">
    <link href="{{ asset('css/modules/usuarios.css') }}" rel="stylesheet">
</head>
<body>
<main class="ot-invitado">
    <section class="ot-error">
        <span class="ot-error-icono" aria-hidden="true">
            <i class="bi @yield('icono', 'bi-exclamation-octagon')"></i>
        </span>
        <p class="ot-error-codigo">@yield('codigo')</p>
        <h1>@yield('titulo')</h1>
        <p class="ot-error-texto">@yield('mensaje')</p>

        <div class="ot-error-acciones">
            <a href="{{ url('/dashboard') }}" class="btn btn-primary">Volver al inicio</a>
        </div>
    </section>
</main>
</body>
</html>
