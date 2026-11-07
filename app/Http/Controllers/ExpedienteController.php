<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClinicoRequest;
use App\Models\EvolucionClinica;
use App\Models\Paciente;
use App\Models\Tratamiento;
use App\Services\ExpedienteService;
use Illuminate\Http\Request;

class ExpedienteController extends Controller
{
    public function show(Request $request, Paciente $paciente, ExpedienteService $service)
    {
        abort_unless($request->user()?->odontologo !== null, 403);
        $expediente = $service->obtenerOCrearPorPaciente($paciente);
        $expediente->load(['anamnesis', 'tratamientos.evoluciones.odontologo.usuario', 'archivos.odontologo.usuario']);

        return view('expedientes.show', compact('paciente', 'expediente'));
    }

    public function anamnesis(ClinicoRequest $request, Paciente $paciente, ExpedienteService $service)
    {
        $service->obtenerOCrearPorPaciente($paciente)->anamnesis()->updateOrCreate([], array_merge($request->validated(), ['actualizado_en' => now()]));

        return back()->with('exito', 'Anamnesis guardada.');
    }

    public function tratamiento(ClinicoRequest $request, Paciente $paciente, ExpedienteService $service, ?Tratamiento $tratamiento = null)
    {
        $expediente = $service->obtenerOCrearPorPaciente($paciente);
        if ($tratamiento) {
            abort_unless($tratamiento->id_expediente === $expediente->id_expediente, 404);
            $tratamiento->update($request->validated());
        } else {
            $expediente->tratamientos()->create($request->validated());
        }

        return back()->with('exito', 'Tratamiento guardado.');
    }

    public function evolucion(ClinicoRequest $request, Paciente $paciente, Tratamiento $tratamiento, ?EvolucionClinica $evolucion = null)
    {
        abort_unless($tratamiento->expediente->id_paciente === $paciente->id_paciente, 404);
        if ($evolucion) {
            abort_unless($evolucion->id_tratamiento === $tratamiento->id_tratamiento, 404);
            abort_unless($evolucion->id_odontologo === $request->user()->odontologo->id_odontologo, 403);
            $evolucion->update($request->validated());
        } else {
            $tratamiento->evoluciones()->create($request->validated() + ['id_odontologo' => $request->user()->odontologo->id_odontologo]);
        }

        return back()->with('exito','Evolución guardada.');
    }
}
