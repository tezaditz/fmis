<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Wilayah;

class WilayahArea extends Model
{
    protected $table = 'wilayah_area';

    public function wilayah()
    {
    	return $this->belongsTo(Wilayah::class , 'wilayah_id' , 'id');
    }
}
