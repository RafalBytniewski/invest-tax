<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AssetImportService;

class ImportAssetsFromJson extends Command
{
    protected $signature = 'assets:import {--reset}';
    protected $description = 'Import assets and prices from JSON file';

    public function handle(AssetImportService $importService)
    {
        $filePath = storage_path('app/data/assets.json');

        if (!file_exists($filePath)) {
            $this->error('Plik JSON nie istnieje');
            return Command::FAILURE;
        }

        if ($this->option('reset')) {
            $this->warn('Reset danych...');
            $importService->reset();
        }

        $this->info('Start importu...');
        $importService->import($filePath);

        $this->info('Import zakończony ✅');

        return Command::SUCCESS;
    }
}
