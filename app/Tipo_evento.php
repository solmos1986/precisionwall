<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tipo_evento extends Model
{
    protected $table = 'tipo_evento';
    protected $primaryKey = 'tipo_evento_id';
    public $timestamps = false;
    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];
    public function eventos()
    {
        return $this->hasMany(Evento::class, 'tipo_evento_id');
    }
}
