<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOdontogramaRequest;
use App\Models\Paciente;
use App\Services\ExpedienteService;
use App\Services\OdontogramaService;
use App\Support\Odontograma;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OdontogramaController extends Controller
{
    public function __construct(
        private readonly ExpedienteService $expedienteService,
        private readonly OdontogramaService $odontogramaService
    ) {
    }

    public function show(
        Request $request,
        Paciente $paciente
    ): View {
        abort_unless(
            $request->user()?->rol === 'ODONTOLOGO'
            && $request->user()?->odontologo !== null,
            403,
            'Necesitas un perfil de odontólogo para consultar el odontograma.'
        );

        $expediente = $this->expedienteService
            ->obtenerOCrearPorPaciente($paciente);

        $registros = $this->odontogramaService
            ->listar($expediente);

        $superiores = Odontograma::SUPERIORES;
        $inferiores = Odontograma::INFERIORES;
        $caras = Odontograma::CARAS;
        $condiciones = Odontograma::CONDICIONES;

        return view('odontograma.show', compact(
            'paciente',
            'expediente',
            'registros',
            'superiores',
            'inferiores',
            'caras',
            'condiciones'
        ));
    }

    public function store(
        StoreOdontogramaRequest $request,
        Paciente $paciente
    ): RedirectResponse {
        $this->odontogramaService->registrar(
            $paciente,
            $request->user()->odontologo,
            $request->validated()
        );

        return redirect()
            ->route('odontograma.show', $paciente)
            ->with('exito', 'Registro del odontograma guardado correctamente.');
    }
}
