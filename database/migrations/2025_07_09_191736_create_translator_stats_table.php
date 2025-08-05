<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('translator_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->unique()->onDelete('cascade');
            $table->integer('total_translations')->default(0);
            $table->integer('approved_translations')->default(0);
            $table->integer('rejected_translations')->default(0);
            $table->decimal('total_earned', 10, 2)->default(0);
            $table->decimal('quality_score', 5, 2)->default(0);
            $table->integer('current_streak')->default(0);
            $table->date('last_activity_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translator_stats');
    }
};
