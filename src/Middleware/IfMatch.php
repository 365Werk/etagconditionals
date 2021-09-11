<?php

namespace Werk365\EtagConditionals\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Werk365\EtagConditionals\EtagConditionals;

class IfMatch extends Middleware
{
    public string $middleware = 'ifMatch';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Next unless method is PATCH and If-Match header is set
        if ((! ($request->isMethod('PATCH') && $request->hasHeader('If-Match')))
            || $request->hasHeader('X-From-Middleware')) {
            return $next($request);
        }

        // Create new GET request to same endpoint,
        // copy headers and add header that allows you to ignore this request in middlewares
        $getRequest = Request::create($request->getRequestUri(), 'GET');
        $getRequest->headers = $request->headers;
        $getRequest->headers->set('X-From-Middleware', 'IfMatch');
        $getResponse = app()->handle($getRequest);

        // Get content from response object and get hashes from content and etag
        $getEtag = EtagConditionals::getEtag($request, $getResponse);
        $ifMatch = $request->header('If-Match');

        if ($ifMatch === null) {
            return response(null, 412);
        }

        $ifMatchArray = (is_string($ifMatch)) ?
            explode(',', $ifMatch) :
            $ifMatch;

        // Strip W/ if weak comparison algorithm can be used
        if (config('etagconditionals.if_match_weak')) {
            foreach ($ifMatchArray as &$match) {
                $match = str_replace('W/', '', $match);
            }
            unset($match);
        }

        foreach ($ifMatchArray as &$match) {
            $match = trim($match);
        }
        unset($match);

        // Compare current and request hashes
        // Also allow wildcard (*) values
        if (! (in_array($getEtag, $ifMatchArray) || in_array('"*"', $ifMatchArray))) {
            return response(null, 412);
        }

        $request->headers->set('X-From-Middleware','If-Match');
        return app()->handle($request);
    }
}
