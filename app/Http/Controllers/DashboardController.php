<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Tratamiento;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $usuario = $request->user();

        $citasHoy = Cita::with(['paciente', 'odontologo.usuario', 'consultorio'])
            ->whereDate('inicio', today())
            ->when($usuario->esOdontologo(), function ($consulta) use ($usuario) {
                $consulta->where('id_odontologo', $usuario->odontologo?->id_odontologo);
            })
            ->orderBy('inicio')
            ->get();

        return view('dashboard.index', [
            'citasHoy' => $citasHoy,
            'resumen' => $this->resumen($usuario, $citasHoy->where('estado', Cita::PROGRAMADA)->count()),
            'accesos' => $this->accesosRapidos($usuario),
        ]);
    }

    private function resumen(Usuario $usuario, int $programadasHoy): array
    {
        $resumen = [
            'pacientes' => Paciente::activos()->count(),
            'citasHoy' => $programadasHoy,
        ];

        if ($usuario->esOdontologo()) {
            $resumen['tratamientos'] = Tratamiento::where('estado', '!=', Tratamiento::FINALIZADO)->count();
        } else {
            $resumen['proximas'] = Cita::where('estado', Cita::PROGRAMADA)
                ->where('inicio', '>=', today()->addDay())
                ->where('inicio', '<', today()->addDays(8))
                ->count();
        }

        return $resumen;
    }

    private function accesosRapidos(Usuario $usuario): array
    {
        $accesos = $usuario->esOdontologo()
            ? [
                ['ruta' => 'citas.index', 'texto' => 'Ver mi agenda', 'icono' => 'bi-calendar-week'],
                ['ruta' => 'expedientes.index', 'texto' => 'Abrir un expediente', 'icono' => 'bi-folder2-open'],
                ['ruta' => 'pacientes.index', 'texto' => 'Buscar un paciente', 'icono' => 'bi-search'],
            ]
            : [
                ['ruta' => 'pacientes.create', 'texto' => 'Registrar paciente', 'icono' => 'bi-person-plus'],
                ['ruta' => 'pacientes.index', 'texto' => 'Buscar un paciente', 'icono' => 'bi-search'],
                ['ruta' => 'citas.index', 'texto' => 'Ver la agenda', 'icono' => 'bi-calendar-week'],
            ];

        return array_values(array_filter($accesos, fn ($acceso) => Route::has($acceso['ruta'])));
    }
}
