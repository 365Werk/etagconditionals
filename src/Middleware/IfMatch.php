<?php

namespace Werk365\EtagConditionals\Middleware;

use Closure;
use Illuminate\Http\Request;

class IfMatch extends Middleware
{

    public string $middleware = "ifMatch";

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->isMethod('PATCH') && $request->hasHeader('If-Match')) {
            $controller = $request->route()->action["uses"];
            $controller = explode('@', $controller)[0];
            $method = "show";
            $get = app()->call("$controller@$method", $request->route()->parameters());

            if(isset($get::$wrap)){
                $get = (object) [$get::$wrap => $get];
            }

            $currentEtag = '"' . md5(json_encode($get)) . '"';
            $ifMatch = $request->header('If-Match');

            if($currentEtag !== $ifMatch){
                return response(null, 412);
            }
        }

        return $next($request);
    }

}
