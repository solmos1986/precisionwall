<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'ticket';
    protected $primaryKey = 'ticket_id';
    public $timestamps = false;
    protected $fillable = [
        'ticket_id', 'num', 'foreman_name', 'superintendent_name', 'horario', 'descripcion','firma_cliente','firma_foreman',
        'estado','fecha_ticket','fecha_finalizado', 'pco', 'actividad_id','proyecto_id','empleado_id','delete'
    ];

}