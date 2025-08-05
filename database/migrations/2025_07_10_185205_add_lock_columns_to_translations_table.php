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
        Schema::table('translations', function (Blueprint $table) {
            $table->foreignId('locked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('locked_at')->nullable();

            $table->index(['locked_by', 'locked_at']);
            $table->index(['translator_id', 'status', 'locked_at']);
        });
    }

    public function down()
    {
        Schema::table('translations', function (Blueprint $table) {
            $table->dropForeign(['locked_by']);
            $table->dropColumn(['locked_by', 'locked_at']);
        });
    }
};
