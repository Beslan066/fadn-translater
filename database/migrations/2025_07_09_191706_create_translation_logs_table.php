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
        Schema::create('translation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('translation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action', 50);
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->text('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('translation_logs');
    }
};
