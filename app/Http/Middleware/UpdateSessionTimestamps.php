<?php

// namespace App\Http\Middleware;

// use Closure;
// use Illuminate\Http\Request;
// use Symfony\Component\HttpFoundation\Response;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Session;

// class UpdateSessionTimestamps
// {
//     /**
//      * Handle an incoming request.
//      *
//      * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
//      */
//     public function handle($request, Closure $next)
//     {
//         $response = $next($request);

//         if (Session::isStarted()) {
//             DB::table('sessions')
//                 ->where('id', Session::getId())
//                 ->whereNull('created_at') 
//                 ->update([
//                     'created_at' => now(),
//                     'updated_at' => now(),
//                 ]);
//         }

//         return $response;
//     }
// }
