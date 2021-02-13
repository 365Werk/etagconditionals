<?php

namespace Werk365\EtagConditionals\Middleware;

use Closure;
use Illuminate\Http\Request;
use Werk365\LaravelJsonApi\Middleware\IfNoneMatch;

class SetEtag extends Middleware
{

    public string $middleware = "setEtag";

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        //Handle response
        $response = $next($request);

        // Setting etag
        $etag = md5($response->getContent());
        $response->setEtag($etag);

        return $response;
    }
}
