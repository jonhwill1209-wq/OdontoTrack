<?php

namespace App\Http\Controllers;

use App\Exceptions\OperacionNoPermitidaException;
use App\Http\Requests\StoreConsultorioRequest;
use App\Http\Requests\UpdateConsultorioRequest;
use App\Models\Consultorio;
use App\Services\ConsultorioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ConsultorioController extends Controller
{
    public function __construct(
        private readonly ConsultorioService $consultorioService
    ) {
    }

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Consultorio::class);

        $filtros = $request->validate([
            'buscar' => ['nullable', 'string', 'max:80'],
            'estado' => ['nullable', 'in:0,1,todos'],
        ], [
            'buscar.string' => 'La búsqueda debe ser texto.',
            'buscar.max' => 'La búsqueda no debe superar 80 caracteres.',
            'estado.in' => 'Selecciona un estado válido.',
        ]);

        return view('consultorios.index', [
            'consultorios' => $this->consultorioService->listar($filtros),
            'totales' => $this->consultorioService->totales(),
            'buscar' => $filtros['buscar'] ?? '',
            'estado' => $filtros['estado'] ?? '1',
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Consultorio::class);

        return view('consultorios.create', [
            'consultorio' => new Consultorio(),
        ]);
    }

    public function store(StoreConsultorioRequest $request): RedirectResponse
    {
        $consultorio = $this->consultorioService->crear($request->validated());

        return redirect()
            ->route('consultorios.show', $consultorio)
            ->with('exito', 'Consultorio registrado correctamente.');
    }

    public function show(Consultorio $consultorio): View
    {
        Gate::authorize('view', $consultorio);

        return view('consultorios.show', [
            'consultorio' => $consultorio->loadCount(['citas', 'citasPendientes']),
            'proximas' => $this->consultorioService->proximasCitas($consultorio),
        ]);
    }

    public function edit(Consultorio $consultorio): View
    {
        Gate::authorize('update', $consultorio);

        return view('consultorios.edit', compact('consultorio'));
    }

    public function update(UpdateConsultorioRequest $request, Consultorio $consultorio): RedirectResponse
    {
        $this->consultorioService->actualizar($consultorio, $request->validated());

        return redirect()
            ->route('consultorios.show', $consultorio)
            ->with('exito', 'Consultorio actualizado correctamente.');
    }

    public function desactivar(Consultorio $consultorio): RedirectResponse
    {
        Gate::authorize('desactivar', $consultorio);

        $pendientes = $this->consultorioService->desactivar($consultorio);

        $respuesta = redirect()
            ->route('consultorios.show', $consultorio)
            ->with('exito', 'Consultorio desactivado. Ya no se ofrecerá para nuevas citas.');

        if ($pendientes > 0) {
            $respuesta->with('advertencia', "Tiene {$pendientes} cita(s) programada(s). Muévelas a otro consultorio desde la agenda.");
        }

        return $respuesta;
    }

    public function activar(Consultorio $consultorio): RedirectResponse
    {
        Gate::authorize('activar', $consultorio);

        $this->consultorioService->activar($consultorio);

        return redirect()
            ->route('consultorios.show', $consultorio)
            ->with('exito', 'Consultorio reactivado correctamente.');
    }

    public function destroy(Consultorio $consultorio): RedirectResponse
    {
        Gate::authorize('delete', $consultorio);

        try {
            $this->consultorioService->eliminar($consultorio);
        } catch (OperacionNoPermitidaException $e) {
            return redirect()
                ->route('consultorios.show', $consultorio)
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('consultorios.index')
            ->with('exito', "Consultorio \"{$consultorio->nombre}\" eliminado definitivamente.");
    }
}
