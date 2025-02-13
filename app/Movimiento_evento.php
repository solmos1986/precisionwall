<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Movimiento_evento extends Model
{
    protected $table = 'movimientos_eventos';
    protected $primaryKey = 'movimientos_eventos_id';
    public $timestamps = false;
    protected $fillable = [
        'cod_evento',
        'Empleado_ID',
        'start_date',
        'exp_date',
        'note',
        'raise_from',
        'raise_to'
    ];
    public function eventos()
    {
        return $this->belongsTo(Evento::class, 'cod_evento');
    }
}
