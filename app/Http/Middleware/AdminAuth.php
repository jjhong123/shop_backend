<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
         // 預設不允許存取
       $is_allow_access = false;
       
       if (!is_null($request->user())) {
           // 如果資料庫 有會員編號資料，允許存取            
           if ($request->user()->roles == 'admin') {
               // 是管理者，允許存取
               $is_allow_access = true;
           }
       }
       
       if (!$is_allow_access) {
           // 若不允許存取，重新導向至首頁
           return response()->json([
               'error' => "your member's authority is not enough.",
           ], 401);
       }
       
       // 允許存取，繼續做下個請求的處理
       return $next($request);
    }
}
