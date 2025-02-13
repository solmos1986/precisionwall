<?php

namespace App\Modules\UserModules;

use App\Queries\UserQuery;

class UserService
{
    use UserQuery;
    /**
     * test
     *  This is method test
     * @return string
     */
    public function test()
    {
        return 'alerta de  prueba';
    }
    public function alert()
    {
        return $this->getAlert("fdfs");
    }
}
