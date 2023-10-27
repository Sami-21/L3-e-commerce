<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getVisitorsCount()
    {
        $visitors = Visitor::count();
        return response()->json([
            'visitors' => $visitors
        ], 200);
    }
}
