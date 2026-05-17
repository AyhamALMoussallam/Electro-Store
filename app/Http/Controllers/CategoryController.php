<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        return $this->success($categories, 'Categories fetched successfully');
    }

    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return $this->notFound('Category');
        }

        return $this->success($category, 'Category fetched successfully');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $category = Category::create([
            'name' => $request->name
        ]);

        return $this->created($category, 'Category created successfully');
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return $this->notFound('Category');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name,' . $id
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $category->update([
            'name' => $request->name
        ]);

        return $this->success($category, 'Category updated successfully');
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return $this->notFound('Category');
        }

        $category->delete();

        return $this->success([], 'Category deleted successfully');
    }
}