<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('transaction_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['inflow', 'outflow']);
            $table->enum('source', [
                'manual_deposit',
                'manual_withdrawal',
                'buy',
                'sell',
                'dividend',
                'crypto_reward',
                'tax',
            ]);
            $table->decimal('amount', 20, 8);
            $table->dateTime('date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->boolean('affects_wallet_balance')->default(true)->after('type');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('asset_id')->nullable()->change();
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE transactions
                MODIFY type ENUM('buy', 'sell', 'dividend', 'crypto_reward', 'tax') NOT NULL
            ");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE transactions
                MODIFY type ENUM('buy', 'sell', 'dividend', 'crypto_reward') NOT NULL
            ");
        }

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('asset_id')->nullable(false)->change();
            $table->dropColumn('affects_wallet_balance');
        });

        Schema::dropIfExists('wallet_ledgers');
    }
};
