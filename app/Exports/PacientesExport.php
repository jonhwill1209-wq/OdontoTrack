<?php

namespace App\Exports;

use App\Services\PacienteService;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class PacientesExport extends DefaultValueBinder implements
    FromQuery,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithCustomValueBinder
{
    public function __construct(
        private readonly PacienteService $pacienteService,
        private readonly string $buscar = '',
        private readonly string $estado = '1'
    ) {
    }

    public function query(): Builder
    {
        return $this->pacienteService
            ->consultaFiltrada($this->buscar, $this->estado);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombres',
            'Apellidos',
            'Fecha de nacimiento',
            'Teléfono',
            'Correo electrónico',
            'Estado',
        ];
    }

    public function map(mixed $paciente): array
    {
        return [
            $paciente->id_paciente,
            $paciente->nombres,
            $paciente->apellidos,
            $paciente->fecha_nacimiento?->format('d/m/Y') ?? '',
            $paciente->telefono ?? '',
            $paciente->correo ?? '',
            $paciente->activo ? 'Activo' : 'Inactivo',
        ];
    }

    public function bindValue(Cell $cell, mixed $value): bool
    {
        if (is_string($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }
}
