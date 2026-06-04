<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\CategoryProductImageSync;
use App\Support\ProductDescriptionGenerator;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    private array $productNames = [
        'Mobile & Tablets' => [
            'Galaxy S24 Ultra 256GB', 'iPhone 15 Pro 128GB', 'Redmi Note 13 Pro',
            'Huawei Nova 12', 'Galaxy Tab S9', 'iPad Air 11 M2',
            'Pixel 8 Pro', 'Galaxy A55', 'iPhone 14', 'Xiaomi Pad 6',
            'Galaxy Z Flip 5', 'Poco X6 Pro', 'iPad 10th Gen',
        ],
        'Laptops & Computers' => [
            'ThinkPad X1 Carbon', 'MacBook Air M3', 'Dell XPS 15',
            'HP Pavilion 15', 'Asus ROG Strix G16', 'Lenovo IdeaPad Slim 5',
            'MacBook Pro 14 M3 Pro', 'Surface Laptop 5', 'Asus VivoBook 15',
            'Dell Inspiron 16', 'HP Victus 16', 'Lenovo Legion 5',
        ],
        'Cameras & Photography' => [
            'Canon EOS R50 Kit', 'Sony Alpha a6400', 'Nikon Z50 II',
            'GoPro Hero 12', 'Canon PowerShot G7 X', 'Sony ZV-E10',
            'DJI Osmo Pocket 3', 'Canon RF 50mm Lens', 'Sony 24-70mm Lens',
            'Tripod Pro 160cm', 'Camera Bag Large', '64GB SD Card Pro',
        ],
        'Audio & Headphones' => [
            'Sony WH-1000XM5', 'AirPods Pro 2', 'JBL Tune 770NC',
            'Bose QuietComfort Ultra', 'Samsung Buds3 Pro', 'JBL Charge 5',
            'Sony SRS-XB43 Speaker', 'Audio-Technica ATH-M50x', 'Soundbar 2.1 120W',
            'Gaming Headset 7.1', 'USB Microphone Studio', 'Portable DAC Amp',
        ],
        'TVs & Monitors' => [
            'Samsung 55" QLED 4K', 'LG 65" OLED C3', 'Sony 50" Bravia 4K',
            'TCL 43" Smart TV', 'Dell 27" 4K Monitor', 'LG 32" UltraGear',
            'Samsung 27" Odyssey G5', 'Asus 24" ProArt', 'TV Wall Mount Kit',
            'HDMI 2.1 Cable 3m', 'Monitor Arm Dual', 'Streaming Stick 4K',
        ],
        'Gaming' => [
            'PlayStation 5 Slim', 'Xbox Series X', 'Nintendo Switch OLED',
            'DualSense Controller', 'Xbox Wireless Controller', 'Gaming Chair Pro',
            'Mechanical Keyboard RGB', 'Gaming Mouse 16000 DPI', 'Racing Wheel Kit',
            'VR Headset Standalone', 'Game Capture Card', '512GB SSD NVMe Gen4',
        ],
        'Smart Home' => [
            'Smart LED Bulb 4-Pack', 'Wi-Fi Security Camera', 'Video Doorbell HD',
            'Smart Plug 3-Pack', 'Robot Vacuum S7', 'Air Purifier HEPA',
            'Smart Thermostat', 'Motion Sensor Kit', 'Smart Hub Zigbee',
            'Garden Irrigation Wi-Fi', 'Smoke Detector Smart', 'Garage Opener Wi-Fi',
        ],
        'Accessories' => [
            'USB-C Hub 7-in-1', '65W GaN Charger', 'Power Bank 20000mAh',
            'Laptop Stand Aluminum', 'Wireless Mouse Silent', 'Bluetooth Keyboard',
            'Phone Holder Car', 'Screen Protector Pack', 'Silicone Case Bundle',
            'Cable Organizer Kit', 'Laptop Sleeve 15"', 'Ring Light 10"',
        ],
    ];

    public function run(): void
    {
        $sync = app(CategoryProductImageSync::class);

        $this->command?->info('Downloading HD category images...');
        $sync->sync(assignToProducts: false);

        $existing = Product::count();
        $target = 100;

        if ($existing >= $target) {
            $sync->sync(assignToProducts: true);

            return;
        }

        $categories = Category::all()->keyBy('name');
        $brands = Brand::all();

        if ($categories->isEmpty() || $brands->isEmpty()) {
            $this->command?->warn('Run CategorySeeder and BrandSeeder first.');

            return;
        }

        $names = $this->buildProductList();
        $toCreate = array_slice($names, $existing, $target - $existing);
        $categoryCounters = [];

        foreach ($toCreate as $item) {
            $category = $categories->get($item['category']);

            if (! $category) {
                continue;
            }

            $catName = $item['category'];
            $categoryCounters[$catName] = $categoryCounters[$catName] ?? 0;
            $image = $sync->imageForCategory($catName, $categoryCounters[$catName]);
            $categoryCounters[$catName]++;

            $descriptions = ProductDescriptionGenerator::pair($item['name'], $item['category']);

            Product::create([
                'category_id' => $category->id,
                'brand_id' => $brands->random()->id,
                'name' => $item['name'],
                'description_en' => $descriptions['en'],
                'description_ar' => $descriptions['ar'],
                'price' => fake()->numberBetween(15, 2499),
                'image' => $image ?? 'products/mobile-tablets-1.jpg',
                'stock' => fake()->numberBetween(5, 120),
                'sales' => fake()->numberBetween(0, 450),
            ]);
        }

        $sync->sync(assignToProducts: true);
    }

    private function buildProductList(): array
    {
        $list = [];

        foreach ($this->productNames as $category => $names) {
            foreach ($names as $name) {
                $list[] = ['category' => $category, 'name' => $name];
            }
        }

        $categories = Category::pluck('name')->all();
        $suffixes = ['Plus', 'Pro', 'Max', 'Elite', 'SE', '2024 Edition', 'Bundle'];

        while (count($list) < 100) {
            $category = fake()->randomElement($categories);
            $base = fake()->words(3, true);
            $suffix = fake()->randomElement($suffixes);
            $list[] = [
                'category' => $category,
                'name' => ucwords($base) . ' ' . $suffix,
            ];
        }

        return $list;
    }

}
