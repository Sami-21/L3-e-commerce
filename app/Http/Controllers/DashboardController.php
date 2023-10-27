<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatusEnum;
use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use App\Models\Visitor;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getStats()
    {
        $visitors = Visitor::count();
        $clients = Client::count();
        $products = Product::count();
        $orders = Order::count();
        $estimatedRevenue = Order::where('status', OrderStatusEnum::Fulfilled)->sum('total');

        return response()->json([
            'visitors' => $visitors,
            'clients' => $clients,
            'products' => $products,
            'orders' => $orders,
            'revenue' => $estimatedRevenue
        ], 200);
    }
}
