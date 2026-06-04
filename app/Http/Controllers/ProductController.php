<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CategoryProductImageSync;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Get all products
     */
    public function index()
    {
        $products = Product::with(['category', 'brand'])
            ->latest()
            ->get();

        return $this->success(
            $products,
            'Products fetched successfully'
        );
    }

    /**
     * Get single product
     */
    public function show(string $id)
    {
        $product = Product::with(['category', 'brand'])->find($id);

        if (!$product) {
            return $this->notFound('Product');
        }

        $gallery = CategoryProductImageSync::categoryGalleryPaths(
            $product->category?->name
        );

        if ($gallery === []) {
            $gallery = $product->image ? [$product->image] : [];
        }

        $product->setAttribute('gallery_images', $gallery);

        return $this->success(
            $product,
            'Product fetched successfully'
        );
    }

    /**
     * Create product
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'brand_id' => 'required|exists:brands,id',

            // IMAGE
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->validationError(
                $validator->errors()
            );
        }

        // SAVE IMAGE
        $imagePath = null;

        if ($request->hasFile('image')) {

            $imagePath = $request
                ->file('image')
                ->store('products', 'public');
        }

        $product = Product::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $imagePath,
            'brand_id' => $request->brand_id,
        ]);

        return $this->created(
            $product,
            'Product created successfully'
        );
    }

    /**
     * Update product
     */
    public function update(Request $request, string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return $this->notFound('Product');
        }

        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',

            // IMAGE OPTIONAL
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->validationError(
                $validator->errors()
            );
        }

        $imagePath = $product->image;

        // IF NEW IMAGE UPLOADED
        if ($request->hasFile('image')) {

            // DELETE OLD IMAGE
            if ($product->image) {

                Storage::disk('public')
                    ->delete($product->image);
            }

            // SAVE NEW IMAGE
            $imagePath = $request
                ->file('image')
                ->store('products', 'public');
        }

        $product->update([
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $imagePath,
        ]);

        return $this->success(
            $product,
            'Product updated successfully'
        );
    }

    /**
     * Delete product
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return $this->notFound('Product');
        }

        // DELETE IMAGE
        if ($product->image) {

            Storage::disk('public')
                ->delete($product->image);
        }

        $product->delete();

        return $this->success(
            [],
            'Product deleted successfully'
        );
    }



    public function topSelling(Request $request)
    {
        $query = Product::with(['category', 'brand']);

        $categoryIds = $request->input('category_ids', []);
        if (! is_array($categoryIds)) {
            $categoryIds = [$categoryIds];
        }
        $categoryIds = array_filter($categoryIds);
        if ($categoryIds) {
            $query->whereIn('category_id', $categoryIds);
        }

        $brandIds = $request->input('brand_ids', []);
        if (! is_array($brandIds)) {
            $brandIds = [$brandIds];
        }
        $brandIds = array_filter($brandIds);
        if ($brandIds) {
            $query->whereIn('brand_id', $brandIds);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $products = $query
            ->orderByDesc('sales')
            ->take(3)
            ->get();

        return $this->success(
            $products,
            'Top selling products'
        );
    }
}