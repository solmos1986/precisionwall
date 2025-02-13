<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HowAreas extends Model
{
    protected $table = "how_areas";
    protected $primaryKey = 'how_areas_id';
    public $timestamps = false;
    protected $fillable = [
        'nombre', 'descripcion',
    ];
}