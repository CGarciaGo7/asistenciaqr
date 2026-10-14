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
        Schema::table('sucursals', function (Blueprint $table) {
            $table->string('tipo_horario', 5)
                ->default('07_23')
                ->after('correo');
        });
    }

    public function down(): void
    {
        Schema::table('sucursals', function (Blueprint $table) {
            $table->dropColumn('tipo_horario');
        });
    }
};
