<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // جلب ريفيوهات منتج
    public function index($productId)
    {
        return Review::where('product_id', $productId)
            ->with('user') // إذا بدك اسم المستخدم
            ->latest()
            ->get();
    }

    // إضافة review
    public function store(Request $request)
{
    $userId = auth()->id();

    $exists = Review::where('user_id', $userId)
        ->where('product_id', $request->product_id)
        ->exists();

    if ($exists) {
        return response()->json([
            'message' => 'You already reviewed this product'
        ], 409);
    }

    Review::create([
        'user_id' => $userId,
        'product_id' => $request->product_id,
        'rating' => $request->rating,
        'comment' => $request->comment,
    ]);

    return response()->json(['message' => 'success']);
}
}