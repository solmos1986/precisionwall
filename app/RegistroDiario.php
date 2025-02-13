<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RegistroDiario extends Model
{
    protected $table = "registro_diario";

    protected $primaryKey = 'Reg_ID';
    public $timestamps = false;
    protected $fillable = [
       'Actividad_Id',
       'Pro_ID',
       'Empleado_ID',
       'Hora_Ingreso',
       'Fecha_Hingreso',
       'Hora_Salida',
       'Fecha_Hsalida',
       'Fecha',
       'Latitud_Ingreso',
       'Longitud_Ingreso',
       'Latitud_Salida',
       'Longitud_Salida',
       'Foto_Ingreso',
       'Foto_Salida',
       'Clave_Digitada_In',
       'Clave_Digitada_Out',
       'Pregunta_IN',
       'Pregunta_OUT',
       'Aux1',
       'Aux2',
    ];
}
