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
        Schema::create('request_logs', function (Blueprint $table) {
            $table->id();
            // Usamos UUID para el request_id por su naturaleza única
            $table->char('request_id', 36)->nullable()->unique();
            $table->string('method', 10);
            $table->string('url', 2048);
            $table->bigInteger('user_id')->unsigned()->nullable()->index();
            $table->string('ip', 45)->nullable();
            $table->json('payload')->nullable();
            $table->json('session_data')->nullable();
            $table->smallInteger('status')->unsigned()->nullable();
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_logs');
    }
};
