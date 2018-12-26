<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Aset;

class MSettingJadwal extends Model
{
    protected $table = 'master_settingjadwal';

    public function aset()
    {
    	return $this->belongsTo(Aset::class , 'aset_id' , 'id');
    }
}
