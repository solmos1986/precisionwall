<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Evaluacion extends Model
{
    protected $table = 'evaluation_form';
    protected $primaryKey = 'evaluation_form_id';
    public $timestamps = false;
    protected $fillable = [
        'titulo',
        'descripcion',
    ];

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'evaluation_question', 'evaluation_id', 'question_id');
    }

    public function areas()
    {
        return $this->belongsToMany(HowAreas::class, 'evaluation_area', 'evaluation_id', 'how_area_id');
    }
}