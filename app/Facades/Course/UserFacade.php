<?php

namespace App\Facades\Course;
use Illuminate\Support\Facades\Facade;

class CourseFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'CourseService';
    }
}
