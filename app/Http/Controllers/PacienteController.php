<?php

namespace App\Http\Controllers;

use App\Exports\PacientesExport;
use App\Http\Requests\StorePacienteRequest;
use App\Http\Requests\UpdatePacienteRequest;
use App\Models\Paciente;
use App\Services\PacienteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PacienteController extends Controller
{
    public function __construct(
        private readonly PacienteService $pacienteService
    ) {
    }

    public function index(Request $request): View
    {
        $filtros = $this->validarFiltros($request);

        $buscar = $filtros['buscar'] ?? '';
        $estado = $filtros['estado'] ?? '1';

        $pacientes = $this->pacienteService->listar($buscar, $estado);

        return view('pacientes.index', compact(
            'pacientes',
            'buscar',
            'estado'
        ));
    }

    public function exportar(Request $request): BinaryFileResponse
    {
        $this->verificarRecepcionista();

        $filtros = $this->validarFiltros($request);

        $buscar = $filtros['buscar'] ?? '';
        $estado = $filtros['estado'] ?? '1';

        $exportacion = new PacientesExport(
            $this->pacienteService,
            $buscar,
            $estado
        );

        $nombreArchivo = 'pacientes_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download($exportacion, $nombreArchivo);
    }

    public function create(): View
    {
        $this->verificarRecepcionista();

        return view('pacientes.create');
    }

    public function store(StorePacienteRequest $request): RedirectResponse
    {
        $paciente = $this->pacienteService->registrar(
            $request->validated()
        );

        return redirect()
            ->route('pacientes.show', $paciente)
            ->with('exito', 'Paciente registrado correctamente.');
    }

    public function show(Paciente $paciente): View
    {
        return view('pacientes.show', compact('paciente'));
    }

    public function edit(Paciente $paciente): View
    {
        $this->verificarRecepcionista();

        return view('pacientes.edit', compact('paciente'));
    }

    public function update(
        UpdatePacienteRequest $request,
        Paciente $paciente
    ): RedirectResponse {
        $this->pacienteService->actualizar(
            $paciente,
            $request->validated()
        );

        return redirect()
            ->route('pacientes.show', $paciente)
            ->with('exito', 'Paciente actualizado correctamente.');
    }

    public function destroy(Paciente $paciente): RedirectResponse
    {
        $this->verificarRecepcionista();

        $this->pacienteService->desactivar($paciente);

        return redirect()
            ->route('pacientes.index')
            ->with('exito', 'Paciente desactivado correctamente.');
    }

    public function activar(Paciente $paciente): RedirectResponse
    {
        $this->verificarRecepcionista();

        $this->pacienteService->activar($paciente);

        return redirect()
            ->route('pacientes.show', $paciente)
            ->with('exito', 'Paciente reactivado correctamente.');
    }

    private function validarFiltros(Request $request): array
    {
        return $request->validate([
            'buscar' => ['nullable', 'string', 'max:100'],
            'estado' => ['nullable', 'in:0,1,todos'],
        ], [
            'buscar.string' => 'La búsqueda debe ser texto.',
            'buscar.max' => 'La búsqueda no debe superar 100 caracteres.',
            'estado.in' => 'Selecciona un estado válido.',
        ]);
    }

    private function verificarRecepcionista(): void
    {
        abort_unless(
            auth()->user()?->esRecepcionista() === true,
            403,
            'No tienes permiso para realizar esta acción.'
        );
    }
}
