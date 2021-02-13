<?php

namespace Werk365\EtagConditionals\Facades;

use Illuminate\Support\Facades\Facade;

class EtagConditionals extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'etagconditionals';
    }
}
