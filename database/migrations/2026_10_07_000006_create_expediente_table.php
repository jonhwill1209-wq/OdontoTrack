<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expediente', function (Blueprint $table) {
            $table->id('id_expediente');
            $table->foreignId('id_paciente')->unique()->constrained('paciente', 'id_paciente');
            $table->dateTime('creado_en')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expediente');
    }
};
