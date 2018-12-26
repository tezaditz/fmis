<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Sla;
use App\Models\Aset;


class SlaAset extends Model
{
    protected $table = 'slaaset';

    public function aset()
    {
    	return $this->belongsTo(Aset::class , 'aset_id'  , 'id');
    }

    public function sla()
    {
    	return $this->belongsTo(Sla::class , 'sla_id'  , 'id');
    }

    public static function options($id)
    {
        return static::where('id', $id)->get()->map(function ($kota) {
            return [$kota->id => $kota->name];
        })->flatten();
    }
}
