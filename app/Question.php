<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $table = 'question';
    protected $primaryKey = 'question_id';
    public $timestamps = false;
    protected $fillable = [
        'question_id', 'nombre', 'descripcion','t_input'
    ];

    public function options()
    {
        return $this->hasMany(SubQuestion::class, 'question_id');
    }
}