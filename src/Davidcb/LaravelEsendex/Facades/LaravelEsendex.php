<?php

namespace Davidcb\LaravelEsendex\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelEsendex extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-esendex';
    }
}
