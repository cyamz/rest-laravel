<?php

namespace App\Http\Middleware;

use App\Exceptions\SyntaxException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHttpHeaderIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // accept json check
        $accept = $request->headers->get('Accept');
        if (!$accept || trim($accept, ';') != 'application/json') {
            throw new SyntaxException('headers错误', ['type' => 'Accept']);
        }

        // content-type form check
        if (!in_array($request->method(), ['GET', 'PATCH', 'PUT', 'DELETE']) && $request->getContentTypeFormat() != 'form') {
            throw new SyntaxException('headers错误', ['type' => 'Content-type']);
        }

        return $next($request);
    }
}
