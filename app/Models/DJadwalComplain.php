<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\complaint;
use App\Models\Tindaklanjut;
use App\Models\MJadwalComplain;

class DJadwalComplain extends Model
{
    protected $table = 'd_jadwalcomplain';

    public function complain()
    {
    	return $this->belongsTo(complaint::class , 'complain_id' ,'id');
    }

    public function tindaklanjut()
    {
    	return $this->belongsTo(Tindaklanjut::class , 'tindaklanjut_id' , 'id');
    }

    public function MJadwalComplain()
    {
    	return $this->belongsTo(MJadwalComplain::class , 'm_jadwalcomplain_id' , 'id');
    }
}
