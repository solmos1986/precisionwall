<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    protected $table = "proyectos";

    protected $primaryKey = 'Pro_ID';
    public $timestamps = false;

}