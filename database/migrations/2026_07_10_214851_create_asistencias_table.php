<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();

            $table->foreignId('trabajador_id')
                    ->constrained()
                    ->cascadeOnDelete();

            $table->date('fecha');
            $table->time('hora_ingreso')->nullable();
            $table->time('hora_salida')->nullable();
            $table->enum('tipo', ['Normal', 'Tardanza', 'Falta']);
            $table->string('estado')->default('Pendiente');
            $table->string('observacion')->nullable();

            // Índices
            $table->index('fecha');
            $table->index('trabajador_id');
            $table->index(['trabajador_id', 'fecha']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
