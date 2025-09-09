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
        Schema::table('sentences', function (Blueprint $table) {
            $table->tinyInteger('otherSentence')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sentences', function (Blueprint $table) {
            Schema::dropColumn('otherSentence');
        });
    }
};
