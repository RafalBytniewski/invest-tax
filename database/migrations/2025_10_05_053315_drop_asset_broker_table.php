<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('asset_broker');
    }

    public function down(): void
    {
        Schema::create('asset_broker', function ($table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->foreignId('broker_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['asset_id', 'broker_id']);
        });
    }
};
