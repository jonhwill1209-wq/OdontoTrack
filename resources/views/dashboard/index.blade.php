@extends('layouts.app')

@section('titulo', 'Dashboard')

@section('content')
<h2>Dashboard</h2>
<p>Bienvenido, {{ auth()->user()->nombre }}.</p>
@endsection
