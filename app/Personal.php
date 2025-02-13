<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Personal extends Model
{
    protected $table = 'personal';
    protected $primaryKey = 'Empleado_ID';
    protected $fillable = [
        'Emp_ID ',
        'Nombre',
        'Apellido_Paterno',
        'Apellido_Materno',
        'Nick_Name',
        'Estado',
        'Ciudad',
        'Zip_Code',
        'Calle',
        'Numero',
        'Cargo',
        'Numero_Seguro_Social',
        'Fecha_Nacimiento',
        'Numero_Licencia_Conducir',
        'Numero_Permiso_Trabajo',
        'Fecha_Expiracion_Trabajo',
        'Numero_Residente',
        'email',
        'Telefono',
        'Celular',
        'Aux1',
        'Aux2',
        'Aux3',
        'Aux4',
        'Aux5',
        'Usuario',
        'Password',
        'P1',
        'R1',
        'P2',
        'R2',
        'P3',
        'R3',
        'Indice_produccion',
        'Nro_Bono',
        'Spec_Bon1',
        'Extra_Mon1',
        'Benefit1',
        'Benefit2',
        'empresa',
        'Rol_ID',
        'status',
    ];
    public $timestamps = false;

    public function empresa()
    {
        return $this->belongsTo(Empresas::class, 'Emp_ID');
    }
}
