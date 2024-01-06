<?php

namespace App\Facades\Subscription;
use Illuminate\Support\Facades\Facade;

class SubscriptionFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'SubscriptionService';
    }
}
