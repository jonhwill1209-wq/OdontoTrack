<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('odontologo', function (Blueprint $table) {
            $table->id('id_odontologo');
            $table->foreignId('id_usuario')->unique()->constrained('usuario', 'id_usuario');
            $table->string('numero_colegiado', 40)->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('odontologo');
    }
};
