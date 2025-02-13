<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Empresas extends Model
{
    protected $table = 'empresas';
    protected $primaryKey = 'Emp_ID';
    public $timestamps = false;
    protected $fillable = [
        'Codigo',
        'Nombre',
        'Estado',
        'Ciudad',
        'Zip_Code',
        'Calle',
        'Numero',
        'Gerente_General',
        'Telefono',
        'Fax',
        'Web',
        'email',
        'Rubro',
        'Detalles',
    ];
    public function empleados()
    {
        return $this->hasMany(Personal::class, 'Empleado_ID');
    }
}
