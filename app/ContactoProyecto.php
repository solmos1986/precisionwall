<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContactoProyecto extends Model
{
    protected $table = 'contacto_proyecto';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'Pro_ID', 'Empleado_ID', 'tipo_contacto'
    ];
}
