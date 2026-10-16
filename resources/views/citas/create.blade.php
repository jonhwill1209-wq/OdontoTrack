@extends('layouts.app')

@section('titulo', 'Nueva cita - OdontoTrack')

@section('content')
<h1 class="h2 mb-4">Nueva cita</h1>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('citas.store') }}">
            @csrf
            @include('citas._form', ['textoBoton' => 'Agendar cita'])
        </form>
    </div>
</div>
@endsection
