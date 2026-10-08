<?php

namespace Database\Seeders;

use App\Models\Consultorio;
use App\Models\Odontologo;
use App\Models\Paciente;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Datos de prueba. Se puede ejecutar varias veces sin duplicar registros.
 * Contraseña de todas las cuentas de prueba: Odonto2026!
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->usuarios();
        $this->consultorios();
        $this->pacientes();
    }

    private function usuarios(): void
    {
        Usuario::updateOrCreate(
            ['correo' => 'recepcion@odontotrack.test'],
            [
                'nombre' => 'Recepción Sonrisas',
                'clave_hash' => Hash::make('Odonto2026!'),
                'rol' => 'RECEPCIONISTA',
                'activo' => true,
            ]
        );

        $odontologos = [
            ['Dra. Ana Martínez', 'doctor1@odontotrack.test', 'JVPO-1001'],
            ['Dr. Luis Hernández', 'doctor2@odontotrack.test', 'JVPO-1002'],
        ];

        foreach ($odontologos as [$nombre, $correo, $colegiado]) {
            $usuario = Usuario::updateOrCreate(
                ['correo' => $correo],
                [
                    'nombre' => $nombre,
                    'clave_hash' => Hash::make('Odonto2026!'),
                    'rol' => 'ODONTOLOGO',
                    'activo' => true,
                ]
            );

            Odontologo::updateOrCreate(
                ['id_usuario' => $usuario->id_usuario],
                ['numero_colegiado' => $colegiado]
            );
        }
    }

    private function consultorios(): void
    {
        foreach (['Consultorio 1', 'Consultorio 2', 'Consultorio 3'] as $nombre) {
            Consultorio::firstOrCreate(['nombre' => $nombre], ['activo' => true]);
        }
    }

    private function pacientes(): void
    {
        $pacientes = [
            ['Carlos Alberto', 'Rivera Mejía', '1990-03-14', '7012-3456', 'carlos.rivera@example.com'],
            ['María José', 'Flores Portillo', '1985-07-22', '7023-4567', 'maria.flores@example.com'],
            ['José Roberto', 'Campos Aguilar', '1978-11-05', '7034-5678', null],
            ['Ana Lucía', 'Menjívar Ortiz', '2001-01-30', '7045-6789', 'ana.menjivar@example.com'],
            ['Pedro Antonio', 'Quintanilla Cruz', '1969-09-18', '7056-7890', null],
            ['Gabriela Esther', 'Serrano Lara', '1995-05-09', '7067-8901', 'gabriela.serrano@example.com'],
            ['Miguel Ángel', 'Henríquez Díaz', '1988-12-02', '7078-9012', null],
            ['Sofía Alejandra', 'Pineda Rosales', '2005-04-27', '7089-0123', 'sofia.pineda@example.com'],
            ['Ricardo Ernesto', 'Alvarado Peña', '1974-06-11', '7090-1234', null],
            ['Daniela Beatriz', 'Guardado Romero', '1999-08-20', '7101-2345', 'daniela.guardado@example.com'],
        ];

        foreach ($pacientes as [$nombres, $apellidos, $nacimiento, $telefono, $correo]) {
            Paciente::firstOrCreate(
                ['nombres' => $nombres, 'apellidos' => $apellidos],
                [
                    'fecha_nacimiento' => $nacimiento,
                    'telefono' => $telefono,
                    'correo' => $correo,
                    'activo' => true,
                ]
            );
        }
    }
}
