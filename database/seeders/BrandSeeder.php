<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            'Apple',
            'Samsung',
            'Sony',
            'Lenovo',
            'HP',
            'Dell',
            'Xiaomi',
            'Huawei',
            'Canon',
            'Nikon',
            'LG',
            'JBL',
            'Bose',
            'Asus',
            'Microsoft',
            'Anker',
            'Logitech',
            'Razer',
        ];

        foreach ($brands as $name) {
            Brand::firstOrCreate(['name' => $name]);
        }
    }
}
