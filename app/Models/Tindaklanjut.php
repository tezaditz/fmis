<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Complaint;

class Tindaklanjut extends Model
{
    protected $table = 'tindaklanjut';

    public function complain()
    {
    	return $this->belongsTo(Complaint::class , 'complain_id' , 'id');
    }
}
