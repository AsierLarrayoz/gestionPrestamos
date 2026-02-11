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
        Schema::create('activos', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->unique()->nullable();
            $table->uuid('uuid')->unique();
            $table->string('rfid_code')->nullable()->unique();
            $table->text('observaciones')->nullable();
            $table->integer('cantidad');
            $table->boolean('is_serialized')->default(true);
            $table->foreignId('modelo_id')->constrained('modelos');
            $table->foreignId('salud_id')->constrained('salud');
            $table->foreignId('tipo_id')->constrained('tipos');
            $table->foreignId('almacen_id')->constrained('almacenes', 'id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activos');
    }
};
