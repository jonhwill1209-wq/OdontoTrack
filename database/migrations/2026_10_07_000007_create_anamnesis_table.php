<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anamnesis', function (Blueprint $table) {
            $table->id('id_anamnesis');
            $table->foreignId('id_expediente')->unique()->constrained('expediente', 'id_expediente');
            $table->text('alergias')->nullable();
            $table->text('antecedentes')->nullable();
            $table->text('medicamentos')->nullable();
            $table->dateTime('actualizado_en')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anamnesis');
    }
};
