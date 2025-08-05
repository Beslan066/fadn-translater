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
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sentence_id')->constrained()->cascadeOnDelete();
            $table->foreignId('region_id')->constrained();
            $table->foreignId('translator_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('proofreader_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('translated_text')->nullable();
            $table->text('proofread_text')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0-assigned, 1-translated, 2-proofread, 3-rejected');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('translated_at')->nullable();
            $table->timestamp('proofread_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('reject_reason')->nullable();
            $table->integer('edit_count')->default(0);
            $table->timestamps();

            $table->index(['sentence_id', 'region_id']);
            $table->index(['translator_id', 'status']);
            $table->index(['proofreader_id', 'status']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
