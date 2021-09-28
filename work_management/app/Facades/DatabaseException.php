<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class DatabaseException extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'exception';
    }
}