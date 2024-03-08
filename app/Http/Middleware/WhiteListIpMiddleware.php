<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Whitelist;

class WhiteListIpMiddleware
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
        $find_ip = Whitelist::where("ip_address",$request->getClientIp())->where("status","ACTIVE")->first();

        // if (!$find_ip) {
        //     abort(403, "You are restricted to access the site.");
        // }
  
        return $next($request);
    }
}
