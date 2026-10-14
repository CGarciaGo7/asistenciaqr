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
        Schema::create('asignacion_turnos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('trabajador_id')
                ->constrained('trabajadors')
                ->cascadeOnDelete();

            $table->foreignId('sucursal_id')
                ->constrained('sucursals')
                ->restrictOnDelete();

            $table->foreignId('cargo_id')
                ->nullable()
                ->constrained('cargos')
                ->nullOnDelete();

            $table->foreignId('horario_id')
                ->nullable()
                ->constrained('horarios')
                ->nullOnDelete();

            $table->date('fecha');
            $table->unsignedTinyInteger('numero_semana');
            $table->unsignedSmallInteger('anio');
            $table->boolean('es_descanso')->default(false);
            $table->text('incidencias')->nullable();

            $table->timestamps();
             // Un solo registro por trabajador en cada fecha.
            $table->unique(
                ['trabajador_id', 'fecha'],
                'asig_turno_trabajador_fecha_unique'
            );

            $table->index(['sucursal_id', 'fecha']);
            $table->index(['anio', 'numero_semana']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignacion_turnos');
    }
};
