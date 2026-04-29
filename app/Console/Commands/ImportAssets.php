<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Asset\AssetImportService;

class ImportAssets extends Command
{
    protected $signature = 'assets:import {type} {--reset}';

    protected $description = 'Import stock / etf / crypto';

    public function handle(AssetImportService $service): int
    {
        $type = $this->argument('type');

        if (!in_array($type, ['stock', 'etf', 'crypto'])) {
            $this->error('Allowed: stock, etf, crypto');
            return self::FAILURE;
        }

        $filePath = storage_path("app/data/{$type}.json");

        if ($this->option('reset')) {
            $service->reset($type);
        }

        $service->import($filePath, $type);

        $this->info("Imported {$type}");

        return self::SUCCESS;
    }
}