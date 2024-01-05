<?php

namespace App\Facades\Course;
use Illuminate\Support\Facades\Facade;

class QuizFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'QuizService';
    }
}
