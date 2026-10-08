<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tratamiento', function (Blueprint $table) {
            $table->id('id_tratamiento');
            $table->foreignId('id_expediente')->constrained('expediente', 'id_expediente');
            $table->text('diagnostico');
            $table->text('plan');
            $table->string('estado', 20)->default('PLANIFICADO'); // PLANIFICADO | EN_CURSO | FINALIZADO
            $table->date('fecha_inicio');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tratamiento');
    }
};
