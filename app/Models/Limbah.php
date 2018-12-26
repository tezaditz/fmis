<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Aset;

class Limbah extends Model
{
    protected $table = 'limbah';

    public function aset()
    {
    	return $this->belongsTo(Aset::class , 'aset_id' , 'id');
    }
}
