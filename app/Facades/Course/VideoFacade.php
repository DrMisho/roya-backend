<?php

namespace App\Facades\Course;
use Illuminate\Support\Facades\Facade;

class VideoFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'VideoService';
    }
}
