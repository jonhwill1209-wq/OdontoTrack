<?php

namespace App\Http\Controllers;

use App\Exports\ReporteCitasExport;
use App\Services\ReporteService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReporteController extends Controller
{
    public function __construct(private readonly ReporteService $reporteService) {}

    public function index(Request $request): View
    {
        [$desde, $hasta] = $this->fechas($request);
        $idOdontologo = $this->idOdontologo($request);
        $citas = $this->reporteService->citas($desde, $hasta, $idOdontologo);
        $totales = $this->reporteService->totales($desde, $hasta, $idOdontologo);
        $porDia = $this->reporteService->porDia($desde, $hasta, $idOdontologo);

        return view('reportes.index', compact('desde', 'hasta', 'citas', 'totales', 'porDia'));
    }

    public function excel(Request $request): BinaryFileResponse
    {
        [$desde, $hasta] = $this->fechas($request);
        $citas = $this->reporteService->citas($desde, $hasta, $this->idOdontologo($request));

        return Excel::download(
            new ReporteCitasExport($citas),
            "reporte_citas_{$desde}_{$hasta}.xlsx"
        );
    }

    public function pdf(Request $request)
    {
        [$desde, $hasta] = $this->fechas($request);
        $idOdontologo = $this->idOdontologo($request);
        $citas = $this->reporteService->citas($desde, $hasta, $idOdontologo);
        $totales = $this->reporteService->totales($desde, $hasta, $idOdontologo);

        return Pdf::loadView('reportes.pdf', compact('desde', 'hasta', 'citas', 'totales'))
            ->download("reporte_citas_{$desde}_{$hasta}.pdf");
    }

    private function fechas(Request $request): array
    {
        $validados = $request->validate([
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date'],
        ], [
            'desde.date' => 'La fecha inicial no es válida.',
            'hasta.date' => 'La fecha final no es válida.',
        ]);
        $desde = $validados['desde'] ?? now()->startOfMonth()->toDateString();
        $hasta = $validados['hasta'] ?? now()->endOfMonth()->toDateString();

        if ($hasta < $desde) {
            throw ValidationException::withMessages([
                'hasta' => 'La fecha final debe ser igual o posterior a la inicial.',
            ]);
        }

        if (Carbon::parse($desde)->diffInDays(Carbon::parse($hasta)) > 366) {
            throw ValidationException::withMessages([
                'hasta' => 'El rango del reporte no puede superar un año.',
            ]);
        }

        return [$desde, $hasta];
    }

    private function idOdontologo(Request $request): ?int
    {
        if (! $request->user()->esOdontologo()) {
            return null;
        }

        $odontologo = $request->user()->odontologo;
        abort_unless($odontologo !== null, 403, 'Tu usuario no tiene un perfil de odontólogo.');

        return $odontologo->id_odontologo;
    }
}
