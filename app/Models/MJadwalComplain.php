<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Complaint;
use App\Models\TindakLlanjut;

class MJadwalComplain extends Model
{
    protected $table = 'm_jadwalcomplain';

    public function complain()
    {
    	return $this->belongsTo(Complaint::class , 'complain_id' , 'id');
    }

    public function tindaklanjut()
    {
    	return $this->belongsTo(Tindaklanjut::class , 'complain_id' , 'complain_id');
    }
}
