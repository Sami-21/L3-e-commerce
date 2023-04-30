<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Order;

class OrderController extends Controller
{
    public function all()
    {
        return response()->json(Order::with('products')->get(), 200);
    }

    public function get($id)
    {
        $order = Order::with('products')->findOrFail($id);
        return response()->json($order, 200);
    }

    public function add(OrderRequest $request)
    {
        $validated = $request->validated();
        $order = Order::create($validated);
        foreach ($validated['products'] as $product) {
            $order->products()->attach($product['id'], ['quantity' => $product['quantity'], 'price' => $product['price']]);
        }
        return response()->json(['message' => 'order created successfully', 'order' => $order], 201);
    }
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->products()->detach();
        $order->delete();
        return response()->json(['message' => 'order deleted successfully'], 200);
    }
}
