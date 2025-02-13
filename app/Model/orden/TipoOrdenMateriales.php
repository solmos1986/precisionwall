<?php

namespace App\Model\orden;

use Illuminate\Database\Eloquent\Model;

class TipoOrdenMateriales extends Model
{
    protected $table = 'tipo_orden_materiales';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
    'material_id',
    'tipo_orden_id',
    'cant_ordenada',
    'cant_sitio_trab',
    'entregado',
    ];
}
