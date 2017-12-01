<?php

namespace Davidcb\Esendex\Facades;

use Illuminate\Support\Facades\Facade;

class Esendex extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'esendex';
    }
}
