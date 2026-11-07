<?php

namespace App\Exports;

use App\Models\Cita;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReporteCitasExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    public function __construct(private readonly Collection $citas) {}

    public function collection(): Collection
    {
        return $this->citas;
    }

    public function headings(): array
    {
        return ['Fecha', 'Hora inicio', 'Hora fin', 'Paciente', 'Odontólogo', 'Consultorio', 'Motivo', 'Estado'];
    }

    public function map(mixed $cita): array
    {
        if (! $cita instanceof Cita) {
            return [];
        }

        return [
            $cita->inicio->format('d/m/Y'),
            $cita->inicio->format('H:i'),
            $cita->fin->format('H:i'),
            $cita->paciente?->nombre_completo ?? '',
            $cita->odontologo?->usuario?->nombre ?? '',
            $cita->consultorio?->nombre ?? '',
            $cita->motivo ?? '',
            $cita->estado,
        ];
    }
}
