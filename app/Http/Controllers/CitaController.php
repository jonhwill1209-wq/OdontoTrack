<?php

namespace App\Http\Controllers;

use App\Exceptions\HorarioOcupadoException;
use App\Http\Requests\StoreCitaRequest;
use App\Models\Cita;
use App\Models\Consultorio;
use App\Models\Odontologo;
use App\Models\Paciente;
use App\Services\CitaService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class CitaController extends Controller
{
    public function __construct(private CitaService $citaService)
    {
    }

    // Pantalla del calendario con filtros
    public function index(): View
    {
        return view('citas.index', [
            'odontologos' => Odontologo::with('usuario')->get(),
            'consultorios' => Consultorio::activos()->orderBy('nombre')->get(),
        ]);
    }

    // JSON que consume FullCalendar. Filtros opcionales: id_odontologo, id_consultorio
    public function eventos(Request $request): JsonResponse
    {
        $zona = config('app.timezone');

        $citas = Cita::with(['paciente', 'odontologo.usuario', 'consultorio'])
            ->when($request->filled('id_odontologo'),
                fn ($q) => $q->where('id_odontologo', $request->integer('id_odontologo')))
            ->when($request->filled('id_consultorio'),
                fn ($q) => $q->where('id_consultorio', $request->integer('id_consultorio')))
            ->when($request->filled('start') && $request->filled('end'), function ($q) use ($request, $zona) {
                $q->where('inicio', '<', Carbon::parse($request->query('end'))->setTimezone($zona))
                    ->where('fin', '>', Carbon::parse($request->query('start'))->setTimezone($zona));
            })
            ->get();

        $eventos = $citas->map(function (Cita $cita) {
            $programada = $cita->estado === Cita::PROGRAMADA;

            return [
                'id' => $cita->id_cita,
                'title' => $cita->paciente->nombres . ' ' . $cita->paciente->apellidos,
                'start' => $cita->inicio->toIso8601String(),
                'end' => $cita->fin->toIso8601String(),
                'editable' => $programada,
                'color' => match ($cita->estado) {
                    Cita::CANCELADA => '#adb5bd',
                    Cita::REALIZADA => '#198754',
                    default => '#0d6efd',
                },
                'extendedProps' => [
                    'estado' => $cita->estado,
                    'odontologo' => $cita->odontologo->usuario->nombre ?? '',
                    'consultorio' => $cita->consultorio->nombre,
                    'motivo' => $cita->motivo,
                    'url_editar' => $programada ? route('citas.edit', $cita) : null,
                    'url_mover' => $programada ? route('citas.mover', $cita) : null,
                    'url_realizar' => $programada ? route('citas.realizar', $cita) : null,
                    'url_cancelar' => $programada ? route('citas.cancelar', $cita) : null,
                ],
            ];
        });

        return response()->json($eventos);
    }

    public function create(Request $request): View
    {
        return view('citas.create', $this->datosFormulario(new Cita()));
    }

    public function store(StoreCitaRequest $request): RedirectResponse
    {
        try {
            $this->citaService->crear($request->validated());
        } catch (HorarioOcupadoException $e) {
            return back()->withInput()->withErrors(['inicio' => $e->getMessage()]);
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['fin' => $e->getMessage()]);
        }

        return redirect()->route('citas.index')->with('exito', 'Cita agendada correctamente.');
    }

    public function edit(Cita $cita): View|RedirectResponse
    {
        if ($bloqueo = $this->bloquearSiNoProgramada($cita)) {
            return $bloqueo;
        }

        return view('citas.edit', $this->datosFormulario($cita));
    }

    public function update(StoreCitaRequest $request, Cita $cita): RedirectResponse
    {
        if ($bloqueo = $this->bloquearSiNoProgramada($cita)) {
            return $bloqueo;
        }

        try {
            $this->citaService->actualizar($cita, $request->validated());
        } catch (HorarioOcupadoException $e) {
            return back()->withInput()->withErrors(['inicio' => $e->getMessage()]);
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['fin' => $e->getMessage()]);
        }

        return redirect()->route('citas.index')->with('exito', 'Cita actualizada correctamente.');
    }

    // Cancelar NO borra: solo cambia el estado a CANCELADA
    public function cancelar(Cita $cita): RedirectResponse
    {
        if ($bloqueo = $this->bloquearSiNoProgramada($cita)) {
            return $bloqueo;
        }

        $this->citaService->cancelar($cita);

        return redirect()->route('citas.index')->with('exito', 'Cita cancelada.');
    }

    // Marca una cita programada como realizada
    public function realizar(Cita $cita): RedirectResponse
    {
        if ($bloqueo = $this->bloquearSiNoProgramada($cita)) {
            return $bloqueo;
        }

        $this->citaService->marcarRealizada($cita);

        return redirect()->route('citas.index')->with('exito', 'Cita marcada como realizada.');
    }

    // Arrastrar o redimensionar en el calendario. Responde JSON para que FullCalendar acepte o revierta el cambio
    public function mover(Request $request, Cita $cita): JsonResponse
    {
        if ($cita->estado !== Cita::PROGRAMADA) {
            return response()->json(['mensaje' => 'Solo se pueden mover citas programadas.'], 422);
        }

        $datos = $request->validate([
            'inicio' => ['required', 'date'],
            'fin' => ['required', 'date', 'after:inicio'],
        ], [
            'inicio.required' => 'Indica la fecha y hora de inicio.',
            'fin.required' => 'Indica la fecha y hora de fin.',
            'fin.after' => 'La hora de fin debe ser posterior a la de inicio.',
        ]);

        try {
            $this->citaService->reprogramar(
                $cita,
                Carbon::parse($datos['inicio'])->setTimezone(config('app.timezone')),
                Carbon::parse($datos['fin'])->setTimezone(config('app.timezone')),
            );
        } catch (HorarioOcupadoException|InvalidArgumentException $e) {
            return response()->json(['mensaje' => $e->getMessage()], 422);
        }

        return response()->json(['mensaje' => 'Cita reprogramada correctamente.']);
    }

    private function bloquearSiNoProgramada(Cita $cita): ?RedirectResponse
    {
        if ($cita->estado !== Cita::PROGRAMADA) {
            return redirect()->route('citas.index')
                ->withErrors(['estado' => 'Solo se pueden modificar o cancelar citas programadas.']);
        }

        return null;
    }

    private function datosFormulario(Cita $cita): array
    {
        return [
            'cita' => $cita,
            'pacientes' => Paciente::activos()->orderBy('apellidos')->orderBy('nombres')->get(),
            'odontologos' => Odontologo::with('usuario')->get(),
            'consultorios' => Consultorio::activos()->orderBy('nombre')->get(),
            // Si quien agenda es odontólogo, se preselecciona él mismo
            'odontologoPorDefecto' => auth()->user()->odontologo?->id_odontologo,
        ];
    }
}
