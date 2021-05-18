<?php

namespace Werk365\EtagConditionals;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class EtagConditionals
{
    /**
     * The callback used to generate the ETag.
     *
     * @var \Closure|null
     */
    protected static $etagGenerateCallback;

    /**
     * Set a callback that should be used when generating the ETag.
     *
     * @param  \Closure|null  $callback
     * @return void
     */
    public static function etagGenerateUsing(?Closure $callback): void
    {
        static::$etagGenerateCallback = $callback;
    }

    /**
     * Get ETag value for this request and response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return string
     */
    public static function getEtag(Request $request, Response $response): string
    {
        if (static::$etagGenerateCallback) {
            $etag = call_user_func(static::$etagGenerateCallback, $request, $response);
        } else {
            $etag = static::defaultGetEtag($response);
        }

        return (string) Str::of($etag)->start('"')->finish('"');
    }

    /**
     * Get default ETag value.
     *
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return string
     */
    private static function defaultGetEtag(Response $response): string
    {
        return md5($response->getContent());
    }
}
