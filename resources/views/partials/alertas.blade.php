{{-- Mensajes de confirmación y de error. Uso en controladores:
     return redirect()->route('...')->with('exito', 'Paciente registrado correctamente.'); --}}
@if (session('exito'))
    <div class="alert alert-success">{{ session('exito') }}</div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
