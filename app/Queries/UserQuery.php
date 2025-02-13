<?php

namespace App\Queries;

use App\Movimiento_evento;

trait UserQuery
{
    public function getAlert($user_id)
    {
        return Movimiento_evento::where('cod_evento', '11')->get();
    }
}
