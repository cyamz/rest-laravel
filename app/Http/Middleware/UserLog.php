<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class UserLog
{
    /**
     * 记录用户input
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() ?? false) {
            Log::channel('user_request')->info(json_encode([
                'user_id' => $request->user()->id
            ]));
        }

        return $next($request);
    }
}
