<?php

namespace Werk365\EtagConditionals\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

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
        if (!$request->isMethod('PATCH') || (!$request->isMethod('PATCH') && !$request->hasHeader('If-Match'))) {
            return $next($request);
        }

        // Get action from request
        $action = $request->route()->action['uses'];

        // Check if action is controller or closure
        if (is_string($action)) { // Controller
            $controller = explode('@', $action)[0];
            $method = 'show';
            $response = app()->call("$controller@$method", $request->route()->parameters());
        } else { // Closure
            $response = app()->call($action);
        }

        // Check if response is object
        if (!is_object($response)) {
            $response = response($response);
        }

        // Handle JsonResource responses
        if (is_a($response, JsonResource::class)) {
            $response = $response->response();
        }

        // Get content from response object and get hashes from content and etag
        $content = $response->getContent();
        $currentEtag = '"' . md5($content) . '"';
        $ifMatch = $request->header('If-Match');

        // Compare current and request hashes
        if ($currentEtag !== $ifMatch) {
            return response(null, 412);
        }

        return $next($request);
    }
}
