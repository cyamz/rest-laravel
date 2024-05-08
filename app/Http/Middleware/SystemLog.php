<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class SystemLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request_id = (string) Str::uuid();

        $request->request_id = $request_id;
        if (PERFORMANCE_MONITORING) {
            $request->request_time = microtime(true);
        }

        Log::shareContext([
            'request-id' => $request_id,
        ]);
        // 填空，自动生成完整input日志
        Log::channel('request')->info('');

        return $next($request);
    }
}
