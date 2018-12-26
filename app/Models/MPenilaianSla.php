<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\bulan;
use App\Models\Aset;
use App\Models\WilayahArea;



class MPenilaianSla extends Model
{
    protected $table = 'm_penilaian_sla';

    public function bulan()
    {
    	return $this->belongsTo(bulan::class , 'bulan_id' , 'id');
    }

    public function aset()
    {
    	return $this->belongsTo(Aset::class , 'aset_id' , 'id');
    }

    public function wilayaharea()
    {
    	return $this->belongsTo(WilayahArea::class , 'aset_id' , 'aset_id');
    }
}
