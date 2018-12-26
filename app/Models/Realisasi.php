<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Realisasi extends Model
{
    protected $table = 'anggaran';

     public function akun()
    {
    	return $this->belongsTo(Akun::class , 'akun_id' , 'id');
    }

    public function subakun()
    {
    	return $this->belongsTo(Subakun::class , 'subakun_id' , 'id');
    }
}
