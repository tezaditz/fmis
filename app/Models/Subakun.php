<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Akun;


class Subakun extends Model
{
    protected $table = 'subakun';

    public function akun()
    {
    	return $this->belongsTo(Akun::class , 'akun_id' , 'id');    }
}
