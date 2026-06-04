<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Mobile & Tablets',
            'Laptops & Computers',
            'Cameras & Photography',
            'Audio & Headphones',
            'TVs & Monitors',
            'Gaming',
            'Smart Home',
            'Accessories',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}
