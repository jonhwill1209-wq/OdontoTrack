<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paciente', function (Blueprint $table) {
            $table->id('id_paciente');
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->date('fecha_nacimiento')->nullable();
            $table->string('telefono', 25)->nullable();
            $table->string('correo', 150)->nullable();
            $table->boolean('activo')->default(true); // baja lógica
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paciente');
    }
};
