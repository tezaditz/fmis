<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class sla extends Model
{
    protected $table = 'sla';

    public static function options($id)
    {
        
        return static::where('id', $id)->get()->map(function ($sla) {
            return [$sla->id => $sla->uraian];
        })->flatten();
    }

    
}
