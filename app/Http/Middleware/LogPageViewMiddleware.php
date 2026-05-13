<?php

// namespace App\Http\Middleware;

// use Closure;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Session;
// use Symfony\Component\HttpFoundation\Response;

// class LogPageViewMiddleware
// {
//     /**
//      * Handle an incoming request.
//      *
//      * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
//      */
//     public function handle(Request $request, Closure $next): Response
//     {
//         // Không log nếu là trang admin
//         if ($request->is('admin') || $request->is('admin/*') || $request->is('track/*')) {
//             return $next($request);
//         }

//         DB::table('page_views')->insert([
//             'user_id' => auth()->check() ? auth()->id() : null,
//             'session_id' => Session::getId(),
//             'url' => $request->fullUrl(),
//             'ip_address' => $request->ip(),
//             'user_agent' => $request->userAgent(),
//             'created_at' => now(),
//             'updated_at' => now(),
//         ]);

//         return $next($request);
//     }
// }
