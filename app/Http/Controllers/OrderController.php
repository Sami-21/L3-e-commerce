<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatusEnum;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderCollection;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function all(Request $request)
    {
        $orders = Order::with('products')->where('address', 'LIKE', '%' . $request->q . '%')->where('wilaya', 'LIKE', '%' . $request->wilaya . '%')->orderBy('created_at', 'desc')->paginate($request->perPage);
        return response()->json(new OrderCollection($orders), 200);
    }

    public function get($id)
    {
        $order = Order::with('products')->findOrFail($id);
        return response()->json($order, 200);
    }

    public function getClientOrders($id)
    {
        $orders = Order::where('client_id', $id)->with('products')->get();
        return response()->json($orders, 200);
    }

    public function add(OrderRequest $request)
    {
        $request->validated();
        $order = Order::create($request->all());
        foreach ($request['products'] as $product) {
            $order->products()->attach($product['id'], ['quantity' => $product['quantity'], 'price' => $product['price']]);
        }
        return response()->json(['message' => 'order created successfully', 'order' => $order], 201);
    }

    public function pend($id)
    {
        $order = Order::findOrFail($id);
        if (!$order)
            return response()->json(['message' => 'order not found'], 404);
        if ($order->status == "Pending")
            return response()->json(['message' => 'order already pended'], 404);
        $order->status = OrderStatusEnum::Pending;
        $order->save();
        return response()->json(['message' =>
        'status updated'], 200);
    }

    public function fulfill($id)
    {
        $order = Order::findOrFail($id);
        if (!$order)
            return response()->json(['message' => 'order not found'], 404);
        if ($order->status == "Fullfilled")
            return response()->json(['message' => 'order already fulfilled'], 404);
        $order->status = OrderStatusEnum::Fulfilled;
        $order->save();
        return response()->json(['message' =>
        'status updated'], 200);
    }

    public function cancel($id)
    {
        $order = Order::findOrFail($id);
        if (!$order)
            return response()->json(['message' => 'order not found'], 404);
        if ($order->status == "Canceled")
            return response()->json(['message' => 'order already canceled'], 404);
        $order->status = OrderStatusEnum::Canceled;
        $order->save();
        return response()->json(['message' => 'status updated', 'order' => $order], 200);
    }


    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->products()->detach();
        $order->delete();
        return response()->json(['message' => 'order deleted successfully'], 200);
    }
}
