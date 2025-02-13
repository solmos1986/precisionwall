<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Personal_evaluaciones extends Model
{
    protected $table = 'personal_evaluaciones';
    protected $primaryKey = 'personal_evaluaciones_id';
    public $timestamps = false;
    protected $fillable = [
        'evaluacion_id',
        'Empleado_ID'
    ];
}
