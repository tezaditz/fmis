<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\WilayahArea;

class Aset extends Model
{
    protected $table = 'aset';

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class , 'provinsi_id' , 'id');
    }

    public function kota()
    {
        return $this->belongsTo(Kota::class , 'kota_id' , 'id');
    }

    public function WilayahArea()
    {
    	return $this->belongsTo(WilayahArea::class , 'wilayah_area_id' , 'id');
    }
}
