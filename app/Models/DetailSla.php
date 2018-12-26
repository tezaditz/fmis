<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sla;

class DetailSla extends Model
{
    protected $table = 'detail_sla';

    public function sla()
    {
    	return $this->belongsTo(Sla::class, 'sla_id' , 'id');
    }

    public static function options($id)
    {
        
    }
}
