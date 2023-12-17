<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $userType): Response
    {
        // Kiểm tra userType của người dùng
        if ($request->user() && $request->user()->userType == $userType) {
            return $next($request);
        }

        // Trả về lỗi nếu không có quyền truy cập
        return response()->json([
            'status' => false,
            'error' => 'Unauthorized'
        ], 403);
    }
}
