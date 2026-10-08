<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cita', function (Blueprint $table) {
            $table->id('id_cita');
            $table->foreignId('id_paciente')->constrained('paciente', 'id_paciente');
            $table->foreignId('id_odontologo')->constrained('odontologo', 'id_odontologo');
            $table->foreignId('id_consultorio')->constrained('consultorio', 'id_consultorio');
            $table->dateTime('inicio');
            $table->dateTime('fin');
            $table->string('estado', 20)->default('PROGRAMADA'); // PROGRAMADA | CANCELADA | REALIZADA
            $table->string('motivo', 250)->nullable();

            // Aceleran la validación de solapamientos
            $table->index(['id_odontologo', 'inicio', 'fin']);
            $table->index(['id_consultorio', 'inicio', 'fin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cita');
    }
};
