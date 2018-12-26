<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use App\Models\Waktu;
use App\Models\bulan;
use App\Models\Minggu;
use App\Models\Hari;


class Frekuensi extends Model
{
    protected $table = 'frekuensi';


    public function bulan() 
    {
    	return $this->belongsToMany(bulan::class , 'frekuensi_bulan' , 'frekuensi_id' , 'bulan_id');
    }

    public function minggu() 
    {
    	return $this->belongsToMany(Minggu::class , 'frekuensi_minggu' , 'frekuensi_id' , 'minggu_id');
    }

    public function hari() 
    {
    	return $this->belongsToMany(Hari::class , 'frekuensi_hari' , 'frekuensi_id' , 'hari_id');
    }
}
