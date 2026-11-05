@extends('errors.minima')

@section('titulo', 'Error del servidor')
@section('codigo', (string) (($exception ?? null)?->getStatusCode() ?? 500))
@section('icono', 'bi-exclamation-octagon')
@section('mensaje', 'El servidor no pudo completar la solicitud. Intenta de nuevo en unos minutos.')
