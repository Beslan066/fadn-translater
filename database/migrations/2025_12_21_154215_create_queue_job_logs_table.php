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
        Schema::create('queue_job_logs', function (Blueprint $table) {
            $table->id();
            $table->string('job_id');
            $table->string('job_type');
            $table->string('queue');
            $table->json('payload')->nullable();
            $table->string('status'); // pending, processing, completed, failed
            $table->float('execution_time')->nullable();
            $table->text('output')->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queue_job_logs');
    }
};
