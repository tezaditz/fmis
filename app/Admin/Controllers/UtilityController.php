<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Chart\Bar;
use Encore\Admin\Widgets\Chart\Doughnut;
use Encore\Admin\Widgets\Chart\Line;
use Encore\Admin\Widgets\Chart\Pie;
use Encore\Admin\Widgets\Chart\PolarArea;
use Encore\Admin\Widgets\Chart\Radar;
use Encore\Admin\Widgets\Collapse;
use Encore\Admin\Widgets\InfoBox;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Widgets\Table;

use App\Models\bulan;
use App\Models\Parameter;
use App\Models\RekapPemakaian;

use Illuminate\Http\Request;
use PDF;

class UtilityController extends Controller
{
    use ModelForm;

    public function index()
    {
    	return Admin::content(function (Content $content) {
    		$content->header('Summary Report Utility');


    		$content->row(function ($rows){
    			$rows->column( 12 , function ($column){

    				$bulan = bulan::all();
    				$param = parameter::where('id' , 1)->get();
    				$tahun = $param[0]['value'];

    				$column->append(( new Box('Report' , view('form_report.UtilityReport' , compact('bulan' , 'tahun') )))->removable()->collapsable());
    			});
    			
    		});
    	});
    }

    public function print(Request $request)
    {


    	$RekapPemakaian = RekapPemakaian::where('tahun' , $request->tahun)
    										->where('jenis_pemakaian' , $request->type)
    										->get();

    	$Utility = $request->type;
    	$tahun	=	$request->tahun;
    	

    	$pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'images' => true])->loadView('report.utility.rekap-utility' ,
         compact('RekapPemakaian' , 'Utility' , 'tahun') )->setPaper('A4' , 'portrait');

        // return view('report.penilaian_sla');
        return $pdf->stream('rekap-utility.pdf');
    }




}
