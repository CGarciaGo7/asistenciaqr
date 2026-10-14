<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('asignacion_horarios', function (Blueprint $table) {
            $table->id();
            // Trabajador
            $table->foreignId('trabajador_id')
                ->constrained('trabajadors')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // Horario asignado
            $table->foreignId('horario_id')
                ->constrained('horarios')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // Vigencia
            $table->date('fecha_inicio');

            $table->date('fecha_fin')->nullable();

            // Observación
            $table->text('observacion')->nullable();

            // Estado
            $table->boolean('estado')->default(true);

            $table->timestamps();

            // Índices
            $table->index('trabajador_id');
            $table->index('horario_id');
            $table->index('fecha_inicio');
            $table->index('estado');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignacion_horarios');
    }
};
