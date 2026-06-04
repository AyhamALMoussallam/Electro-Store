<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            BrandSeeder::class,
            SyriaCityAreaSeeder::class,
            UserSeeder::class,
            ProductSeeder::class,
            ReviewSeeder::class,
        ]);

        $this->command?->info('Tip: run php artisan products:sync-images to refresh HD product photos.');
    }
}
