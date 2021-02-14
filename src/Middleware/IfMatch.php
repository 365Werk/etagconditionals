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
        if ($request->isMethod('PATCH') && $request->hasHeader('If-Match')) {
            $controller = $request->route()->action['uses'];

            // Check if controller or closure should be called
            if (is_string($controller)) { // Controller
                $controller = explode('@', $controller)[0];
                $method = 'show';
                $get = app()->call("$controller@$method", $request->route()->parameters());
            } else { // Closure
                $get = app()->call($controller);
            }

            // Handle JsonResource responses
            if (is_a($get, JsonResource::class)) {
                $get = json_encode((object) [$get::$wrap => $get]);
            }

            // Handle regular responses
            if(is_a($get, Response::class)){
                $get = $get->getContent();
            }

            $currentEtag = '"'.md5($get).'"';
            $ifMatch = $request->header('If-Match');
            
            if ($currentEtag !== $ifMatch) {
                return response(null, 412);
            }
        }

        return $next($request);
    }
}