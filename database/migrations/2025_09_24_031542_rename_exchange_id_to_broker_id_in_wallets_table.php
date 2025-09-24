<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropForeign(['exchange_id']);
            $table->renameColumn('exchange_id', 'broker_id');
            $table->foreign('broker_id')->references('id')->on('brokers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropForeign(['broker_id']);
            $table->renameColumn('broker_id', 'exchange_id');
            $table->foreign('exchange_id')->references('id')->on('exchanges')->onDelete('cascade');
        });
    }
};
