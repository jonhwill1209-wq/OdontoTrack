<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEvolucionClinicaRequest;
use App\Http\Requests\StoreTratamientoRequest;
use App\Http\Requests\UpdateAnamnesisRequest;
use App\Http\Requests\UpdateTratamientoRequest;
use App\Models\Paciente;
use App\Models\Odontologo;
use App\Models\Tratamiento;
use App\Services\ExpedienteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpedienteController extends Controller
{
    public function __construct(
        private readonly ExpedienteService $expedienteService
    ) {
    }

    public function show(Request $request, Paciente $paciente): View
    {
        $this->odontologoAutenticado($request);

        $expediente = $this->expedienteService
            ->obtenerOCrearPorPaciente($paciente);

        $expediente->load([
            'anamnesis',
            'tratamientos.evoluciones.odontologo.usuario',
        ]);

        return view('expedientes.show', compact('paciente', 'expediente'));
    }

    public function actualizarAnamnesis(
        UpdateAnamnesisRequest $request,
        Paciente $paciente
    ): RedirectResponse {
        $expediente = $this->expedienteService
            ->obtenerOCrearPorPaciente($paciente);

        $this->expedienteService->actualizarAnamnesis(
            $expediente,
            $request->validated()
        );

        return redirect()
            ->route('expedientes.show', $paciente)
            ->with('exito', 'Anamnesis actualizada correctamente.');
    }

    public function guardarTratamiento(
        StoreTratamientoRequest $request,
        Paciente $paciente
    ): RedirectResponse {
        $expediente = $this->expedienteService
            ->obtenerOCrearPorPaciente($paciente);

        $this->expedienteService->crearTratamiento(
            $expediente,
            $request->validated()
        );

        return redirect()
            ->route('expedientes.show', $paciente)
            ->with('exito', 'Tratamiento registrado correctamente.');
    }

    public function actualizarTratamiento(
        UpdateTratamientoRequest $request,
        Paciente $paciente,
        Tratamiento $tratamiento
    ): RedirectResponse {
        $expediente = $this->expedienteService
            ->obtenerOCrearPorPaciente($paciente);

        $this->asegurarTratamientoDelExpediente($tratamiento, $expediente->id_expediente);

        $this->expedienteService->actualizarTratamiento(
            $tratamiento,
            $request->validated()
        );

        return redirect()
            ->route('expedientes.show', $paciente)
            ->with('exito', 'Tratamiento actualizado correctamente.');
    }

    public function agregarEvolucion(
        StoreEvolucionClinicaRequest $request,
        Paciente $paciente,
        Tratamiento $tratamiento
    ): RedirectResponse {
        $odontologo = $this->odontologoAutenticado($request);
        $expediente = $this->expedienteService
            ->obtenerOCrearPorPaciente($paciente);

        $this->asegurarTratamientoDelExpediente($tratamiento, $expediente->id_expediente);

        $this->expedienteService->agregarEvolucion(
            $tratamiento,
            $odontologo->id_odontologo,
            $request->validated()['observacion']
        );

        return redirect()
            ->route('expedientes.show', $paciente)
            ->with('exito', 'Nota de evolución registrada correctamente.');
    }

    private function odontologoAutenticado(Request $request): Odontologo
    {
        $odontologo = $request->user()?->odontologo;

        abort_unless(
            $request->user()?->rol === 'ODONTOLOGO' && $odontologo !== null,
            403,
            'Necesitas un perfil de odontólogo para consultar el expediente.'
        );

        return $odontologo;
    }

    private function asegurarTratamientoDelExpediente(
        Tratamiento $tratamiento,
        int $idExpediente
    ): void {
        abort_unless(
            (int) $tratamiento->id_expediente === $idExpediente,
            404
        );
    }
}
