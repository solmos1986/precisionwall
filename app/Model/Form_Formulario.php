<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Form_Formulario extends Model
{
    protected $table = 'form_formulario';
    protected $primaryKey = 'formulario_id';
    public $timestamps = false;
    protected $fillable = [
        'titulo',
        'fecha_creacion',
        'Empleado_ID',
        'descripcion',
        'estado'
    ];
    public function secciones()
    {
        return $this->hasMany(Form_Seccion::class, 'formulario_id');
    }
    /*public function movimiento_eventos()
    {
        return $this->hasMany(Tipo_evento::class, 'cod_evento');
    }*/
}
