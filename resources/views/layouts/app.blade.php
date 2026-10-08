<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'OdontoTrack')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/base.css') }}" rel="stylesheet">
    @stack('estilos')
</head>
<body>
@auth
    <div class="d-flex">
        <nav class="bg-dark text-white p-3 vh-100 flex-shrink-0" style="width: 240px;">
            <h5 class="mb-3">OdontoTrack</h5>
            <p class="small text-secondary">
                {{ auth()->user()->nombre }}<br>{{ auth()->user()->rol }}
            </p>
            @include('partials.menu')
            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm">Cerrar sesión</button>
            </form>
        </nav>
        <main class="flex-grow-1 p-4">
            @include('partials.alertas')
            @yield('content')
        </main>
    </div>
@else
    <main class="container py-5">
        @include('partials.alertas')
        @yield('content')
    </main>
@endauth

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
