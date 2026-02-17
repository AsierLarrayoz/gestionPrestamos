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
        Schema::create('permisos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_rol')->unique();
            $table->boolean('permiso_usuarios_wr')->default(false);
            $table->boolean('permiso_activos_wr')->default(true);
            $table->boolean('permiso_almacenes_wr')->default(true);
            $table->boolean('permiso_incidencias_wr')->default(true);
            $table->boolean('permiso_prestamos_wr')->default(true);
            $table->boolean('permiso_reservas_wr')->default(true);

            $table->boolean('permiso_usuarios_r')->default(false);
            $table->boolean('permiso_activos_r')->default(true);
            $table->boolean('permiso_almacenes_r')->default(true);
            $table->boolean('permiso_incidencias_r')->default(true);
            $table->boolean('permiso_prestamos_r')->default(true);
            $table->boolean('permiso_reservas_r')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permisos');
    }
};
