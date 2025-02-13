<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Form_Respuesta extends Model
{
    protected $table = 'form_respuestas';
    protected $primaryKey = 'form_respuesta_id';
    public $timestamps = false;
    protected $fillable = [
        'val',
        'valor',
        'form_pregunta_id',
        'estado'
    ];
    public function respuestas_personal_evaluaciones()
    {
        return $this->hasMany(Respuesta_Personal_Evaluacion::class, 'form_respuesta_id');
    }
    public function pregunta()
    {
        return $this->belongsTo(Form_Pregunta::class, 'form_pregunta_id');
    }
}
