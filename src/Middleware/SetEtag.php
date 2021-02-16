<?php

namespace Werk365\EtagConditionals\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetEtag extends Middleware
{
    public string $middleware = 'setEtag';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(!$request->isMethod('HEAD') && !$request->isMethod('GET') ){
            return  $next($request);
        }

        // Handle request
        $method = $request->getMethod();

        // Support using HEAD method for checking If-None-Match
        if ($request->isMethod('HEAD')) {
            $request->setMethod('GET');
        }

        //Handle response
        $response = $next($request);
        
        // Setting etag
        $etag = md5($response->getContent());
        $response->setEtag($etag);

        $request->setMethod($method);

        return $response;
    }
}
