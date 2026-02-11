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
        Schema::create('prestamos', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fecha_prestado');
            $table->dateTime('fecha_devuelto')->nullable();
            $table->integer('cantidad_prestada');
            $table->integer('cantidad_devuelta')->nullable();
            $table->text('descripcion')->default(null)->nullable();
            $table->foreignId('activo_id')->constrained('activos');
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestamos');
    }
};
