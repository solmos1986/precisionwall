<?php

namespace App\Model\orden;

use Illuminate\Database\Eloquent\Model;

class TipoOrdenMaterialesRecojerEquipo extends Model
{
    protected $table = 'tipo_orden_materiales_material';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
    'tipo_orden_materiales_id',
    'cant_instalada',
    'fecha_instalada',
    'cant_restante',
    'cant_almacenada',
    ];
}
