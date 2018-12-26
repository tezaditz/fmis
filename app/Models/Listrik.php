<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Aset;

class Listrik extends Model
{
    protected $table = 'listrik';

    public function aset()
    {
    	return $this->belongsTo(Aset::class , 'aset_id' , 'id');
    }
}
