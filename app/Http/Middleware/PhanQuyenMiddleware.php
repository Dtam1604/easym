<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PhanQuyenMiddleware
{
    /**
     * Handle an incoming request.
     * Kiểm tra xem người dùng có quyền truy cập vào Route không (Role-based).
     * Tham số $role truyền từ Route, ví dụ: middleware('auth.role:admin')
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Kiểm tra xem người dùng đã đăng nhập chưa
        if (! $request->user()) {
            return redirect('/dang-nhap')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        // 2. Kiểm tra vai_tro
        // Cho phép admin truy cập mọi nơi (Tùy chọn)
        // Nhưng ở đây ta kiểm tra strict theo $role
        if ($request->user()->vai_tro !== $role && $request->user()->vai_tro !== 'admin') {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        return $next($request);
    }
}
