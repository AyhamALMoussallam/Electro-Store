<?php

use App\Models\Product;
use App\Support\ProductDescriptionGenerator;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('description_en')->nullable()->after('name');
            $table->text('description_ar')->nullable()->after('description_en');
        });

        Product::query()->with('category')->each(function (Product $product) {
            $category = $product->category?->name ?? 'Accessories';
            $pair = ProductDescriptionGenerator::fromLegacy(
                $product->description,
                $product->name,
                $category
            );

            $product->forceFill([
                'description_en' => $pair['en'] ?: ProductDescriptionGenerator::english($product->name, $category),
                'description_ar' => $pair['ar'] ?: ProductDescriptionGenerator::arabic($product->name, $category),
            ])->save();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
        });

        Product::query()->each(function (Product $product) {
            $product->forceFill([
                'description' => $product->description_en ?? '',
            ])->save();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['description_en', 'description_ar']);
        });
    }
};
