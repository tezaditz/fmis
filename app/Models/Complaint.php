<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Aset;
use App\Models\Jenis_Complain;
use App\Models\Status;

class Complaint extends Model
{
    protected $table = 'complaint';

    public function jenis_complain()
    {
        return $this->belongsTo(Jenis_Complain::class , 'id_jenis_complaint' , 'id');
    }

    public function aset()
    {
    	return $this->belongsTo(Aset::class , 'aset_id' , 'id');
    }

    public function status()
    {
    	return $this->belongsTo(Status::class , 'status_id' , 'id');
    }
}
