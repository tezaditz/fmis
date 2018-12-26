<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Aset;

class Solar extends Model
{
    protected $table = 'solar';

    public function aset()
    {
    	return $this->belongsTo(Aset::class , 'aset_id' , 'id');
    }
}
