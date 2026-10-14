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
        Schema::table('trabajadors', function (Blueprint $table) {
            $table->string('primer_nombre')->after('dni');
            $table->string('segundo_nombre')->nullable()->after('primer_nombre');

            $table->string('primer_apellido')->after('segundo_nombre');
            $table->string('segundo_apellido')->after('primer_apellido');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trabajadors', function (Blueprint $table) {
            $table->dropColumn([
                'primer_nombre',
                'segundo_nombre',
                'primer_apellido',
                'segundo_apellido',
            ]);
        });
    }
};