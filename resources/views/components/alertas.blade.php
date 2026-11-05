@if (session('success'))
<div class="alerta alerta-exito">{{ session('success') }}</div>
@endif
@if (session('warning'))
<div class="alerta alerta-advertencia">{{ session('warning') }}</div>
@endif
@if (session('error'))
<div class="alerta alerta-error">{{ session('error') }}</div>
@endif
@if ($errors->any())
<div class="alerta alerta-error">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
