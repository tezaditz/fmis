<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Akun;
use App\Models\Subakun;

class Transaksi_Pengeluaran extends Model
{
    protected $table = 'transaksi_pengeluaran';


    public function akun()
    {
    	return $this->belongsTo(Akun::class , 'akun_id' , 'id');
    }

    public function subakun()
    {
    	return $this->belongsTo(Subakun::class , 'subakun_id' , 'id');
    }
    
}
