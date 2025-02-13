<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubQuestion extends Model
{
    protected $table = 'sub_question';
    protected $primaryKey = 'sub_question_id';
    public $timestamps = false;
    protected $fillable = [
        'titulo', 'question_id',
    ];
}