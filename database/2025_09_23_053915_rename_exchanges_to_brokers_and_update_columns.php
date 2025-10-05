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
        Schema::rename('exchanges', 'brokers');

        Schema::table('brokers', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
    }

    public function down(): void
    {
        Schema::table('brokers', function (Blueprint $table) {
            $table->string('currency')->nullable();
        });
        Schema::rename('brokers', 'exchanges');
    }
};
