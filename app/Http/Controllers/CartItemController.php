<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartItemController extends Controller
{
    /**
     * Get current user cart items
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $cart = Cart::where('user_id', $user->id)
            ->with('cartItems.product.category')
            ->first();

        if (!$cart) {
            return $this->success([], 'Cart is empty');
        }

        return $this->success(
            $cart->cartItems,
            'Cart items fetched successfully'
        );
    }

    /**
     * Add product to cart
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $product = Product::findOrFail(
            $request->product_id
        );

        if ($request->quantity > $product->stock) {

            return response()->json([
                'message' => "Only {$product->stock} item(s) available"
            ], 422);

        }

        $user = $request->user();

        // get or create cart
        $cart = Cart::firstOrCreate([
            'user_id' => $user->id
        ]);

        // check if product already exists
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($cartItem) {

            $newQuantity =
                $cartItem->quantity +
                $request->quantity;

            if ($newQuantity > $product->stock) {

                return response()->json([
                    'message' => "Only {$product->stock} item(s) available"
                ], 422);

            }

            $cartItem->update([
                'quantity' => $newQuantity
            ]);

        }

        else {

            $cartItem = CartItem::create([
                'cart_id'   => $cart->id,
                'product_id'=> $request->product_id,
                'quantity'  => $request->quantity,
            ]);
        }

        return $this->created(
            $cartItem,
            'Product added to cart'
        );
    }

    /**
     * Update quantity
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = $request->user();

        $cartItem = CartItem::where('id', $id)
            ->whereHas('cart', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->first();

        if (!$cartItem) {
            return $this->notFound('Cart Item');
        }

        $cartItem->update([
            'quantity' => $request->quantity
        ]);

        return $this->success(
            $cartItem,
            'Cart item updated successfully'
        );
    }

    /**
     * Remove item from cart
     */
    public function destroy(Request $request, string $id)
    {
        $user = $request->user();

        $cartItem = CartItem::where('id', $id)
            ->whereHas('cart', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->first();

        if (!$cartItem) {
            return $this->notFound('Cart Item');
        }

        $cartItem->delete();

        return $this->success(
            [],
            'Item removed from cart'
        );
    }
}