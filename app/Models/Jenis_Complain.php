<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jenis_Complain extends Model
{
    protected $table = 'jenis_complaint';


    public static function options($id)
    {
        
        return static::where('id', $id)->get()->map(function ($jenis_complaint) {
            return [$jenis_complaint->id => $jenis_complaint->uraian];
        })->flatten();
    }
}
