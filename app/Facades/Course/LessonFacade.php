<?php

namespace App\Facades\Course;
use Illuminate\Support\Facades\Facade;

class LessonFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'LessonService';
    }
}
