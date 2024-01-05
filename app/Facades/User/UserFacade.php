<?php

namespace App\Facades\User;
use Illuminate\Support\Facades\Facade;

class UserFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'UserService';
    }
}
