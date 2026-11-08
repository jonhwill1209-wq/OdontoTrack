<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOdontologoRequest;
use App\Http\Requests\UpdateOdontologoRequest;
use App\Models\Odontologo;
use App\Services\OdontologoService;
use App\Services\UsuarioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OdontologoController extends Controller
{
    public function __construct(
        private readonly OdontologoService $odontologoService,
        private readonly UsuarioService $usuarioService
    ) {
    }

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Odontologo::class);

        $filtros = $request->validate([
            'buscar' => ['nullable', 'string', 'max:100'],
            'estado' => ['nullable', 'in:0,1,todos'],
        ], [
            'buscar.string' => 'La búsqueda debe ser texto.',
            'buscar.max' => 'La búsqueda no debe superar 100 caracteres.',
            'estado.in' => 'Selecciona un estado válido.',
        ]);

        return view('odontologos.index', [
            'odontologos' => $this->odontologoService->listar($filtros),
            'totales' => $this->odontologoService->totales(),
            'buscar' => $filtros['buscar'] ?? '',
            'estado' => $filtros['estado'] ?? '1',
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Odontologo::class);

        return view('odontologos.create', [
            'odontologo' => new Odontologo(),
        ]);
    }

    public function store(StoreOdontologoRequest $request): RedirectResponse
    {
        $odontologo = $this->odontologoService->crear($request->validated());

        return redirect()
            ->route('odontologos.show', $odontologo)
            ->with('exito', 'Odontólogo registrado. Ya puede iniciar sesión con su correo.');
    }

    public function show(Odontologo $odontologo): View
    {
        Gate::authorize('view', $odontologo);

        return view('odontologos.show', [
            'odontologo' => $odontologo->load('usuario'),
            'resumen' => $this->odontologoService->resumenCitas($odontologo),
            'proximas' => $this->odontologoService->proximasCitas($odontologo),
        ]);
    }

    public function edit(Odontologo $odontologo): View
    {
        Gate::authorize('update', $odontologo);

        return view('odontologos.edit', [
            'odontologo' => $odontologo->load('usuario'),
        ]);
    }

    public function update(UpdateOdontologoRequest $request, Odontologo $odontologo): RedirectResponse
    {
        $this->odontologoService->actualizar($odontologo, $request->validated());

        return redirect()
            ->route('odontologos.show', $odontologo)
            ->with('exito', 'Datos del odontólogo actualizados correctamente.');
    }

    public function desactivar(Odontologo $odontologo): RedirectResponse
    {
        Gate::authorize('desactivar', $odontologo);

        $pendientes = $this->usuarioService->desactivar($odontologo->usuario);

        $respuesta = redirect()
            ->route('odontologos.show', $odontologo)
            ->with('exito', 'Odontólogo desactivado. Ya no puede iniciar sesión.');

        if ($pendientes > 0) {
            $respuesta->with('advertencia', "Tiene {$pendientes} cita(s) programada(s). Reasígnalas o cancélalas desde la agenda.");
        }

        return $respuesta;
    }

    public function activar(Odontologo $odontologo): RedirectResponse
    {
        Gate::authorize('activar', $odontologo);

        $this->usuarioService->activar($odontologo->usuario);

        return redirect()
            ->route('odontologos.show', $odontologo)
            ->with('exito', 'Odontólogo reactivado correctamente.');
    }
}
