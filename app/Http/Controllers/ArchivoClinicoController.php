<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClinicoRequest;
use App\Models\ArchivoClinico;
use App\Models\Paciente;
use App\Services\ExpedienteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArchivoClinicoController extends Controller
{
    public function store(ClinicoRequest $request, Paciente $paciente, ExpedienteService $service)
    {
        $expediente = $service->obtenerOCrearPorPaciente($paciente);
        $file = $request->file('archivo');
        $ruta = $file->store('clinicos/'.$expediente->id_expediente, 'local');
        abort_unless($ruta, 500, 'No se pudo guardar el archivo.');
        try {
            $expediente->archivos()->create(['id_odontologo' => $request->user()->odontologo->id_odontologo, 'nombre_original' => mb_substr(basename($file->getClientOriginalName()), 0, 255), 'ruta' => $ruta, 'mime' => $file->getMimeType(), 'tamano' => $file->getSize(), 'descripcion' => $request->validated('descripcion')]);
        } catch (\Throwable $error) {
            Storage::disk('local')->delete($ruta);
            throw $error;
        }

        return back()->with('exito', 'Archivo clínico guardado.');
    }

    public function show(Request $request, Paciente $paciente, ArchivoClinico $archivo)
    {
        abort_unless($request->user()?->odontologo !== null, 403);
        abort_unless($archivo->expediente->id_paciente === $paciente->id_paciente, 404);
        abort_unless(Storage::disk('local')->exists($archivo->ruta), 404);
        $headers = ['Content-Type' => $archivo->mime, 'X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'private, no-store', 'Content-Security-Policy' => "sandbox; default-src 'none'"];
        if ($request->boolean('descargar')) {
            return Storage::disk('local')->download($archivo->ruta, $archivo->nombre_original, $headers);
        }

        return Storage::disk('local')->response($archivo->ruta, $archivo->nombre_original, $headers, 'inline');
    }
}
