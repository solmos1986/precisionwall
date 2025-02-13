<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Formulario_evaluaciones extends Model
{
    protected $table = 'formulario_evaluaciones';
    protected $primaryKey = 'formulario_evaluacion_id';
    public $timestamps = false;
    protected $fillable = [
        'formulario_id',
        'evaluacion_id'
    ];
}


