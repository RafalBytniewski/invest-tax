<?php

namespace App\Console\Commands;

use App\Services\Asset\AssetImportService;
use Illuminate\Console\Command;

class ImportAssets extends Command
{
    /*
        Move files to - storage/app/data

        Available file names:
        us-stock
        eu-stock
        pl-stock
        etf
        crypto

        php artisan assets:import 'file-name'
        --reset - delete all prices for the selected asset type
    */

    protected $signature = 'assets:import {type} {--reset}';

    protected $description = 'Import asset with prices';

    public function handle(AssetImportService $service): int
    {
        $type = $this->argument('type');

        $allowedTypes = [
            'us-stock',
            'eu-stock',
            'pl-stock',
            'etf',
            'crypto',
        ];

        if (!in_array($type, $allowedTypes, true)) {
            $this->error(
                'Allowed: us-stock, eu-stock, pl-stock, etf, crypto'
            );

            return self::FAILURE;
        }

        $filePath = storage_path("app/data/{$type}.json");

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");

            return self::FAILURE;
        }

        $progressBar = null;

        try {
            $stats = $service->import(
                $filePath,
                $type,
                $this->option('reset'),
                function (
                    int $current,
                    int $total,
                    string $symbol
                ) use (&$progressBar): void {
                    if ($progressBar === null) {
                        $progressBar = $this->output->createProgressBar($total);

                        $progressBar->setFormat(
                            ' %current%/%max% [%bar%] %percent:3s%% %message%'
                        );

                        $progressBar->start();
                    }

                    $progressBar->setMessage($symbol);
                    $progressBar->advance();
                }
            );

            if ($progressBar !== null) {
                $progressBar->finish();
                $this->newLine(2);
            }

            $this->info("Imported {$type}");

            $this->table(
                ['Statistic', 'Count'],
                [
                    ['New assets', $stats['new_assets']],
                    ['Existing assets', $stats['existing_assets']],
                    ['Prices processed', $stats['prices_processed']],
                    [
                        'Assets without prices',
                        count($stats['assets_without_prices']),
                    ],
                ]
            );

            if (!empty($stats['assets_without_prices'])) {
                $this->newLine();

                $this->warn('Assets without prices:');

                $this->line(
                    implode(', ', $stats['assets_without_prices'])
                );
            }
        } catch (\Throwable $e) {
            if ($progressBar !== null) {
                $this->newLine();
            }

            $this->error("Import failed: {$e->getMessage()}");

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}