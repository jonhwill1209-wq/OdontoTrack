<?php

namespace App\Http\Controllers;

use App\Models\Expediente;
use App\Models\ExpedienteArchivo;
use App\Models\Paciente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExpedienteController extends Controller
{
    public function index(Request $request): View
    {
        $filtros = $request->validate([
            'buscar' => ['nullable', 'string', 'max:100'],
        ]);
        $buscar = trim($filtros['buscar'] ?? '');

        $pacientes = Paciente::query()
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where('nombres', 'like', "%{$buscar}%")
                    ->orWhere('apellidos', 'like', "%{$buscar}%");
            })
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->paginate(15)
            ->withQueryString();

        return view('expedientes.index', compact('pacientes', 'buscar'));
    }

    public function show(Paciente $paciente): View
    {
        $expediente = Expediente::firstOrCreate([
            'id_paciente' => $paciente->id_paciente,
        ]);
        $expediente->load([
            'anamnesis',
            'tratamientos.evoluciones.odontologo.usuario',
            'archivos',
        ]);

        return view('expedientes.show', compact('paciente', 'expediente'));
    }

    public function actualizarAnamnesis(Request $request, Expediente $expediente): RedirectResponse
    {
        $datos = $request->validate([
            'alergias' => ['nullable', 'string', 'max:10000'],
            'antecedentes' => ['nullable', 'string', 'max:10000'],
            'medicamentos' => ['nullable', 'string', 'max:10000'],
        ], [
            'string' => 'El campo :attribute debe ser texto.',
            'max' => 'El campo :attribute no debe superar :max caracteres.',
        ]);

        $expediente->anamnesis()->updateOrCreate(
            ['id_expediente' => $expediente->id_expediente],
            $datos
        );

        return back()->with('exito', 'Anamnesis guardada correctamente.');
    }

    public function subirArchivo(Request $request, Expediente $expediente): RedirectResponse
    {
        $datos = $request->validate([
            'archivo' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ], [
            'archivo.required' => 'Selecciona un documento o una imagen.',
            'archivo.file' => 'El archivo seleccionado no es válido.',
            'archivo.mimes' => 'Solo se permiten archivos PDF, JPG o PNG.',
            'archivo.max' => 'El archivo no debe superar 10 MB.',
        ]);

        $archivo = $datos['archivo'];
        $nombreAlmacenado = Str::uuid().'.'.$archivo->extension();
        $ruta = $archivo->storeAs(
            'expedientes/'.$expediente->id_expediente,
            $nombreAlmacenado,
            'local'
        );

        if (! $ruta) {
            throw ValidationException::withMessages([
                'archivo' => 'No fue posible almacenar el archivo. Intenta nuevamente.',
            ]);
        }

        try {
            ExpedienteArchivo::create([
                'id_expediente' => $expediente->id_expediente,
                'nombre_original' => mb_substr($archivo->getClientOriginalName(), 0, 255),
                'ruta' => $ruta,
                'mime_type' => $archivo->getMimeType() ?: 'application/octet-stream',
                'tamano' => $archivo->getSize(),
            ]);
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($ruta);
            throw $exception;
        }

        return back()->with('exito', 'Archivo cargado correctamente.');
    }

    public function descargarArchivo(ExpedienteArchivo $archivo): StreamedResponse
    {
        abort_unless(Storage::disk('local')->exists($archivo->ruta), 404, 'El archivo ya no está disponible.');

        return Storage::disk('local')->download($archivo->ruta, $archivo->nombre_original);
    }

    public function eliminarArchivo(ExpedienteArchivo $archivo): RedirectResponse
    {
        $expediente = $archivo->expediente;
        $disco = Storage::disk('local');

        abort_unless($disco->exists($archivo->ruta), 404, 'El archivo ya no está disponible.');

        if (! $disco->delete($archivo->ruta)) {
            throw ValidationException::withMessages([
                'archivo' => 'No fue posible eliminar el archivo. Intenta nuevamente.',
            ]);
        }

        $archivo->delete();

        return redirect()
            ->route('expedientes.show', $expediente->paciente)
            ->with('exito', 'Archivo eliminado correctamente.');
    }
}
