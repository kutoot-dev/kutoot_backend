<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;
class CheckSeller
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
        // $user = Auth::guard('web')->user();
        // if($user->seller){
        //     $seller = $user->seller;
        //     if($seller->status == 1){
        //         return $next($request);
        //     }else{
        //         $notification = trans('Something Went Wrong');
        //         return response()->json(['notification' => $notification],403);
        //     }
        // }else{
        //     $notification = trans('Something Went Wrong');
        //     return response()->json(['notification' => $notification],403);
        // }
        return $next($request);

    }
}
