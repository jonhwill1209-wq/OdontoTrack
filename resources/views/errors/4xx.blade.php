@extends('errors.plantilla')

@section('titulo', 'No se pudo completar la solicitud')
@section('codigo', (string) (($exception ?? null)?->getStatusCode() ?? 400))
@section('icono', 'bi-exclamation-circle')
@section('mensaje', 'Regresa a la página anterior e inténtalo de nuevo desde el sistema.')
