<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Evaluaciones extends Model
{
    protected $table = 'evaluaciones';
    protected $primaryKey = 'evaluacion_id';
    public $timestamps = false;
    protected $fillable = [
        'foreman_id',
        'note',
        'fecha_asignacion',
        'estado'
    ];
    public function personales()
    {
        return $this->hasMany(Personal_evaluaciones::class, 'evaluacion_id');
    }
}
