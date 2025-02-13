<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Images_goal extends Model
{
    protected $table = 'goal_imagen';
    protected $primaryKey = 't_imagen_id';
    public $timestamps = false;
    protected $fillable = [
        't_imagen_id ', 
        'imagen', 
        'tipo', 
        'caption',
        'size',
        'id_informe_proyecto',
    ];

    public function informe_proyecto(){
        return $this->belongsTo(Informe_proyecto::class,'Informe_ID');
    }
}
