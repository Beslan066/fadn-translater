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
        Schema::create('sentences', function (Blueprint $table) {
            $table->id();
            $table->text('sentence');
            $table->decimal('price', 8, 2)->default(0);
            $table->tinyInteger('complexity')->default(1)->comment('1-5 scale');
            $table->timestamp('locked_at')->nullable();
            $table->foreignId('locked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('delayed_until')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0-available, 1-assigned, 2-translated, 3-proofread, 4-rejected');
            $table->timestamps();

            $table->index(['status', 'locked_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sentences');
    }
};
