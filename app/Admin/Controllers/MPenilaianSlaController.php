<?php

namespace App\Admin\Controllers;

use App\Models\MPenilaianSla;
use App\Models\bulan;
use App\Models\Parameter;
use App\Models\Regional;
use App\Models\RekapPenilaianSla;
use App\Models\Aset;
use App\Models\WilayahArea;
use App\Models\Wilayah;

use Encore\Admin\Widgets\Box;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

use Illuminate\Http\Request;
use PDF;
use DB;

class MPenilaianSlaController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(MPenilaianSla::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            $grid->created_at();
            $grid->updated_at();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(MPenilaianSla::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

    public function getprint()
    {
        return Admin::content(function (Content $content) {
            $content->header('Summary Report Penilaian SLA');


            $content->row(function ($rows){
                $rows->column( 12 , function ($column){

                    $bulan = bulan::all();
                    $param = parameter::where('id' , 1)->get();
                    $tahun = $param[0]['value'];

                    $column->append(( new Box('Report' , view('form_report.PenilaianReport' , compact('bulan' , 'tahun') )))->removable()->collapsable());
                });
                
            });
        });
    }

    public function print(Request $request)
    {
        
        $Master = MPenilaianSla::all();

        // Hapus Rekap
        RekapPenilaianSla::where('id' , '!=' , 0)->delete();



        foreach ($Master as $key => $value) {
            
            //get regional
            $asetID         = $value->aset_id;
            $DataAset       = Aset::where('id' , $asetID)->get();
            $WillArea       = WilayahArea::where('id' , $DataAset[0]['wilayah_area_id'])->get();
            $Will           =  Wilayah::where('id' , $WillArea[0]['wilayah_id'] )->get();

            
            $cek = RekapPenilaianSla::where('aset_id' , $value->aset_id)
                                    ->where('tahun' , $value->tahun)
                                    ->get();

            

            if(Count($cek) >= 1)
            {
               switch ($value->bulan_id) {
                    case 1:
                        $update = RekapPenilaianSla::where('aset_id' , $value->aset_id)
                                        ->where('tahun' , $value->tahun)
                                        ->update(['jan' => $value->pencapaian_sla
                                        ]); 
                        break;
                    case 2:
                        $update = RekapPenilaianSla::where('aset_id' , $value->aset_id)
                                        ->where('tahun' , $value->tahun)
                                        ->update(['feb' => $value->pencapaian_sla
                                        ]);  
                        break;
                    case 3:
                        $update = RekapPenilaianSla::where('aset_id' , $value->aset_id)
                                        ->where('tahun' , $value->tahun)
                                        ->update(['mar' => $value->pencapaian_sla
                                        ]); 
                        break;
                    case 4:
                        $update = RekapPenilaianSla::where('aset_id' , $value->aset_id)
                                        ->where('tahun' , $value->tahun)
                                        ->update(['apr' => $value->pencapaian_sla
                                        ]);  
                        break;
                    case 5:
                        $update = RekapPenilaianSla::where('aset_id' , $value->aset_id)
                                        ->where('tahun' , $value->tahun)
                                        ->update(['mei' => $value->pencapaian_sla
                                        ]);  
                        break;
                    case 6:
                        $update = RekapPenilaianSla::where('aset_id' , $value->aset_id)
                                        ->where('tahun' , $value->tahun)
                                        ->update(['jun' => $value->pencapaian_sla
                                        ]);  
                        break;
                    case 7:
                        $update = RekapPenilaianSla::where('aset_id' , $value->aset_id)
                                        ->where('tahun' , $value->tahun)
                                        ->update(['jul' => $value->pencapaian_sla
                                        ]);  
                        break;
                    case 8:
                       $update = RekapPenilaianSla::where('aset_id' , $value->aset_id)
                                        ->where('tahun' , $value->tahun)
                                        ->update(['aug' => $value->pencapaian_sla
                                        ]);  
                        break;
                    case 9:
                        $update = RekapPenilaianSla::where('aset_id' , $value->aset_id)
                                        ->where('tahun' , $value->tahun)
                                        ->update(['sep' => $value->pencapaian_sla
                                        ]); 
                        break;
                    case 10:
                        $update = RekapPenilaianSla::where('aset_id' , $value->aset_id)
                                        ->where('tahun' , $value->tahun)
                                        ->update(['oct' => $value->pencapaian_sla
                                        ]); 
                        break;
                    case 11:
                        $update = RekapPenilaianSla::where('aset_id' , $value->aset_id)
                                        ->where('tahun' , $value->tahun)
                                        ->update(['nov' => $value->pencapaian_sla
                                        ]);  
                        break;
                    case 12:
                        $update = RekapPenilaianSla::where('aset_id' , $value->aset_id)
                                        ->where('tahun' , $value->tahun)
                                        ->update(['dec' => $value->pencapaian_sla
                                        ]);  
                        break;
                    default:
                        # code...
                        break;
                }
            }
            else
            {
                $insert                 = new RekapPenilaianSla();
                $insert->regional_id    = $Will[0]['regional_id'];
                $insert->tahun        = $value->tahun;
                $insert->aset_id        = $value->aset_id;

                switch ($value->bulan_id) {
                    case 1:
                        $insert->jan = $value->pencapaian_sla; 
                        break;
                    case 2:
                        $insert->feb = $value->pencapaian_sla; 
                        break;
                    case 3:
                        $insert->mar = $value->pencapaian_sla; 
                        break;
                    case 4:
                        $insert->apr = $value->pencapaian_sla; 
                        break;
                    case 5:
                        $insert->mei = $value->pencapaian_sla; 
                        break;
                    case 6:
                        $insert->jun = $value->pencapaian_sla; 
                        break;
                    case 7:
                        $insert->jul = $value->pencapaian_sla; 
                        break;
                    case 8:
                        $insert->aug = $value->pencapaian_sla; 
                        break;
                    case 9:
                        $insert->sep = $value->pencapaian_sla; 
                        break;
                    case 10:
                        $insert->oct = $value->pencapaian_sla; 
                        break;
                    case 11:
                        $insert->nov = $value->pencapaian_sla; 
                        break;
                    case 12:
                        $insert->dec = $value->pencapaian_sla; 
                        break;
                    default:
                        # code...
                        break;
                }
                $insert->save();
            }
        }

        $param = parameter::where('id' , 1)->get();
        $tahun = $param[0]['value'];



        $Rekap = DB::select("select rekap_penilaian_sla.jan as jan , rekap_penilaian_sla.feb as feb , rekap_penilaian_sla.mar as mar , rekap_penilaian_sla.apr as apr ,
            rekap_penilaian_sla.mei as mei , rekap_penilaian_sla.jun as jun , 
            rekap_penilaian_sla.jul as jul , rekap_penilaian_sla.aug as aug ,
            rekap_penilaian_sla.sep as sep , rekap_penilaian_sla.oct as oct ,
            rekap_penilaian_sla.nov as nov , rekap_penilaian_sla.dec as des , aset.nama as aset
    , wilayah_area.nama_area as area , regional.id as regid, regional.nama as region FROM fmis.aset
    INNER JOIN fmis.rekap_penilaian_sla 
        ON (aset.id = rekap_penilaian_sla.aset_id)
    INNER JOIN fmis.wilayah_area 
        ON (wilayah_area.id = aset.wilayah_area_id)
    INNER JOIN fmis.wilayah 
        ON (wilayah_area.wilayah_id = wilayah.id)
    INNER JOIN fmis.regional 
        ON (regional.id = wilayah.regional_id)");

        $regional = DB::select('Select rekap_penilaian_sla.regional_id as RegId , regional.nama as RegNama from rekap_penilaian_sla join regional on rekap_penilaian_sla.regional_id = regional.id group by rekap_penilaian_sla.regional_id');
   
        

        if($request->Period == 'JanJun')
        {
            $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'images' => true])->loadView('report.penilaian.rekap-penilaian-JanJun' ,
         compact('Rekap' , 'regional' ,'tahun') )->setPaper('A4' , 'portrait');
        }
        else {
            $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'images' => true])->loadView('report.penilaian.rekap-penilaian-JulDes' ,
         compact('Rekap' , 'regional' ,'tahun') )->setPaper('A4' , 'portrait');
        }
        

        // return view('report.penilaian_sla');
        return $pdf->stream('rekap-utility.pdf');
    }
}
