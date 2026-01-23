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
        Schema::create('asset_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->decimal('open_price', 20, 10)->nullable();
            $table->decimal('low_price', 20, 10)->nullable();
            $table->decimal('high_price', 20, 10)->nullable();
            $table->decimal('close_price', 20, 10)->nullable();
            $table->string('source');
            $table->date('date');
            $table->timestamps();
            $table->unique(['asset_id', 'date', 'source']);
            $table->index(['asset_id', 'date']);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_prices');
    }
};
