<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expediente_archivo', function (Blueprint $table) {
            $table->id('id_archivo');
            $table->foreignId('id_expediente')->constrained('expediente', 'id_expediente')->cascadeOnDelete();
            $table->string('nombre_original', 255);
            $table->string('ruta', 500)->unique();
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('tamano');
            $table->dateTime('subido_en')->useCurrent();
            $table->index(['id_expediente', 'subido_en']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expediente_archivo');
    }
};
