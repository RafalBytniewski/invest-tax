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
        Schema::create('asset_broker', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->foreignId('broker_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Opcjonalnie unikalne ograniczenie, żeby nie powtarzać tego samego powiązania
            $table->unique(['asset_id', 'broker_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_broker');
    }
};
