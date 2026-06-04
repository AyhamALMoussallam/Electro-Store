<?php

namespace App\Console\Commands;

use App\Services\CategoryProductImageSync;
use Illuminate\Console\Command;

class SyncProductImages extends Command
{
    protected $signature = 'products:sync-images';

    protected $description = 'Download HD category images (2 per category) and assign them to products';

    public function handle(CategoryProductImageSync $sync): int
    {
        $this->info('Downloading HD images and updating products...');

        try {
            $map = $sync->sync(true);
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        foreach ($map as $category => $paths) {
            $this->line($category . ': ' . implode(', ', $paths));
        }

        $this->info('Done. Product images updated.');

        return self::SUCCESS;
    }
}
