<?php

namespace Tiagofv\ZApi;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Tiagofv\ZApi\ZApi
 */
class ZApiFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'zapi';
    }
}
