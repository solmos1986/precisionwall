<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Orden extends Model
{
    protected $table = 'orden';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['id','num','job_name','sub_contractor',
    'sub_empleoye_id', 'proyecto_id','estado','date_order','date_work','fecha_firm_installer',
    'fecha_firm_foreman','firma_installer','firma_foreman','delete','created_by'];
}
