<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    public function index()
    {
        return $this->success(
            Brand::latest()->get(),
            'Brands fetched successfully'
        );
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return $this->validationError(
                $validator->errors()
            );
        }

        $brand = Brand::create([
            'name' => $request->name
        ]);

        return $this->created(
            $brand,
            'Brand created successfully'
        );
    }

    public function update(Request $request, string $id)
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return $this->notFound('Brand');
        }

        $brand->update([
            'name' => $request->name
        ]);

        return $this->success(
            $brand,
            'Brand updated successfully'
        );
    }

    public function destroy(string $id)
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return $this->notFound('Brand');
        }

        $brand->delete();

        return $this->success(
            [],
            'Brand deleted successfully'
        );
    }
}