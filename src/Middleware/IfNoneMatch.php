<?php

namespace Werk365\EtagConditionals\Middleware;

use Closure;
use Illuminate\Http\Request;
use Werk365\EtagConditionals\EtagConditionals;

class IfNoneMatch extends Middleware
{
    public string $middleware = 'ifNoneMatch';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Handle request
        $method = $request->getMethod();

        // Support using HEAD method for checking If-None-Match
        if ($request->isMethod('HEAD')) {
            $request->setMethod('GET');
        }

        //Handle response
        $response = $next($request);

        $etag = EtagConditionals::getEtag($request, $response);
        $noneMatch = $request->getETags();

        // Strip W/ if weak comparison algorithm can be used
        if (config('etagconditionals.if_none_match_weak')) {
            $noneMatch = array_map([$this, 'stripWeakTags'], $noneMatch);
        }

        if (in_array($etag, $noneMatch)) {
            $response->setNotModified();
        }

        $request->setMethod($method);

        return $response;
    }

    private function stripWeakTags($etag)
    {
        return str_replace('W/', '', $etag);
    }
}
