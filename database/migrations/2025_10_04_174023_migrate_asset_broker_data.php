<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('assets')->get()->each(function ($asset) {
            if ($asset->broker_id) { // tylko jeśli jest przypisany broker
                DB::table('asset_broker')->insert([
                    'asset_id'  => $asset->id,
                    'broker_id' => $asset->broker_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }

    public function down(): void
    {
        DB::table('asset_broker')->truncate(); // opcjonalnie usunięcie danych
    }
};

