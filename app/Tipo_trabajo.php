<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tipo_trabajo extends Model
{
    protected $table = 'tipo_trabajo';
    public $timestamps = false;
    protected $fillable=['nombre','descripcion'];
    protected $primaryKey = 'id';
}
