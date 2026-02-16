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
            $table->boolean('permiso_usuarios')->default(false);
            $table->boolean('permiso_activos')->default(true);
            $table->boolean('permiso_almacenes')->default(true);
            $table->boolean('permiso_incidencias')->default(true);
            $table->boolean('permiso_prestamos')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
