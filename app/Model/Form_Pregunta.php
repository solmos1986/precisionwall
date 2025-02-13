<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Form_Pregunta extends Model
{
    protected $table = 'form_pregunta';
    protected $primaryKey = 'form_pregunta_id';
    public $timestamps = false;
    protected $fillable = [
        'pregunta',
        'tipo',
        'form_seccion_id',
        'estado'
    ];
    public function respuestas()
    {
        return $this->hasMany(Form_Respuesta::class, 'form_pregunta_id');
    }
    public function seccion()
    {
        return $this->belongsTo(Form_Seccion::class, 'form_seccion_id');
    }
}
