<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\PusherCredentail;
class PusherCredential
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
        // config(['broadcasting.connections.pusher' => $pusherConfig]);
        // dd(config('broadcasting.connections.pusher'));
        return $next($request);
    }
}
