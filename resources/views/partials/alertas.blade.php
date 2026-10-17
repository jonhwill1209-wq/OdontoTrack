@if (session('exito'))
    <div class="alert alert-success alert-dismissible ot-alerta" role="status">
        <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
        <div>{{ session('exito') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible ot-alerta" role="alert">
        <i class="bi bi-exclamation-octagon-fill" aria-hidden="true"></i>
        <div>{{ session('error') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger ot-alerta" role="alert">
        <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
        @if ($errors->count() === 1)
            <div>{{ $errors->first() }}</div>
        @else
            <div>
                <p class="mb-1 fw-semibold">Revisa estos datos:</p>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endif
