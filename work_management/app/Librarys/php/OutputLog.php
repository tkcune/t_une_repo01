<?php

namespace App\Librarys\php;

use Illuminate\Support\Facades\Facade;

//OutputLogClassのファザードクラス
class OutputLog extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'OutputLogClass';
    }
}

?>
