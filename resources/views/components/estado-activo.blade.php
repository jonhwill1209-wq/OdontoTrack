@props(['activo'])

<span {{ $attributes->class(['ot-estado', 'ot-estado-activo' => $activo, 'ot-estado-inactivo' => ! $activo]) }}>
    {{ $activo ? 'Activo' : 'Inactivo' }}
</span>
