<?php

namespace Werk365\EtagConditionals\Middleware;

class Middleware
{
    public string $middleware;

    public function name(): string
    {
        return $this->middleware;
    }
}
