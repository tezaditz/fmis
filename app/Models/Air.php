<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Aset;

class Air extends Model
{
    protected $table = 'air';

    public function aset()
    {
    	return $this->belongsTo(Aset::class , 'aset_id' , 'id');
    }
}
