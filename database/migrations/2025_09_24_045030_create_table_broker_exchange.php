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
        Schema::create('broker_exchange', function (Blueprint $table) {
            $table->id();
            $table->foreignId('broker_id')->constrained('brokers')->onDelete('cascade');
            $table->foreignId('exchange_id')->constrained('exchanges')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['broker_id', 'exchange_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('broker_exchange');
    }
};