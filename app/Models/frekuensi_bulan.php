<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Frekuensi;

class frekuensi_bulan extends Model
{
    protected $table = 'frekuensi_bulan';

    public function Frekuensi()
    {
    	$this->belongsToMany( Frekuensi::class );
    }
}
