<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Respuesta_Personal_Evaluacion extends Model
{
    protected $table = 'respuestas_personal_evaluaciones';
    protected $primaryKey = 'respuestas_personal_evaluaciones_id';
    public $timestamps = false;
    protected $fillable = [
        'form_respuesta_id',
        'personal_evaluaciones_id',
        'respuesta',
        'estado'
    ];
    public function respuesta_personal_evaluaciones()
    {
        return $this->belongsTo(Form_Respuesta::class, 'form_respuesta_id');
    }
}
