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
        Schema::create('trabajadors', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained()->cascadeOnDelete();

            $table->foreignId('sucursal_id')->constrained()->cascadeOnDelete();

            $table->foreignId('area_id')->constrained()->cascadeOnDelete();

            $table->foreignId('cargo_id')->constrained()->cascadeOnDelete();

            $table->string('codigo',20)->unique();

            $table->string('dni',8)->unique();
            $table->string('primer_nombre');
            $table->string('segundo_nombre')->nullable();
            $table->string('primer_apellido');
            $table->string('segundo_apellido')->nullable();
            $table->enum('sexo',['M','F']);
            $table->string('direccion')->nullable();
            $table->string('telefono',20)->nullable();
            $table->string('foto')->nullable();
            $table->date('fecha_ingreso');
            $table->date('fecha_cese')->nullable();
            $table->boolean('estado')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trabajadors');
    }
};
