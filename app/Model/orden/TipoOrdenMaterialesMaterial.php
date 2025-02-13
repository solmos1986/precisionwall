<?php

namespace App\Model\orden;

use Illuminate\Database\Eloquent\Model;

class TipoOrdenMaterialesMaterial extends Model
{
    protected $table = 'tipo_orden_materiales_recojer_equipo';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
    'tipo_orden_materiales_id',
    'estatus',
    'fecha',
    'cant_dia',
    ];
}
