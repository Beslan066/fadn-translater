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
        Schema::create('queue_workers', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->default('default');
            $table->string('connection')->default('database');
            $table->string('status')->default('stopped'); // stopped, running, paused
            $table->integer('pid')->nullable();
            $table->json('options')->nullable();
            $table->integer('processed_jobs')->default(0); // Добавляем поле
            $table->integer('failed_jobs')->default(0);
            $table->timestamp('last_heartbeat')->nullable();
            $table->timestamps();

            // Добавляем уникальный индекс для очереди
            $table->unique('queue');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queue_workers');
    }
};
