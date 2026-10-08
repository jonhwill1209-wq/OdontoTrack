<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registro_odontograma', function (Blueprint $table) {
            $table->id('id_registro');
            $table->foreignId('id_expediente')->constrained('expediente', 'id_expediente');
            $table->foreignId('id_odontologo')->constrained('odontologo', 'id_odontologo');
            $table->string('pieza', 4);
            $table->string('cara', 20);
            $table->string('condicion', 80);
            $table->dateTime('registrado_en')->useCurrent();

            $table->index(['id_expediente', 'pieza']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registro_odontograma');
    }
};
