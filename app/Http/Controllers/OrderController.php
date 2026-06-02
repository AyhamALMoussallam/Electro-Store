<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\OrderLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Get all orders
     */
    public function index()
    {
        $orders = Order::with([
            'user',
            'area.city',
            'items.product',
            'logs.admin'
        ])->latest()->get();

        return $this->success(
            $orders,
            'Orders fetched successfully'
        );
    }

    /**
     * Get single order
     */
    public function show(string $id)
    {
        $order = Order::with([
            'user',
            'area.city',
            'items.product'
        ])->find($id);

        if (!$order) {
            return $this->notFound('Order');
        }

        return $this->success(
            $order,
            'Order fetched successfully'
        );
    }

    /**
     * Create order
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'area_id' => 'required|exists:areas,id',
            'note' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return $this->validationError(
                $validator->errors()
            );
        }

        $user = auth()->user();

        // =========================
        // GET USER CART
        // =========================
        $cart = $user->cart;

        if (!$cart) {

            return response()->json([
                'success' => false,
                'message' => 'Cart not found'
            ], 404);
        }

        // =========================
        // GET CART ITEMS
        // =========================
        $cartItems = CartItem::with('product')
            ->where('cart_id', $cart->id)
            ->get();

        if ($cartItems->isEmpty()) {

            return response()->json([
                'success' => false,
                'message' => 'Cart is empty'
            ], 400);
        }




        DB::beginTransaction();

        foreach ($cartItems as $item) {

            $item->product->refresh();

            if ($item->quantity > $item->product->stock) {

                throw new \Exception(
                    $item->product->name .
                    ' has only ' .
                    $item->product->stock .
                    ' item(s) available'
                );
            }
        }

        try {

            // =========================
            // GET AREA
            // =========================
            $area = Area::find($request->area_id);

            // =========================
            // CALCULATE SUBTOTAL
            // =========================
            $subtotal = 0;

            foreach ($cartItems as $item) {

                $subtotal += (
                    $item->quantity *
                    $item->product->price
                );
            }

            // =========================
            // SHIPPING
            // =========================
            $shippingFee = $area->fee ?? 0;

            // =========================
            // TOTAL
            // =========================
            $total = $subtotal + $shippingFee;

            // =========================
            // CREATE ORDER
            // =========================
            $order = Order::create([
                'user_id' => $user->id,
                'area_id' => $request->area_id,
                'total_price' => $total,
                'status' => 'pending',
                'note' => $request->note
            ]);

            // =========================
            // CREATE ORDER ITEMS
            // =========================



            foreach ($cartItems as $item) {

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity
                ]);

                $item->product->decrement(
                    'stock',
                    $item->quantity
                );
            }

            // =========================
            // CLEAR CART
            // =========================
            CartItem::where(
                'cart_id',
                $cart->id
            )->delete();

            DB::commit();

            return $this->created(
                $order->load([
                    'items.product',
                    'area.city'
                ]),
                'Order created successfully'
            );

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update order status
     */
public function updateStatus(Request $request, string $id)
{
    $order = Order::find($id);

    if (!$order) {
        return $this->notFound('Order');
    }

    $validator = Validator::make($request->all(), [
        'status' => 'required|in:pending,paid,shipped,delivered,canceled'
    ]);

    if ($validator->fails()) {
        return $this->validationError(
            $validator->errors()
        );
    }

    $oldStatus = $order->status;
    $newStatus = $request->status;

    // prevent same status
    if ($oldStatus === $newStatus) {

        return response()->json([
            'success' => false,
            'message' => 'Order already has this status'
        ], 400);
    }

    // update status
    $order->status = $newStatus;
    $order->save();

    // create log
    OrderLog::create([
        'order_id'   => $order->id,
        'admin_id'   => auth()->id(),
        'action'     => 'Updated order status',
        'old_status' => $oldStatus,
        'new_status' => $newStatus
    ]);

    return $this->success(
        $order,
        'Order status updated successfully'
    );
}

    /**
     * Delete order
     */
    public function destroy(string $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return $this->notFound('Order');
        }

        $order->delete();

        return $this->success(
            [],
            'Order deleted successfully'
        );
    }
}