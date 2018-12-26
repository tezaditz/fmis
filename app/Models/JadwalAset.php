<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Aset;
use App\Models\JadwalTindakLanjut;

class JadwalAset extends Model
{
    protected $table = 'jadwal_sla_aset';

    public function aset()
    {
    	return $this->belongsTo(Aset::class , 'aset_id' , 'id');
    }

    public function JadwalTindakLanjut()
    {
    	return $this->hasMany(JadwalTindakLanjut::class , 'jadwal_sla_aset' , 'id');
    }

}
