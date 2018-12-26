<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    protected $table = 'request';

    public function jenis_complain()
    {
        return $this->belongsTo(Jenis_Complain::class , 'id_jenis_complaint' , 'id');
    }
}
