<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TYPES = [
        'deposit',
        'withdraw',
        'correction',
        'buy',
        'sell',
        'dividend',
        'tax',
        'fee',
    ];

    public function up(): void
    {
        Schema::table('wallet_ledgers', function (Blueprint $table) {
            $table->enum('type', self::TYPES)->change();
        });
    }

    public function down(): void
    {
        Schema::table('wallet_ledgers', function (Blueprint $table) {
            $table->enum('type', array_values(array_diff(self::TYPES, ['correction'])))->change();
        });
    }
};
