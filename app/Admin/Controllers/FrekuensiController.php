<?php

namespace App\Admin\Controllers;

use App\Models\Frekuensi;
use App\Models\Parameter;
use App\Models\Waktu;
use App\Models\bulan;
use App\Models\Minggu;
use App\Models\Hari;

use App\Models\frekuensi_bulan;
use App\Models\FrekuensiHari;
use App\Models\FrekuensiMinggu;
use App\Models\DetailFrekuensi;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Carbon\Carbon;

class FrekuensiController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        $this->generate_tanggal();
        return Admin::content(function (Content $content) {

            $content->header('Frekuensi');
            $content->description('List');

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

            $update = Frekuensi::where('id' , $id)
                            ->update(['generate' => 0]);


            $content->header('Frekuensi');
            $content->description('Edit');

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

            $content->header('Frekuensi');
            $content->description('Create');

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
        return Admin::grid(Frekuensi::class, function (Grid $grid) {

            // $grid->id('ID')->sortable();
            $grid->uraian('Uraian');
            $grid->kode('Kode Text');
            $grid->warna('Warna');
            
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Frekuensi::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('uraian','Uraian');
            $form->text('kode' , 'Kode Text');

            $hari = [];
            for ($i=1; $i <= 7 ; $i++) { 
                $hari[$i] = $i;
            };

            $minggu = [];
            for ($j=1; $j <=4 ; $j++) { 
                $minggu[$j] = $j;
            };

            $bulan = [];
            for ($k=1; $k <=12 ; $k++) { 
                $bulan[$k] = $k;
            };


            $form->multipleSelect('bulan', 'Pilih Bulan')->options(bulan::all()->pluck('uraian', 'id'));
            $form->multipleSelect('minggu', 'Pilih Minggu')->options(Minggu::all()->pluck('uraian', 'id'));
            $form->multipleSelect('hari', 'Pilih Hari')->options(Hari::all()->pluck('uraian', 'id'));
            $form->color('warna');
            
        });
    }

    public function getfrekuensi()
    {
        $Frekuensi = Frekuensi::all(['id' , 'uraian']);

        return $Frekuensi;
    }

    public function generate_tanggal()
    {
        $data_frekuensi = Frekuensi::where('generate' , '=' , 0)->get();
        $param = Parameter::where('id' , 1)->get();
        $Current_Year = $param[0]['value'];

        if(Count($data_frekuensi) > 0)
        {
            $id                     = $data_frekuensi[0]['id'];
            $tanggal                = $data_frekuensi[0]['tanggal_awal'];
            $JmlHariPerMinggu       = 7;

            $cek = DetailFrekuensi::where('frekuensi_id' , '=' , $id)->get();
            
            if(Count($cek) > 1)
            {
                $delete = DetailFrekuensi::where('frekuensi_id' , $id)->delete();
                
            }

            $Data_Bulan = frekuensi_bulan::where('frekuensi_id' , $id)
                            ->OrderBy('bulan_id' , 'ASC')->get();
            $Data_Minggu = FrekuensiMinggu::where('frekuensi_id' , $id)
                            ->OrderBy('minggu_id' , 'ASC')->get();
            $Data_Hari = FrekuensiHari::where('frekuensi_id' , $id)
                            ->OrderBy('hari_id' , 'ASC')->get();                

                foreach ($Data_Bulan as $key_bulan => $value_bulan) {
                                      
                    foreach ($Data_Minggu as $key_minggu => $value_minggu) {
                        $startDate = Carbon::create($Current_Year , $value_bulan->bulan_id , 1)->startofweek();
                        $w = $value_minggu->minggu_id - 1;
                        $a = $startDate->AddWeek($w);

                        $month = $a->month;
                        $thisday = $a->day;

                        foreach ($Data_Hari as $key_hari => $value_hari) {
                            $dt = Carbon::create($Current_Year , $month , $thisday );
                            $d = $value_hari->hari_id - 1;
                            $day = $dt->AddDay($d);
                           
                            $cek1 = DetailFrekuensi::where('tanggal' , $day->toDateString())
                                                    ->where('frekuensi_id' , $id)
                                                    ->get();



                            if(Count($cek1) < 1)
                            {
                                $insert = new DetailFrekuensi();
                                $insert->frekuensi_id = $id;
                                $insert->tanggal = $day;
                                $insert->save();    
                            }
                        }

                    }
                }
           
           $Update = Frekuensi::where('id' , $id)
                            ->update(['generate' => 1]);



        }
    }


}
