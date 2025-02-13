<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket_material extends Model
{
    protected $table = 'ticket_material';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'cantidad', 'material_id', 'ticket_id'
    ];
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }
}
