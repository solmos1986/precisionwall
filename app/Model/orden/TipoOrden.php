<?php

namespace App\Model\orden;

use Illuminate\Database\Eloquent\Model;

class TipoOrden extends Model
{
    protected $table = 'tipo_orden';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
    'sub_contractor_id',
    'sub_empleoye_id',
    'proyecto_id',
    'num',
    'estado_orden',
    'nombre_trabajo',
    'estado',
    'fecha_order',
    'fecha_trabajo',
    'fecha_entrega',
    'fecha_foreman',
    'firma_installer',
    'firma_foreman',
    'eliminado',
    'creado_por',
    ];
}
