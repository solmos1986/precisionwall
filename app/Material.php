<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table = 'materiales';
    protected $primaryKey = 'Mat_ID';
    public $timestamps = false;
    protected $fillable = [
        'Pro_ID', 'Denominacion', 'Unidad_Medida', 'Precio_Unitario'
    ];
}