<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Informe_proyecto extends Model
{
    protected $table = 'informe_proyecto';
    protected $primaryKey = 'Informe_ID';
    public $timestamps = false;
    protected $fillable = [
        'Codigo',
        'Informe_ID',
        'Pro_ID',
        'Fecha',
        'Check_status',
        'Check_coming',
        'Date_Check_coming',
        'Check_framing',
        'Date_Check_framing',
        'Check_hanging',
        'Date_Check_hanging',
        'Check_construction',
        'Check_hidden',
        'hidden_yes_no',
        'hidden_options',
        'rooms',
        'others',
        'Check_we_can',
        'Date_estimate',
        'Date_actual',
        'DAte_finally',
        'Check_quality',
        'text_Check_quality	',
        'Check_discuse',
        'text_Check_discuse',
        'Check_control',
        'text_Check_control',
        'pwt_actual',
        'pwt_quality',
        'pwt_production_rate',
        'pwt_painters',
        'pwt_apprentices',
        'pwt_comments',
        'pwt_action',
        'pwt_miscellaneous',
        'gc	',
        'gc_action',
        'quality',
        'quality_comments',
        'quality_action_taken',
        'Drywall',
        'Drywall_comments',
        'Drywall_action_taken',
        'delete_informe_proyecto',
        'estado',
    ];
    public function images()
    {
        return $this->hasMany(Images_goal::class, 'id_informe_proyecto');
    }
}
