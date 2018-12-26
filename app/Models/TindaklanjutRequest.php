<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Permintaan;

class TindaklanjutRequest extends Model
{
    protected $table = 'tindaklanjut_request';

    public function Permintaan()
    {
    	return $this->belongsTo(Permintaan::class , 'request_id' , 'id');
    }
}
