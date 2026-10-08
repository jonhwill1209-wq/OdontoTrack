{{-- Responsable: Persona 3. Cada enlace aparece solo cuando existe su ruta,
     así ningún módulo necesita editar este archivo. --}}
<ul class="nav flex-column">
    <li class="nav-item">
        <a class="nav-link text-white" href="{{ route('dashboard') }}">Dashboard</a>
    </li>
    @if (Route::has('pacientes.index'))
        <li class="nav-item">
            <a class="nav-link text-white" href="{{ route('pacientes.index') }}">Pacientes</a>
        </li>
    @endif
    @if (Route::has('citas.index'))
        <li class="nav-item">
            <a class="nav-link text-white" href="{{ route('citas.index') }}">Agenda de citas</a>
        </li>
    @endif
</ul>
