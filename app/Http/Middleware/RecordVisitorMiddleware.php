<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class RecordVisitorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();

        // Check if the visitor already exists
        $existingVisitor = DB::table('visitors')->where('ip', $ip)->first();

        if (!$existingVisitor) {
            // Save visitor information to the database
            DB::table('visitors')->insert([
                'ip' => $ip,
                'user_agent' => $request->userAgent(),
                'created_at' => now()
            ]);
        }

        return $next($request);
    }
}
