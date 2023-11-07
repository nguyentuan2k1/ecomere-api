<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAuthApi
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('api')->check()) {
            $response = [
                'success' => false,
                'message' => "Please Login",
            ];

            return response()->json($response, 401);
        }

        return $next($request);
    }
}
