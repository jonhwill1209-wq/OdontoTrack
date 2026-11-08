<?php

namespace App\Http\Controllers;

use App\Models\Expediente;
use App\Models\Odontologo;
use App\Models\Tratamiento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TratamientoController extends Controller
{
    public function create(Expediente $expediente): View
    {
        return view('tratamientos.create', compact('expediente'));
    }

    public function store(Request $request, Expediente $expediente): RedirectResponse
    {
        $datos = $this->validar($request);
        $tratamiento = $expediente->tratamientos()->create($datos);

        return redirect()
            ->route('tratamientos.show', $tratamiento)
            ->with('exito', 'Tratamiento registrado correctamente.');
    }

    public function show(Tratamiento $tratamiento): View
    {
        $tratamiento->load([
            'expediente.paciente',
            'evoluciones.odontologo.usuario',
        ]);

        return view('tratamientos.show', compact('tratamiento'));
    }

    public function edit(Tratamiento $tratamiento): View
    {
        return view('tratamientos.edit', compact('tratamiento'));
    }

    public function update(Request $request, Tratamiento $tratamiento): RedirectResponse
    {
        $tratamiento->update($this->validar($request));

        return redirect()
            ->route('tratamientos.show', $tratamiento)
            ->with('exito', 'Tratamiento actualizado correctamente.');
    }

    public function destroy(Tratamiento $tratamiento): RedirectResponse
    {
        if ($tratamiento->evoluciones()->exists()) {
            return back()->withErrors([
                'tratamiento' => 'No se puede eliminar un tratamiento con evoluciones clínicas; cambia su estado a Finalizado.',
            ]);
        }

        $paciente = $tratamiento->expediente->paciente;
        $tratamiento->delete();

        return redirect()
            ->route('expedientes.show', $paciente)
            ->with('exito', 'Tratamiento eliminado correctamente.');
    }

    public function registrarEvolucion(Request $request, Tratamiento $tratamiento): RedirectResponse
    {
        $datos = $request->validate([
            'observacion' => ['required', 'string', 'min:3', 'max:5000'],
        ], [
            'observacion.required' => 'Escribe la observación clínica.',
            'observacion.min' => 'La observación debe tener al menos :min caracteres.',
            'observacion.max' => 'La observación no debe superar :max caracteres.',
        ]);
        $odontologo = $request->user()->odontologo;

        abort_unless($odontologo instanceof Odontologo, 403, 'Tu usuario no tiene un perfil de odontólogo.');

        $tratamiento->evoluciones()->create([
            'id_odontologo' => $odontologo->id_odontologo,
            'observacion' => $datos['observacion'],
        ]);

        return back()->with('exito', 'Evolución clínica registrada correctamente.');
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'diagnostico' => ['required', 'string', 'min:3', 'max:5000'],
            'plan' => ['required', 'string', 'min:3', 'max:5000'],
            'estado' => ['required', Rule::in([
                Tratamiento::PLANIFICADO,
                Tratamiento::EN_CURSO,
                Tratamiento::FINALIZADO,
            ])],
            'fecha_inicio' => ['required', 'date', 'before_or_equal:today'],
        ], [
            'diagnostico.required' => 'Ingresa el diagnóstico.',
            'diagnostico.min' => 'El diagnóstico debe tener al menos :min caracteres.',
            'diagnostico.max' => 'El diagnóstico no debe superar :max caracteres.',
            'plan.required' => 'Ingresa el plan de tratamiento.',
            'plan.min' => 'El plan debe tener al menos :min caracteres.',
            'plan.max' => 'El plan no debe superar :max caracteres.',
            'estado.required' => 'Selecciona el estado del tratamiento.',
            'estado.in' => 'Selecciona un estado válido.',
            'fecha_inicio.required' => 'Indica la fecha de inicio.',
            'fecha_inicio.date' => 'La fecha de inicio no es válida.',
            'fecha_inicio.before_or_equal' => 'La fecha de inicio no puede ser futura.',
        ]);
    }
}
