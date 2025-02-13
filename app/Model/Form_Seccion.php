<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Form_Seccion extends Model
{
    protected $table = 'form_seccion';
    protected $primaryKey = 'form_seccion_id';
    public $timestamps = false;
    protected $fillable = [
        'descripcion',
        'subtitulo',
        'formulario_id',
        'estado'
    ];
    public function preguntas()
    {
        return $this->hasMany(Form_Pregunta::class, 'form_pregunta_id');
    }
    public function formulario()
    {
        return $this->belongsTo(Form_Formulario::class, 'formulario_id');
    }
}
