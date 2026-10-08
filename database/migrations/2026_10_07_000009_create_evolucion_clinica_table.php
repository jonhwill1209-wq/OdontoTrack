<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evolucion_clinica', function (Blueprint $table) {
            $table->id('id_evolucion');
            $table->foreignId('id_tratamiento')->constrained('tratamiento', 'id_tratamiento');
            $table->foreignId('id_odontologo')->constrained('odontologo', 'id_odontologo');
            $table->dateTime('fecha_registro')->useCurrent();
            $table->text('observacion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evolucion_clinica');
    }
};
