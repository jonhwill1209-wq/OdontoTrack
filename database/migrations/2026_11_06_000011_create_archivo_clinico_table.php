<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archivo_clinico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_expediente')->constrained('expediente', 'id_expediente');
            $table->foreignId('id_odontologo')->constrained('odontologo', 'id_odontologo');
            $table->string('nombre_original');
            $table->string('ruta')->unique();
            $table->string('mime', 100);
            $table->unsignedBigInteger('tamano');
            $table->string('descripcion', 500);
            $table->dateTime('fecha')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archivo_clinico');
    }
};
