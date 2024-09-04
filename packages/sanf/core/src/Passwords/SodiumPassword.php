<?php

namespace Sanf\Core\Passwords;

use Illuminate\Support\Facades\Facade;

/**
 * @see Sanf\Core\Passwords\SodiumPasswordBrokerManager
 */
class SodiumPassword extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'sodiumPasswordBrokerManager';
    }
}
