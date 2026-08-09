<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `wallet_ledgers` MODIFY `type` ENUM('deposit', 'withdraw', 'correction', 'buy', 'sell', 'dividend', 'tax', 'fee') NOT NULL");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `wallet_ledgers` MODIFY `type` ENUM('deposit', 'withdraw', 'buy', 'sell', 'dividend', 'tax', 'fee') NOT NULL");
        }
    }
};
