<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Razon_Trabajo extends Model
{
	
    protected $table = 'razontrabajo';
    public $timestamps = false;
    protected $fillable=['tipo','descripcion','descripcion_traduccion'];
    protected $primaryKey = 'id';
    
}