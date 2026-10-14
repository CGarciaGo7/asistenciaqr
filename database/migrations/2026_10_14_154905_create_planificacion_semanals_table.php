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
        Schema::create('planificacion_semanals', function (Blueprint $table) {
            $table->id();
             $table->foreignId('sucursal_id')
                ->constrained('sucursals')
                ->cascadeOnDelete();

            $table->date('fecha_inicio');
            $table->date('fecha_fin');

            $table->string('estado', 20)->default('registrada');

            $table->timestamps();
        
            $table->unique(
                ['sucursal_id', 'fecha_inicio'],
                'planificacion_sucursal_semana_unica'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planificacion_semanals');
    }
};
