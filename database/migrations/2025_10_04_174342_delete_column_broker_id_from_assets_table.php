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
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign(['broker_id']); // usuwa constraint FK
            $table->dropColumn('broker_id');    // usuwa kolumnę
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->foreignId('broker_id')->nullable()->constrained()->onDelete('cascade');
        });

    }
};
