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
        Schema::create('horarios', function (Blueprint $table) {

            $table->id();

            $table->foreignId('empresa_id')
                ->constrained('empresas')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('nombre',100);

            $table->string('tipo_turno',20)
                ->default('CONTINUO');

            $table->time('hora_ingreso');

            $table->time('hora_salida');

            $table->time('hora_ingreso2')->nullable();

            $table->time('hora_salida2')->nullable();

            $table->unsignedSmallInteger('refrigerio')->default(0);

            $table->unsignedSmallInteger('tolerancia')->default(10);

            $table->boolean('estado')->default(true);

            $table->text('observacion')->nullable();

            $table->timestamps();

            $table->unique(['empresa_id','nombre']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
