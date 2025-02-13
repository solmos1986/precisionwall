<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    protected $table = 'evento';
    protected $primaryKey = 'cod_evento';
    public $timestamps = false;
    protected $fillable = [
        'nombre',
        'descripcion',
        'duracion_day',
        'note',
        'access_code',
        'access_pers',
        'report_alert',
        'tipo_evento_id',
    ];
    public function tipo_evento()
    {
        return $this->belongsTo(Tipo_evento::class, 'tipo_evento_id');
    }
    public function movimiento_eventos()
    {
        return $this->hasMany(Tipo_evento::class, 'cod_evento');
    }
}
