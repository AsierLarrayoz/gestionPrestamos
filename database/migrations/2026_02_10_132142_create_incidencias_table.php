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
        Schema::create('incidencias', function (Blueprint $table) {
            $table->id();
            $table->text('descripcion')->nullable();
            $table->dateTime('fecha_incidencia');
            $table->foreignId('estado_id')->constrained('estados');
            $table->foreignId('nivel_id')->constrained('niveles', 'id');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('activo_id')->constrained('activos');
            $table->foreignId('prestamo_id')->nullable()->constrained('prestamos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidencias');
    }
};
