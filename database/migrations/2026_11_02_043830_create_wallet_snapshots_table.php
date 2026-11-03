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
        Schema::create('wallet_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->cascadeOnDelete();
            $table->dateTime('snapshot_at');
            $table->string('snapshot_type')->default('daily');
            $table->decimal('cash_value', 20, 8);
            $table->decimal('positions_value', 20, 8);
            $table->decimal('total_value', 20, 8);
            $table->string('event_type')->nullable();
            $table->decimal('event_amount', 20, 8)->nullable();
            $table->foreignId('wallet_ledger_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('transaction_id')->nullable()->constrained()->nullOnDelete();
            $table->string('valuation_status')->default('complete');
            $table->date('price_as_of')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['wallet_id', 'snapshot_at']);
            $table->index(['wallet_id', 'snapshot_type', 'snapshot_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_snapshots');
    }
};
