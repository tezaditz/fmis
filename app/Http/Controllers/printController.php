<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;

class printController extends Controller
{
    public function print()
    {
        // $DataComplain   = Complaint::where('id' , $id)->get();
        // $DataAset       = Aset::where('id' , $DataComplain[0]['aset_id'])->get();
        // $DataArea       = WilayahArea::where('id' , $DataAset[0]['wilayah_area_id'])->get();
        // $area           = $DataArea[0]['nama_area'];
    	return view('report.printtest');

  //       $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('report.printtest')->setPaper('A4' , 'landscape');

		// return $pdf->stream('complain');
    }
}
