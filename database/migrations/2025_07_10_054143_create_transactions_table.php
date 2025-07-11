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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['buy', 'sell', 'dividend', 'crypto_reward']);
            $table->enum('reward_type', ['airdrop', 'launchpool', 'launchpad', 'staking'])->nullable();
            $table->decimal('quantity', 20, 8);
            $table->decimal('price_per_unit', 20, 8)->nullable();
            $table->decimal('total_fees', 20, 8)->nullable();
            $table->dateTime('date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transcations');
    }
};
