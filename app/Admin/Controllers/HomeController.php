<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
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
use Encore\Admin\Widgets\Alert;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Widgets\Table;

use Encore\Admin\Grid;
use App\Models\Complaint;
use App\Models\Wilayah;
use App\Models\WilayahArea;
use App\Models\RekapPemakaian;
use App\Models\RekapWilayah;
use App\Models\Parameter;
use App\Models\Aset;

use Carbon\Carbon;
use PDF;
use DB;

class HomeController extends Controller
{
    public function index()
    {
        $roles      = Admin::user()->roles;
        $idRoles    = $roles[0]['id'];
        $user           = Admin::user();
        $idGroupArea    = $user['groupArea'];
        $idGroupWill    = $user['groupWil'];
        $idAset         = $user['aset_id'];

        
        
        if($idRoles == 4)
        {
            $DataWill = WilayahArea::where('wilayah_id' , $idGroupWill)->get(['id']);

            $DataAset = Aset::whereIn('wilayah_area_id' , $DataWill)->get(['id']);
            
                        return Admin::content(function (Content $content) use ($DataWill , $DataAset) {
           
                $content->header('Dashboard');          
                $content->row(function ($row) use ($DataAset) {
                $DataComplain       = Complaint::where('status_id' , 1)
                                        ->WhereIn('aset_id' , $DataAset)->get();
               
                $TotalComplain      = Count($DataComplain);
                $DataOnProgress     = Complaint::where('status_id' , 2)
                                        ->WhereIn('aset_id' , $DataAset)->get();
                $TotalOnProgress    = Count($DataOnProgress);
                $DataOutStanding     = Complaint::where('status_id' , 3)
                                        ->WhereIn('aset_id' , $DataAset)->get();
                $TotalOutStanding    = Count($DataOutStanding);

                $row->column(4, new InfoBox('Complain', 'list', 'red', '/admin/tindaklanjuts', $TotalComplain));
                $row->column(4, new InfoBox('On Progress Complain', 'list', 'yellow', '/admin/tindaklanjuts', $TotalOnProgress ));
                $row->column(4, new InfoBox('Out Standing Complain', 'list', 'green', '/admin/tindaklanjuts', $TotalOutStanding));
                });  

                $bulan = ['Jan' , 'Feb' , 'Mar' , 'Apr' , 'Mei' , 'Jun' , 'Jul' , 'Ags' , 'Sep' , 'Okt' , 'Nov' , 'Des'];
                $param = Parameter::where('id' , 1)->get();
                $warna = ['red' , 'blue' , 'yellow' , 'green' , 'orange' , 'grey' , 'black'];


                $content->row(function ($row) use ($bulan , $param , $warna , $DataWill , $DataAset) {
                //listrik
                $row->column(6 , function ($column) use ($bulan , $param , $warna , $DataWill , $DataAset) {

                    // $Data = RekapWilayah::where('jenis_pemakaian' , 'Listrik')
                    //                     ->where('tahun' , $param[0]['value'] )
                    //                     ->get();

                    $Data = RekapPemakaian::whereIn('aset_id' , $DataAset)
                                        ->where('jenis_pemakaian' , 'Listrik')
                                        ->where('tahun' , $param[0]['value'] )
                                        ->get();

                    $seriesArray = [];
                    $i = 0;
                    foreach ($Data as $key => $value) {
                        $seriesArray[$i] = array(
                            'name'  => $value->nama_aset,
                            'data'  => [$value->jan , $value->feb , $value->mar , $value->apr , $value->may , $value->jun , $value->jul , $value->aug , $value->sep , $value->oct ,$value->nov , $value->dec]
                        );
                        $i = $i + 1;
                    }

                    

                    $chart1 = \Chart::title(['text' => 'Penggunaan Listrik Period ' . $param[0]['value'] ,])
                        ->chart([
                            'type'     => 'line', // pie , columnt ect
                            'renderTo' => 'chart1', // render the chart into your div with id
                        ])
                        ->subtitle([
                            'text' => '',
                        ])
                        ->xaxis([
                            'categories' => $bulan,
                        ])
                        ->yaxis([
                            'title' => 'text:a'

                        ])
                        ->series(
                            $seriesArray
                        )
                        ->display();

                    $column->append(( new Box('Penggunaan Listrik' , view('admin.charts.chart' , compact('chart1'))))->removable()->collapsable());
                });
                //solar
                $row->column(6 , function ($column) use ($bulan , $param , $warna , $DataWill , $DataAset) {

                    // $Data = RekapWilayah::where('jenis_pemakaian' , 'Solar')
                    //                     ->where('tahun' , $param[0]['value'] )
                    //                     ->get();
                    $Data = RekapPemakaian::whereIn('aset_id' , $DataAset)
                                        ->where('jenis_pemakaian' , 'Solar')
                                        ->where('tahun' , $param[0]['value'] )
                                        ->get();

                    $seriesArray = [];
                    $i = 0;
                    foreach ($Data as $key => $value) {
                        $seriesArray[$i] = array(
                           'name'  => $value->nama_aset,
                            'data'  => [$value->jan , $value->feb , $value->mar , $value->apr , $value->may , $value->jun , $value->jul , $value->aug , $value->sep , $value->oct ,$value->nov , $value->dec]
                        );
                        $i = $i + 1;
                    }

                    $solar = \Chart::title(['text' => 'Penggunaan Solar Period ' . $param[0]['value'] ,])
                        ->chart([
                            'type'     => 'line', // pie , columnt ect
                            'renderTo' => 'chart_solar', // render the chart into your div with id
                        ])
                        ->subtitle([
                            'text' => '',
                        ])
                        ->xaxis([
                            'categories' => $bulan,
                        ])
                        ->yaxis([
                            'title' => 'text:a'

                        ])
                        ->series(
                            $seriesArray
                        )
                        ->display();

                    $column->append(( new Box('Penggunaan Solar' , view('admin.charts.solar' , compact('solar'))))->removable()->collapsable());
                });
                });
                $content->row(function ($row) use ($bulan , $param , $warna , $DataWill , $DataAset)  {
                //air_pam
                $row->column(6 , function ($column) use ($bulan , $param , $warna , $DataWill , $DataAset) {

                    // $Data = RekapWilayah::where('jenis_pemakaian' , 'Air')
                    //                     ->where('jenis' , 'PAM')
                    //                     ->where('tahun' , $param[0]['value'] )
                    //                     ->get();

                    $Data = RekapPemakaian::whereIn('aset_id' , $DataAset)
                                        ->where('jenis_pemakaian' , 'Air')
                                        ->where('jenis' , 'PAM')
                                        ->where('tahun' , $param[0]['value'] )
                                        ->get();

                    $seriesArray_air = [];
                    $i = 0;
                    foreach ($Data as $key => $value) {
                        $seriesArray_air[$i] = array(
                            'name'  => $value->nama_aset,
                            'data'  => [$value->jan , $value->feb , $value->mar , $value->apr , $value->may , $value->jun , $value->jul , $value->aug , $value->sep , $value->oct ,$value->nov , $value->dec]
                        );
                        $i = $i + 1;
                    }

                    $air = \Chart::title(['text' => 'Penggunaan Air PAM Period ' . $param[0]['value'] ,])
                        ->chart([
                            'type'     => 'line', // pie , columnt ect
                            'renderTo' => 'chart_air', // render the chart into your div with id
                        ])
                        ->subtitle([
                            'text' => '',
                        ])
                        ->xaxis([
                            'categories' => $bulan,
                        ])
                        ->yaxis([
                            'title' => 'text:a'

                        ])
                        ->series(
                            $seriesArray_air
                        )
                        ->display();

                    $column->append(( new Box('Penggunaan Air PAM' , view('admin.charts.air' , compact('air'))))->removable()->collapsable());
                });
                //air_sumur
                $row->column(6 , function ($column) use ($bulan , $param , $warna , $DataWill , $DataAset) {

                    // $Data = RekapWilayah::where('jenis_pemakaian' , 'Air')
                    //                     ->where('jenis' , 'Sumur')
                    //                     ->where('tahun' , $param[0]['value'] )
                    //                     ->get();
                    $Data = RekapPemakaian::whereIn('aset_id' , $DataAset)
                                        ->where('jenis_pemakaian' , 'Air')
                                        ->where('jenis' , 'Sumur')
                                        ->where('tahun' , $param[0]['value'] )
                                        ->get();

                    $seriesArray_air_sumur = [];
                    $i = 0;
                    foreach ($Data as $key => $value) {
                        $seriesArray_air_sumur[$i] = array(
                            'name'  => $value->nama_aset,
                            'data'  => [$value->jan , $value->feb , $value->mar , $value->apr , $value->may , $value->jun , $value->jul , $value->aug , $value->sep , $value->oct ,$value->nov , $value->dec]
                        );
                        $i = $i + 1;
                    }

                    $air_sumur = \Chart::title(['text' => 'Penggunaan Air Sumur Period ' . $param[0]['value'] ,])
                        ->chart([
                            'type'     => 'line', // pie , columnt ect
                            'renderTo' => 'chart_air_sumur', // render the chart into your div with id
                        ])
                        ->subtitle([
                            'text' => '',
                        ])
                        ->xaxis([
                            'categories' => $bulan,
                        ])
                        ->yaxis([
                            'title' => 'text:a'

                        ])
                        ->series(
                            $seriesArray_air_sumur
                        )
                        ->display();

                    $column->append(( new Box('Penggunaan Air Sumur' , view('admin.charts.air_sumur' , compact('air_sumur'))))->removable()->collapsable());
                });
                });

                $content->row(function ($row) use ($bulan , $param , $warna , $DataWill , $DataAset) {
                //limbah basah
                $row->column(6 , function ($column) use ($bulan , $param , $warna , $DataWill , $DataAset) {

                    // $Data = RekapWilayah::where('jenis_pemakaian' , 'Limbah')
                    //                     ->where('jenis' , 'Basah')
                    //                     ->where('tahun' , $param[0]['value'] )
                    //                     ->get();

                    $Data = RekapPemakaian::whereIn('aset_id' , $DataAset)
                                        ->where('jenis_pemakaian' , 'Limbah')
                                        ->where('jenis' , 'Basah')
                                        ->where('tahun' , $param[0]['value'] )
                                        ->get();

                    $seriesArray = [];
                    $i = 0;
                    foreach ($Data as $key => $value) {
                        $seriesArray[$i] = array(
                            'name'  => $value->nama_aset,
                            'data'  => [$value->jan , $value->feb , $value->mar , $value->apr , $value->may , $value->jun , $value->jul , $value->aug , $value->sep , $value->oct ,$value->nov , $value->dec]
                        );
                        $i = $i + 1;
                    }

                    $limbah = \Chart::title(['text' => 'Penggunaan Limbah Basah Period ' . $param[0]['value'] ,])
                        ->chart([
                            'type'     => 'line', // pie , columnt ect
                            'renderTo' => 'chart_limbah', // render the chart into your div with id
                        ])
                        ->subtitle([
                            'text' => '',
                        ])
                        ->xaxis([
                            'categories' => $bulan,
                        ])
                        ->yaxis([
                            'title' => 'text:a'

                        ])
                        ->series(
                            $seriesArray
                        )
                        ->display();

                    $column->append(( new Box('Penggunaan Limbah Basah' , view('admin.charts.limbah' , compact('limbah'))))->removable()->collapsable());
                });
                //limbah basah
                $row->column(6 , function ($column) use ($bulan , $param , $warna , $DataWill , $DataAset) {

                    // $Data = RekapWilayah::where('jenis_pemakaian' , 'Limbah')
                    //                     ->where('jenis' , 'Kering')
                    //                     ->where('tahun' , $param[0]['value'] )
                    //                     ->get();
                $Data = RekapPemakaian::whereIn('aset_id' , $DataAset)
                                        ->where('jenis_pemakaian' , 'Limbah')
                                        ->where('jenis' , 'Kering')
                                        ->where('tahun' , $param[0]['value'] )
                                        ->get();
                    $seriesArray = [];
                    $i = 0;
                    foreach ($Data as $key => $value) {
                        $seriesArray[$i] = array(
                            'name'  => $value->nama_aset,
                            'data'  => [$value->jan , $value->feb , $value->mar , $value->apr , $value->may , $value->jun , $value->jul , $value->aug , $value->sep , $value->oct ,$value->nov , $value->dec]
                        );
                        $i = $i + 1;
                    }

                    $limbah_kering = \Chart::title(['text' => 'Penggunaan Limbah Kering Period ' . $param[0]['value'] ,])
                        ->chart([
                            'type'     => 'line', // pie , columnt ect
                            'renderTo' => 'chart_limbah_kering', // render the chart into your div with id
                        ])
                        ->subtitle([
                            'text' => '',
                        ])
                        ->xaxis([
                            'categories' => $bulan,
                        ])
                        ->yaxis([
                            'title' => 'text:a'

                        ])
                        ->series(
                            $seriesArray
                        )
                        ->display();

                    $column->append(( new Box('Penggunaan Limbah Kering' , view('admin.charts.limbah_kering' , compact('limbah_kering'))))->removable()->collapsable());
                });
                });
            
                $content->row(function ($row){
                    $row->column(6, new InfoBox('Progress Pekerjaan Rutin', 'list', 'blue', '/admin/tindaklanjuts', 0));
                    $row->column(6, new InfoBox('Progress Pekerjaan Non Rutin', 'list', 'orange', '/admin/tindaklanjuts', 0));
                });
            });
            
        }

        if($idRoles == 5 || $idRoles == 8)
        {
            $DataAset = Aset::where('wilayah_area_id' , $idGroupArea)->get(['id']);
            return Admin::content(function (Content $content) use ($idGroupArea , $DataAset) {
           
                $content->header('Dashboard');          
                $content->row(function ($row) use ($DataAset) {
                $DataComplain       = Complaint::where('status_id' , 1)
                                        ->WhereIn('aset_id' , $DataAset)->get();
               
                $TotalComplain      = Count($DataComplain);
                $DataOnProgress     = Complaint::where('status_id' , 2)
                                        ->WhereIn('aset_id' , $DataAset)->get();
                $TotalOnProgress    = Count($DataOnProgress);
                $DataOutStanding     = Complaint::where('status_id' , 3)
                                        ->WhereIn('aset_id' , $DataAset)->get();
                $TotalOutStanding    = Count($DataOutStanding);

                $row->column(4, new InfoBox('Complain', 'list', 'red', '/admin/tindaklanjuts', $TotalComplain));
                $row->column(4, new InfoBox('On Progress Complain', 'list', 'yellow', '/admin/tindaklanjuts', $TotalOnProgress ));
                $row->column(4, new InfoBox('Out Standing Complain', 'list', 'green', '/admin/tindaklanjuts', $TotalOutStanding));
                });  

                $bulan = ['Jan' , 'Feb' , 'Mar' , 'Apr' , 'Mei' , 'Jun' , 'Jul' , 'Ags' , 'Sep' , 'Okt' , 'Nov' , 'Des'];
                $param = Parameter::where('id' , 1)->get();
                $warna = ['red' , 'blue' , 'yellow' , 'green' , 'orange' , 'grey' , 'black'];


                $content->row(function ($row) use ($bulan , $param , $warna , $idGroupArea , $DataAset) {
                //listrik
                $row->column(6 , function ($column) use ($bulan , $param , $warna , $idGroupArea , $DataAset) {

                    // $Data = RekapWilayah::where('jenis_pemakaian' , 'Listrik')
                    //                     ->where('tahun' , $param[0]['value'] )
                    //                     ->get();

                    $Data = RekapPemakaian::whereIn('aset_id' , $DataAset)
                                        ->where('jenis_pemakaian' , 'Listrik')
                                        ->where('tahun' , $param[0]['value'] )
                                        ->get();

                    $seriesArray = [];
                    $i = 0;
                    foreach ($Data as $key => $value) {
                        $seriesArray[$i] = array(
                            'name'  => $value->nama_aset,
                            'data'  => [$value->jan , $value->feb , $value->mar , $value->apr , $value->may , $value->jun , $value->jul , $value->aug , $value->sep , $value->oct ,$value->nov , $value->dec]
                        );
                        $i = $i + 1;
                    }

                    

                    $chart1 = \Chart::title(['text' => 'Penggunaan Listrik Period ' . $param[0]['value'] ,])
                        ->chart([
                            'type'     => 'line', // pie , columnt ect
                            'renderTo' => 'chart1', // render the chart into your div with id
                        ])
                        ->subtitle([
                            'text' => '',
                        ])
                        ->xaxis([
                            'categories' => $bulan,
                        ])
                        ->yaxis([
                            'title' => 'text:a'

                        ])
                        ->series(
                            $seriesArray
                        )
                        ->display();

                    $column->append(( new Box('Penggunaan Listrik' , view('admin.charts.chart' , compact('chart1'))))->removable()->collapsable());
                });
                //solar
                $row->column(6 , function ($column) use ($bulan , $param , $warna , $idGroupArea , $DataAset) {

                    // $Data = RekapWilayah::where('jenis_pemakaian' , 'Solar')
                    //                     ->where('tahun' , $param[0]['value'] )
                    //                     ->get();
                    $Data = RekapPemakaian::whereIn('aset_id' , $DataAset)
                                        ->where('jenis_pemakaian' , 'Solar')
                                        ->where('tahun' , $param[0]['value'] )
                                        ->get();

                    $seriesArray = [];
                    $i = 0;
                    foreach ($Data as $key => $value) {
                        $seriesArray[$i] = array(
                           'name'  => $value->nama_aset,
                            'data'  => [$value->jan , $value->feb , $value->mar , $value->apr , $value->may , $value->jun , $value->jul , $value->aug , $value->sep , $value->oct ,$value->nov , $value->dec]
                        );
                        $i = $i + 1;
                    }

                    $solar = \Chart::title(['text' => 'Penggunaan Solar Period ' . $param[0]['value'] ,])
                        ->chart([
                            'type'     => 'line', // pie , columnt ect
                            'renderTo' => 'chart_solar', // render the chart into your div with id
                        ])
                        ->subtitle([
                            'text' => '',
                        ])
                        ->xaxis([
                            'categories' => $bulan,
                        ])
                        ->yaxis([
                            'title' => 'text:a'

                        ])
                        ->series(
                            $seriesArray
                        )
                        ->display();

                    $column->append(( new Box('Penggunaan Solar' , view('admin.charts.solar' , compact('solar'))))->removable()->collapsable());
                });
                });
                $content->row(function ($row) use ($bulan , $param , $warna , $idGroupArea , $DataAset)  {
                //air_pam
                $row->column(6 , function ($column) use ($bulan , $param , $warna , $idGroupArea , $DataAset) {

                    // $Data = RekapWilayah::where('jenis_pemakaian' , 'Air')
                    //                     ->where('jenis' , 'PAM')
                    //                     ->where('tahun' , $param[0]['value'] )
                    //                     ->get();

                    $Data = RekapPemakaian::whereIn('aset_id' , $DataAset)
                                        ->where('jenis_pemakaian' , 'Air')
                                        ->where('jenis' , 'PAM')
                                        ->where('tahun' , $param[0]['value'] )
                                        ->get();

                    $seriesArray_air = [];
                    $i = 0;
                    foreach ($Data as $key => $value) {
                        $seriesArray_air[$i] = array(
                            'name'  => $value->nama_aset,
                            'data'  => [$value->jan , $value->feb , $value->mar , $value->apr , $value->may , $value->jun , $value->jul , $value->aug , $value->sep , $value->oct ,$value->nov , $value->dec]
                        );
                        $i = $i + 1;
                    }

                    $air = \Chart::title(['text' => 'Penggunaan Air PAM Period ' . $param[0]['value'] ,])
                        ->chart([
                            'type'     => 'line', // pie , columnt ect
                            'renderTo' => 'chart_air', // render the chart into your div with id
                        ])
                        ->subtitle([
                            'text' => '',
                        ])
                        ->xaxis([
                            'categories' => $bulan,
                        ])
                        ->yaxis([
                            'title' => 'text:a'

                        ])
                        ->series(
                            $seriesArray_air
                        )
                        ->display();

                    $column->append(( new Box('Penggunaan Air PAM' , view('admin.charts.air' , compact('air'))))->removable()->collapsable());
                });
                //air_sumur
                $row->column(6 , function ($column) use ($bulan , $param , $warna , $idGroupArea , $DataAset) {

                    // $Data = RekapWilayah::where('jenis_pemakaian' , 'Air')
                    //                     ->where('jenis' , 'Sumur')
                    //                     ->where('tahun' , $param[0]['value'] )
                    //                     ->get();
                    $Data = RekapPemakaian::whereIn('aset_id' , $DataAset)
                                        ->where('jenis_pemakaian' , 'Air')
                                        ->where('jenis' , 'Sumur')
                                        ->where('tahun' , $param[0]['value'] )
                                        ->get();

                    $seriesArray_air_sumur = [];
                    $i = 0;
                    foreach ($Data as $key => $value) {
                        $seriesArray_air_sumur[$i] = array(
                            'name'  => $value->nama_aset,
                            'data'  => [$value->jan , $value->feb , $value->mar , $value->apr , $value->may , $value->jun , $value->jul , $value->aug , $value->sep , $value->oct ,$value->nov , $value->dec]
                        );
                        $i = $i + 1;
                    }

                    $air_sumur = \Chart::title(['text' => 'Penggunaan Air Sumur Period ' . $param[0]['value'] ,])
                        ->chart([
                            'type'     => 'line', // pie , columnt ect
                            'renderTo' => 'chart_air_sumur', // render the chart into your div with id
                        ])
                        ->subtitle([
                            'text' => '',
                        ])
                        ->xaxis([
                            'categories' => $bulan,
                        ])
                        ->yaxis([
                            'title' => 'text:a'

                        ])
                        ->series(
                            $seriesArray_air_sumur
                        )
                        ->display();

                    $column->append(( new Box('Penggunaan Air Sumur' , view('admin.charts.air_sumur' , compact('air_sumur'))))->removable()->collapsable());
                });
                });

                $content->row(function ($row) use ($bulan , $param , $warna , $idGroupArea , $DataAset) {
                //limbah basah
                $row->column(6 , function ($column) use ($bulan , $param , $warna , $idGroupArea , $DataAset) {

                    // $Data = RekapWilayah::where('jenis_pemakaian' , 'Limbah')
                    //                     ->where('jenis' , 'Basah')
                    //                     ->where('tahun' , $param[0]['value'] )
                    //                     ->get();

                    $Data = RekapPemakaian::whereIn('aset_id' , $DataAset)
                                        ->where('jenis_pemakaian' , 'Limbah')
                                        ->where('jenis' , 'Basah')
                                        ->where('tahun' , $param[0]['value'] )
                                        ->get();

                    $seriesArray = [];
                    $i = 0;
                    foreach ($Data as $key => $value) {
                        $seriesArray[$i] = array(
                            'name'  => $value->nama_aset,
                            'data'  => [$value->jan , $value->feb , $value->mar , $value->apr , $value->may , $value->jun , $value->jul , $value->aug , $value->sep , $value->oct ,$value->nov , $value->dec]
                        );
                        $i = $i + 1;
                    }

                    $limbah = \Chart::title(['text' => 'Penggunaan Limbah Basah Period ' . $param[0]['value'] ,])
                        ->chart([
                            'type'     => 'line', // pie , columnt ect
                            'renderTo' => 'chart_limbah', // render the chart into your div with id
                        ])
                        ->subtitle([
                            'text' => '',
                        ])
                        ->xaxis([
                            'categories' => $bulan,
                        ])
                        ->yaxis([
                            'title' => 'text:a'

                        ])
                        ->series(
                            $seriesArray
                        )
                        ->display();

                    $column->append(( new Box('Penggunaan Limbah Basah' , view('admin.charts.limbah' , compact('limbah'))))->removable()->collapsable());
                });
                //limbah basah
                $row->column(6 , function ($column) use ($bulan , $param , $warna , $idGroupArea , $DataAset) {

                    // $Data = RekapWilayah::where('jenis_pemakaian' , 'Limbah')
                    //                     ->where('jenis' , 'Kering')
                    //                     ->where('tahun' , $param[0]['value'] )
                    //                     ->get();
                $Data = RekapPemakaian::whereIn('aset_id' , $DataAset)
                                        ->where('jenis_pemakaian' , 'Limbah')
                                        ->where('jenis' , 'Kering')
                                        ->where('tahun' , $param[0]['value'] )
                                        ->get();
                    $seriesArray = [];
                    $i = 0;
                    foreach ($Data as $key => $value) {
                        $seriesArray[$i] = array(
                            'name'  => $value->nama_aset,
                            'data'  => [$value->jan , $value->feb , $value->mar , $value->apr , $value->may , $value->jun , $value->jul , $value->aug , $value->sep , $value->oct ,$value->nov , $value->dec]
                        );
                        $i = $i + 1;
                    }

                    $limbah_kering = \Chart::title(['text' => 'Penggunaan Limbah Kering Period ' . $param[0]['value'] ,])
                        ->chart([
                            'type'     => 'line', // pie , columnt ect
                            'renderTo' => 'chart_limbah_kering', // render the chart into your div with id
                        ])
                        ->subtitle([
                            'text' => '',
                        ])
                        ->xaxis([
                            'categories' => $bulan,
                        ])
                        ->yaxis([
                            'title' => 'text:a'

                        ])
                        ->series(
                            $seriesArray
                        )
                        ->display();

                    $column->append(( new Box('Penggunaan Limbah Kering' , view('admin.charts.limbah_kering' , compact('limbah_kering'))))->removable()->collapsable());
                });
                });
            
                $content->row(function ($row){
                    $row->column(6, new InfoBox('Progress Pekerjaan Rutin', 'list', 'blue', '/admin/tindaklanjuts', 0));
                    $row->column(6, new InfoBox('Progress Pekerjaan Non Rutin', 'list', 'orange', '/admin/tindaklanjuts', 0));
                });
            });
        }
        return Admin::content(function (Content $content) {
            $A = Admin::user()->roles;
            $content->header('Dashboard');
            $CountDown = 0;
            // $content->description('Description...');
            if($A[0]['pivot']['role_id'] == 8)
            {
               // reminder
                $param_reminder         = parameter::where('description' , 'Tanggal Pelaporan')->get(['value']);
                $param_reminder_start   = parameter::where('description' , 'Reminder Start')->get(['value']);
                $tanggal                = Carbon::now();
                $bulan_now              = $tanggal->month;
                $tahun_now              = $tanggal->year;
                $tanggal_now            = $tanggal->day;

                if($tanggal_now >= $param_reminder_start[0]['value'])
                {
                    $CountDown = $param_reminder[0]['value'] - $tanggal_now;
                }

                $content->row(function ($row) use ($CountDown){
                    if($CountDown >= 0)
                    {
                        $row->column(12 , new Alert( $CountDown .' Hari batas Pengiriman Laporan', 'Reminder', 'danger'));
                    }
                    else
                    {
                         $row->column(12 , new Alert( 'Anda Telat Pengiriman Laporan', 'Reminder', 'danger'));
                    }
                });
 
            }
            
            $content->row(function ($row) {

                $DataComplain       = Complaint::where('status_id' , 1)->get();
                $TotalComplain      = Count($DataComplain);
                $DataOnProgress     = Complaint::where('status_id' , 2)->get();
                $TotalOnProgress    = Count($DataOnProgress);
                $DataOutStanding     = Complaint::where('status_id' , 3)->get();
                $TotalOutStanding    = Count($DataOutStanding);

                $row->column(4, new InfoBox('Complain', 'list', 'red', '/admin/tindaklanjuts', $TotalComplain));
                $row->column(4, new InfoBox('On Progress Complain', 'list', 'yellow', '/admin/tindaklanjuts', $TotalOnProgress ));
                $row->column(4, new InfoBox('Out Standing Complain', 'list', 'green', '/admin/tindaklanjuts', $TotalOutStanding));
            });  

            $bulan = ['Jan' , 'Feb' , 'Mar' , 'Apr' , 'Mei' , 'Jun' , 'Jul' , 'Ags' , 'Sep' , 'Okt' , 'Nov' , 'Des'];
            $param = Parameter::where('id' , 1)->get();
            $warna = ['red' , 'blue' , 'yellow' , 'green' , 'orange' , 'grey' , 'black'];




            $content->row(function ($row) use ($bulan , $param , $warna) {
                //listrik
                $row->column(6 , function ($column) use ($bulan , $param , $warna) {

                    $Data = RekapWilayah::where('jenis_pemakaian' , 'Listrik')
                                        ->where('tahun' , $param[0]['value'] )
                                        ->get();

                    $seriesArray = [];
                    $i = 0;
                    foreach ($Data as $key => $value) {
                        $seriesArray[$i] = array(
                            'name'  => $value->nama,
                            'data'  => [$value->Jan , $value->Feb , $value->Mar , $value->Apr , $value->May , $value->Jun , $value->Jul , $value->Aug , $value->Sep , $value->Oct ,$value->Nov , $value->Dec]
                        );
                        $i = $i + 1;
                    }

                    

                    $chart1 = \Chart::title(['text' => 'Penggunaan Listrik Period ' . $param[0]['value'] ,])
                        ->chart([
                            'type'     => 'line', // pie , columnt ect
                            'renderTo' => 'chart1', // render the chart into your div with id
                        ])
                        ->subtitle([
                            'text' => '',
                        ])
                        ->xaxis([
                            'categories' => $bulan,
                        ])
                        ->yaxis([
                            'title' => 'text:a'

                        ])
                        ->series(
                            $seriesArray
                        )
                        ->display();

                    $column->append(( new Box('Penggunaan Listrik' , view('admin.charts.chart' , compact('chart1'))))->removable()->collapsable());
                });
                //solar
                $row->column(6 , function ($column) use ($bulan , $param , $warna) {

                    $Data = RekapWilayah::where('jenis_pemakaian' , 'Solar')
                                        ->where('tahun' , $param[0]['value'] )
                                        ->get();

                    $seriesArray = [];
                    $i = 0;
                    foreach ($Data as $key => $value) {
                        $seriesArray[$i] = array(
                            'name'  => $value->nama,
                            'data'  => [$value->Jan , $value->Feb , $value->Mar , $value->Apr , $value->May , $value->Jun , $value->Jul , $value->Aug , $value->Sep , $value->Oct ,$value->Nov , $value->Dec]
                        );
                        $i = $i + 1;
                    }

                    $solar = \Chart::title(['text' => 'Penggunaan Solar Period ' . $param[0]['value'] ,])
                        ->chart([
                            'type'     => 'line', // pie , columnt ect
                            'renderTo' => 'chart_solar', // render the chart into your div with id
                        ])
                        ->subtitle([
                            'text' => '',
                        ])
                        ->xaxis([
                            'categories' => $bulan,
                        ])
                        ->yaxis([
                            'title' => 'text:a'

                        ])
                        ->series(
                            $seriesArray
                        )
                        ->display();

                    $column->append(( new Box('Penggunaan Solar' , view('admin.charts.solar' , compact('solar'))))->removable()->collapsable());
                });
            });
            $content->row(function ($row) use ($bulan , $param , $warna) {
                //air_pam
                $row->column(6 , function ($column) use ($bulan , $param , $warna) {

                    $Data = RekapWilayah::where('jenis_pemakaian' , 'Air')
                                        ->where('jenis' , 'PAM')
                                        ->where('tahun' , $param[0]['value'] )
                                        ->get();

                    $seriesArray_air = [];
                    $i = 0;
                    foreach ($Data as $key => $value) {
                        $seriesArray_air[$i] = array(
                            'name'  => $value->nama,
                            'data'  => [$value->Jan , $value->Feb , $value->Mar , $value->Apr , $value->May , $value->Jun , $value->Jul , $value->Aug , $value->Sep , $value->Oct ,$value->Nov , $value->Dec]
                        );
                        $i = $i + 1;
                    }

                    $air = \Chart::title(['text' => 'Penggunaan Air PAM Period ' . $param[0]['value'] ,])
                        ->chart([
                            'type'     => 'line', // pie , columnt ect
                            'renderTo' => 'chart_air', // render the chart into your div with id
                        ])
                        ->subtitle([
                            'text' => '',
                        ])
                        ->xaxis([
                            'categories' => $bulan,
                        ])
                        ->yaxis([
                            'title' => 'text:a'

                        ])
                        ->series(
                            $seriesArray_air
                        )
                        ->display();

                    $column->append(( new Box('Penggunaan Air PAM' , view('admin.charts.air' , compact('air'))))->removable()->collapsable());
                });
                //air_sumur
                $row->column(6 , function ($column) use ($bulan , $param , $warna) {

                    $Data = RekapWilayah::where('jenis_pemakaian' , 'Air')
                                        ->where('jenis' , 'Sumur')
                                        ->where('tahun' , $param[0]['value'] )
                                        ->get();

                    $seriesArray_air_sumur = [];
                    $i = 0;
                    foreach ($Data as $key => $value) {
                        $seriesArray_air_sumur[$i] = array(
                            'name'  => $value->nama,
                            'data'  => [$value->Jan , $value->Feb , $value->Mar , $value->Apr , $value->May , $value->Jun , $value->Jul , $value->Aug , $value->Sep , $value->Oct ,$value->Nov , $value->Dec]
                        );
                        $i = $i + 1;
                    }

                    $air_sumur = \Chart::title(['text' => 'Penggunaan Air Sumur Period ' . $param[0]['value'] ,])
                        ->chart([
                            'type'     => 'line', // pie , columnt ect
                            'renderTo' => 'chart_air_sumur', // render the chart into your div with id
                        ])
                        ->subtitle([
                            'text' => '',
                        ])
                        ->xaxis([
                            'categories' => $bulan,
                        ])
                        ->yaxis([
                            'title' => 'text:a'

                        ])
                        ->series(
                            $seriesArray_air_sumur
                        )
                        ->display();

                    $column->append(( new Box('Penggunaan Air Sumur' , view('admin.charts.air_sumur' , compact('air_sumur'))))->removable()->collapsable());
                });
            });

            $content->row(function ($row) use ($bulan , $param , $warna) {
                //limbah basah
                $row->column(6 , function ($column) use ($bulan , $param , $warna) {

                    $Data = RekapWilayah::where('jenis_pemakaian' , 'Limbah')
                                        ->where('jenis' , 'Basah')
                                        ->where('tahun' , $param[0]['value'] )
                                        ->get();

                    $seriesArray = [];
                    $i = 0;
                    foreach ($Data as $key => $value) {
                        $seriesArray[$i] = array(
                            'name'  => $value->nama,
                            'data'  => [$value->Jan , $value->Feb , $value->Mar , $value->Apr , $value->May , $value->Jun , $value->Jul , $value->Aug , $value->Sep , $value->Oct ,$value->Nov , $value->Dec]
                        );
                        $i = $i + 1;
                    }

                    $limbah = \Chart::title(['text' => 'Penggunaan Limbah Basah Period ' . $param[0]['value'] ,])
                        ->chart([
                            'type'     => 'line', // pie , columnt ect
                            'renderTo' => 'chart_limbah', // render the chart into your div with id
                        ])
                        ->subtitle([
                            'text' => '',
                        ])
                        ->xaxis([
                            'categories' => $bulan,
                        ])
                        ->yaxis([
                            'title' => 'text:a'

                        ])
                        ->series(
                            $seriesArray
                        )
                        ->display();

                    $column->append(( new Box('Penggunaan Limbah Basah' , view('admin.charts.limbah' , compact('limbah'))))->removable()->collapsable());
                });
                //limbah basah
                $row->column(6 , function ($column) use ($bulan , $param , $warna) {

                    $Data = RekapWilayah::where('jenis_pemakaian' , 'Limbah')
                                        ->where('jenis' , 'Kering')
                                        ->where('tahun' , $param[0]['value'] )
                                        ->get();

                    $seriesArray = [];
                    $i = 0;
                    foreach ($Data as $key => $value) {
                        $seriesArray[$i] = array(
                            'name'  => $value->nama,
                            'data'  => [$value->Jan , $value->Feb , $value->Mar , $value->Apr , $value->May , $value->Jun , $value->Jul , $value->Aug , $value->Sep , $value->Oct ,$value->Nov , $value->Dec]
                        );
                        $i = $i + 1;
                    }

                    $limbah_kering = \Chart::title(['text' => 'Penggunaan Limbah Kering Period ' . $param[0]['value'] ,])
                        ->chart([
                            'type'     => 'line', // pie , columnt ect
                            'renderTo' => 'chart_limbah_kering', // render the chart into your div with id
                        ])
                        ->subtitle([
                            'text' => '',
                        ])
                        ->xaxis([
                            'categories' => $bulan,
                        ])
                        ->yaxis([
                            'title' => 'text:a'

                        ])
                        ->series(
                            $seriesArray
                        )
                        ->display();

                    $column->append(( new Box('Penggunaan Limbah Kering' , view('admin.charts.limbah_kering' , compact('limbah_kering'))))->removable()->collapsable());
                });
            });
            
            $content->row(function ($row){
                $row->column(6, new InfoBox('Progress Pekerjaan Rutin', 'list', 'blue', '/admin/tindaklanjuts', 0));
                $row->column(6, new InfoBox('Progress Pekerjaan Non Rutin', 'list', 'orange', '/admin/tindaklanjuts', 0));
            });
        });

    }


}
