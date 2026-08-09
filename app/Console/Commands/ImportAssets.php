<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Asset\AssetImportService;

class ImportAssets extends Command
{
    /*
        move files to - storage/app/data
        available file names - us-stock, eu-stock, pl-stock, etf, crypto
        
        php artisan asset:import 'file-name'
        --reset - delete all prices for the selected asset type
    */
    protected $signature = 'assets:import {type} {--reset}';

    // description for php artisan list
    protected $description = 'Import asset with prices';

    public function handle(AssetImportService $service): int
    {
        $type = $this->argument('type');

        if (!in_array($type, ['us-stock', 'eu-stock', 'pl-stock', 'etf', 'crypto'])) {
            $this->error('Allowed: us-stock, eu-stock, pl-stock, etf, crypto');
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