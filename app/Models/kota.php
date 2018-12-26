<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Provinsi;

class kota extends Model
{
    protected $table = 'kota';

    public function provinsi()
    {
    	return $this->belongsTo(Provinsi::class , 'provinsi_id' , 'id');
    }

    public static function options($id)
    {
        
        return static::where('id', $id)->get()->map(function ($kota) {
            return [$kota->id => $kota->name];
        })->flatten();
    }
}
