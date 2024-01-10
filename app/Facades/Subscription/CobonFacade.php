<?php

namespace App\Facades\Subscription;
use Illuminate\Support\Facades\Facade;

class CobonFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'CobonService';
    }
}
