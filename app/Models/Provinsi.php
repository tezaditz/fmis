<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{
    protected $table = 'provinsi';

    public static function options($id)
    {
        
        return static::where('id', $id)->get()->map(function ($provinsi) {
            return [$provinsi->id => $provinsi->name];
        })->flatten();
    }
}
