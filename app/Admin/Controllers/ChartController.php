<?php

namespace App\Admin\Controllers;


use App\Charts\listrik;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Charts;
use App\Models\RekapPemakaian;

class ChartController extends Controller
{
	public function index()
	{
		$data = RekapPemakaian::where('jenis_pemakaian' , 'listrik')->get();
		$chart = Charts::create('pie', 'highcharts')
			    ->title('My nice chart')
			    ->labels(['First', 'Second', 'Third'])
			    ->values([5,10,20])
			    ->dimensions(1000,500)
			    ->responsive(false);

		$chart2 = Charts::multi('areaspline', 'highcharts')
		    ->title('My nice chart')
		    ->colors(['#ff0000', '#ffffff'])
		    ->labels(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday','Saturday', 'Sunday'])
		    ->dataset('John', [3, 4, 3, 5, 4, 10, 12])
		    ->dataset('Jane',  [1, 3, 4, 3, 3, 5, 4]);

		return view('admin.charts.chart',compact('chart' , 'chart2'));
	}
	
}
