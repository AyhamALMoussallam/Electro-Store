<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CategoryProductImageSync
{
    /**
     * Two HD, category-relevant images per category (Pexels, 1200px wide).
     */
    public const CATEGORY_IMAGE_URLS = [
        'Mobile & Tablets' => [
            'https://images.pexels.com/photos/1092644/pexels-photo-1092644.jpeg?auto=compress&cs=tinysrgb&w=1200',
            'https://images.pexels.com/photos/699122/pexels-photo-699122.jpeg?auto=compress&cs=tinysrgb&w=1200',
        ],
        'Laptops & Computers' => [
            'https://images.pexels.com/photos/18105/pexels-photo.jpg?auto=compress&cs=tinysrgb&w=1200',
            'https://images.pexels.com/photos/7974/pexels-photo.jpg?auto=compress&cs=tinysrgb&w=1200',
        ],
        'Cameras & Photography' => [
            'https://images.pexels.com/photos/212372/pexels-photo-212372.jpeg?auto=compress&cs=tinysrgb&w=1200',
            'https://images.pexels.com/photos/90946/pexels-photo-90946.jpeg?auto=compress&cs=tinysrgb&w=1200',
        ],
        'Audio & Headphones' => [
            'https://images.pexels.com/photos/3394650/pexels-photo-3394650.jpeg?auto=compress&cs=tinysrgb&w=1200',
            'https://images.pexels.com/photos/3783471/pexels-photo-3783471.jpeg?auto=compress&cs=tinysrgb&w=1200',
        ],
        'TVs & Monitors' => [
            'https://images.pexels.com/photos/5721908/pexels-photo-5721908.jpeg?auto=compress&cs=tinysrgb&w=1200',
            'https://images.pexels.com/photos/157826/pexels-photo-157826.jpeg?auto=compress&cs=tinysrgb&w=1200',
        ],
        'Gaming' => [
            'https://images.pexels.com/photos/442576/pexels-photo-442576.jpeg?auto=compress&cs=tinysrgb&w=1200',
            'https://images.pexels.com/photos/371924/pexels-photo-371924.jpeg?auto=compress&cs=tinysrgb&w=1200',
        ],
        'Smart Home' => [
            'https://images.pexels.com/photos/4792285/pexels-photo-4792285.jpeg?auto=compress&cs=tinysrgb&w=1200',
            'https://images.pexels.com/photos/1571460/pexels-photo-1571460.jpeg?auto=compress&cs=tinysrgb&w=1200',
        ],
        'Accessories' => [
            'https://images.pexels.com/photos/2115256/pexels-photo-2115256.jpeg?auto=compress&cs=tinysrgb&w=1200',
            'https://images.pexels.com/photos/3945683/pexels-photo-3945683.jpeg?auto=compress&cs=tinysrgb&w=1200',
        ],
    ];

    /**
     * Download category images and assign to all products (2 per category, alternating).
     *
     * @return array<string, array<int, string>> category name => [storage path, storage path]
     */
    public function sync(bool $assignToProducts = true): array
    {
        $dir = storage_path('app/public/products');
        File::ensureDirectoryExists($dir);

        $map = [];

        foreach (self::CATEGORY_IMAGE_URLS as $categoryName => $urls) {
            $slug = Str::slug($categoryName);
            $paths = [];

            foreach ($urls as $index => $url) {
                $filename = $slug . '-' . ($index + 1) . '.jpg';
                $fullPath = $dir . '/' . $filename;
                $storagePath = 'products/' . $filename;

                $this->downloadImage($url, $fullPath);
                $paths[] = $storagePath;
            }

            $map[$categoryName] = $paths;
        }

        if ($assignToProducts) {
            $this->assignImagesToProducts($map);
        }

        return $map;
    }

    public function imageForCategory(string $categoryName, int $productIndexInCategory): ?string
    {
        if (! isset(self::CATEGORY_IMAGE_URLS[$categoryName])) {
            return null;
        }

        $slug = Str::slug($categoryName);
        $fileIndex = $productIndexInCategory % 2;

        return 'products/' . $slug . '-' . ($fileIndex + 1) . '.jpg';
    }

    /**
     * @return array<int, string> storage paths for both category gallery images
     */
    public static function categoryGalleryPaths(?string $categoryName): array
    {
        if (! $categoryName || ! isset(self::CATEGORY_IMAGE_URLS[$categoryName])) {
            return [];
        }

        $slug = Str::slug($categoryName);

        return [
            'products/' . $slug . '-1.jpg',
            'products/' . $slug . '-2.jpg',
        ];
    }

    private function assignImagesToProducts(array $map): void
    {
        $categories = Category::all()->keyBy('name');
        $counters = [];

        Product::with('category')->orderBy('id')->chunk(50, function ($products) use ($map, $categories, &$counters) {
            foreach ($products as $product) {
                $categoryName = $product->category?->name
                    ?? $categories->get($product->category_id)?->name;

                if (! $categoryName || empty($map[$categoryName])) {
                    continue;
                }

                $counters[$categoryName] = $counters[$categoryName] ?? 0;
                $image = $map[$categoryName][$counters[$categoryName] % 2];
                $counters[$categoryName]++;

                $product->update(['image' => $image]);
            }
        });
    }

    private function downloadImage(string $url, string $destination): void
    {
        $response = Http::timeout(120)
            ->withOptions(['allow_redirects' => true])
            ->withHeaders([
                'User-Agent' => 'Electro-V2/1.0 (local dev; product image sync)',
            ])
            ->get($url);

        if (! $response->successful()) {
            throw new \RuntimeException(
                'Failed to download image: ' . $url . ' (HTTP ' . $response->status() . ')'
            );
        }

        $body = $response->body();

        if (strlen($body) < 10_000) {
            throw new \RuntimeException('Downloaded image is too small: ' . $url);
        }

        File::put($destination, $body);
    }
}
