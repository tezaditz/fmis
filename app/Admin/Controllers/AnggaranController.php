<?php

namespace App\Admin\Controllers;

use App\Models\Anggaran;
use App\Models\Parameter;
use App\Models\Akun;
use App\Models\Aset;
use App\Models\Subakun;
use App\Models\RekapAnggaran;
use App\Models\Transaksi_Pengeluaran;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use PDF;
use DB;

class AnggaranController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        $this->update_sisa_anggaran();
        return Admin::content(function (Content $content) {

            $content->header('Anggaran');
            $content->description('');

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

            $content->header('Ubah Anggaran');
            $content->description('');

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

            $content->header('Membuat Anggaran');
            $content->description('');

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
        return Admin::grid(Anggaran::class, function (Grid $grid) {

            // $grid->id('ID')->sortable();
            $grid->akun()->uraian('Akun');
            $grid->subakun()->uraian('Subakun');
            $grid->column('Anggaran')->display(function(){
                return number_format($this->anggaran , 2 , ',' , '.');
            });
            $grid->column('Realisasi')->display(function(){
                return number_format($this->realisasi , 2 , ',' , '.');
            });
            $grid->column('Sisa Anggaran')->display(function(){
                return number_format($this->sisa_anggaran , 2 , ',' , '.');
            });
            // $grid->created_at();
            // $grid->updated_at();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Anggaran::class, function (Form $form) {

            // $form->display('id', 'ID');
            // $form->select('aset_id' , 'Aset ID')->options(
            //         Aset::all()->pluck('nama', 'id')
            //     );
            $Param = Parameter::where('id' , 1)->get();


            $form->text('tahun_anggaran', 'Tahun Anggaran')->default($Param[0]['value'])->attribute('readonly');
            $form->select('akun_id' , 'Akun ID')->options(
                    Akun::all()->pluck('uraian', 'id')
                )->load('subakun_id' , '/admin/api/subakun');
            $form->select('subakun_id' , 'Subakun ID')->options(
                    Subakun::all()->pluck('uraian', 'id')
                );
            $form->select('jenis' , 'Jenis Aset')->options(['Bangunan' => 'Bangunan', 'Tanah' => 'Tanah']);
            $form->currency('anggaran', 'Anggaran')->symbol('Rp.');
            // $form->text('anggaran' , 'Anggaran');
        });
    }

    public function update_sisa_anggaran()
    {
        $Data = Anggaran::all();

        foreach ($Data as $key => $value) {
            if($value->realisasi == 0)
            {
                $Update = Anggaran::where('id' ,$value->id)
                                    ->update(['sisa_anggaran' => $value->anggaran]);
            }
            else
            {
                $sisa = $value->anggaran - $value->realisasi;

                $Update = Anggaran::where('id' ,$value->id)
                                    ->update(['sisa_anggaran' => $sisa]);
            }


        }
    }

    public function getanggaran($ids , $thn)
    {

        $Data = Anggaran::where('subakun_id' , $ids)        
        ->where('tahun_anggaran' , $thn)
        ->get();


        if(Count($Data) > 0)
        {
            $nilai = $Data[0]['sisa_anggaran'];
            
        }
        else
        {
            $nilai = 0;
            
        }

        return $nilai;

        
    }

    public function index_laporan()
    {
        return Admin::content(function (Content $content){

            $content->header('Anggaran');
            $content->description('laporan');

            $content->body($this->form_laporan());
        });
    }

    public function form_laporan()
    {
        return Admin::form(Anggaran::class, function (Form $form) {
            
            $form->setView('admin.form');
            $form->setAction('/admin/anggaran/laporan/print');
            $form->disableReset();

            $DataParamenter = Parameter::where('id' , 1)->get();
           
            $form->text('Tahun Anggaran')->default($DataParamenter[0]['value'])->attribute('readonly');


        });
    }

    public function laporan()
    {
        $this->generate_rekap_anggaran();

        $Data = RekapAnggaran::orderby('subakun_id')->get();


        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('report.anggaran.rekap_anggaran' , compact('Data'))->setPaper('legal' , 'landscape');
        return $pdf->stream();
    }

    public function generate_rekap_anggaran()
    {
        $DataParamenter = Parameter::where('id' , 1)->get();

        $DataAnggaran = Anggaran::where('tahun_anggaran' , $DataParamenter[0]['value'])->get();

        
        foreach ($DataAnggaran as $key => $value) {
            $cek = RekapAnggaran::where('tahun_anggaran' , $DataParamenter[0]['value'])
                                ->where('akun_id' , $value->akun_id)
                                ->where('subakun_id' , $value->subakun_id)
                                ->get();

            // return Count($cek);

            if(Count($cek) > 0)
            {
                if($value->jenis == 'Bangunan')
                {
                    $update = RekapAnggaran::where('tahun_anggaran' , $DataParamenter[0]['value'])
                                ->where('akun_id' , $value->akun_id)
                                ->where('subakun_id' , $value->subakun_id)
                                ->update(['rkap_bangunan' => $value->anggaran]);
                }
                else
                {
                    $update = RekapAnggaran::where('tahun_anggaran' , $DataParamenter[0]['value'])
                                ->where('akun_id' , $value->akun_id)
                                ->where('subakun_id' , $value->subakun_id)
                                ->update(['rkap_tanah' => $value->anggaran]);
                }
                
            }
            else
            {

                $insert                     = new RekapAnggaran();
                $insert->tahun_anggaran     = $DataParamenter[0]['value'];
                $insert->akun_id            = $value->akun_id;
                $insert->subakun_id         = $value->subakun_id;

                $DataAkun       = Akun::where('id' , $value->akun_id)->get();
            
                $DataSubAkun    = Subakun::where('id' , $value->subakun_id)->get();

                $insert->akun_desc          = $DataAkun[0]['uraian'];    
                $insert->subakun_desc       = $DataSubAkun[0]['uraian'];
                if($value->jenis == 'Bangunan')
                {
                    $insert->rkap_bangunan  = $value->anggaran;
                }
                else
                {
                    $insert->rkap_tanah     = $value->anggaran;
                }
                $insert->save();


            }
        }

        $Data = DB::select('select sum(jumlah) as JML , akun_id , subakun_id , Month(tanggal) as BLN from transaksi_pengeluaran group by akun_id , subakun_id');

        foreach ($Data as $key => $value) {
            $Cek = RekapAnggaran::where('akun_id' , $value->akun_id)
                                    ->where('subakun_id' , $value->subakun_id)
                                    ->where('tahun_anggaran' , $DataParamenter[0]['value'])
                                    ->get();

            if(Count($Cek) > 0)
            {
                switch ($value->BLN) {
                    case 1:
                        $update = RekapAnggaran::where('akun_id' , $value->akun_id)
                                    ->where('subakun_id' , $value->subakun_id)
                                    ->where('tahun_anggaran' , $DataParamenter[0]['value'])
                                    ->update(['jan' => $value->JML]);
                        break;
                    case 2:
                        $update = RekapAnggaran::where('akun_id' , $value->akun_id)
                                    ->where('subakun_id' , $value->subakun_id)
                                    ->where('tahun_anggaran' , $DataParamenter[0]['value'])
                                    ->update(['feb' => $value->JML]);
                        break;
                    case 3:
                        $update = RekapAnggaran::where('akun_id' , $value->akun_id)
                                    ->where('subakun_id' , $value->subakun_id)
                                    ->where('tahun_anggaran' , $DataParamenter[0]['value'])
                                    ->update(['mar' => $value->JML]);
                        break;
                    case 4:
                        $update = RekapAnggaran::where('akun_id' , $value->akun_id)
                                    ->where('subakun_id' , $value->subakun_id)
                                    ->where('tahun_anggaran' , $DataParamenter[0]['value'])
                                    ->update(['apr' => $value->JML]);
                        break;
                    case 5:
                        $update = RekapAnggaran::where('akun_id' , $value->akun_id)
                                    ->where('subakun_id' , $value->subakun_id)
                                    ->where('tahun_anggaran' , $DataParamenter[0]['value'])
                                    ->update(['mei' => $value->JML]);
                        break;
                    case 6:
                        $update = RekapAnggaran::where('akun_id' , $value->akun_id)
                                    ->where('subakun_id' , $value->subakun_id)
                                    ->where('tahun_anggaran' , $DataParamenter[0]['value'])
                                    ->update(['jun' => $value->JML]);
                        break;
                    case 7:
                        $update = RekapAnggaran::where('akun_id' , $value->akun_id)
                                    ->where('subakun_id' , $value->subakun_id)
                                    ->where('tahun_anggaran' , $DataParamenter[0]['value'])
                                    ->update(['jul' => $value->JML]);
                        break;
                    case 8:
                        $update = RekapAnggaran::where('akun_id' , $value->akun_id)
                                    ->where('subakun_id' , $value->subakun_id)
                                    ->where('tahun_anggaran' , $DataParamenter[0]['value'])
                                    ->update(['aug' => $value->JML]);
                        break;
                    case 9:
                        $update = RekapAnggaran::where('akun_id' , $value->akun_id)
                                    ->where('subakun_id' , $value->subakun_id)
                                    ->where('tahun_anggaran' , $DataParamenter[0]['value'])
                                    ->update(['sep' => $value->JML]);
                        break;
                    case 10:
                        $update = RekapAnggaran::where('akun_id' , $value->akun_id)
                                    ->where('subakun_id' , $value->subakun_id)
                                    ->where('tahun_anggaran' , $DataParamenter[0]['value'])
                                    ->update(['oct' => $value->JML]);
                        break;
                    case 11:
                        $update = RekapAnggaran::where('akun_id' , $value->akun_id)
                                    ->where('subakun_id' , $value->subakun_id)
                                    ->where('tahun_anggaran' , $DataParamenter[0]['value'])
                                    ->update(['nov' => $value->JML]);
                        break;
                    case 12:
                        $update = RekapAnggaran::where('akun_id' , $value->akun_id)
                                    ->where('subakun_id' , $value->subakun_id)
                                    ->where('tahun_anggaran' , $DataParamenter[0]['value'])
                                    ->update(['dec' => $value->JML]);
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }

        $Data = DB::select('select sum(rkap_bangunan) as JML_bangunan , sum(rkap_tanah) as JML_tanah , akun_id   from rekap_anggaran group by akun_id ');

        foreach ($Data as $key => $value) {
            $Cek = RekapAnggaran::where('akun_id' , $value->akun_id)
                                    ->where('tahun_anggaran' , $DataParamenter[0]['value'])
                                    ->get();
            if(Count($Cek) > 0)
            {
               $update = RekapAnggaran::where('akun_id' , $value->akun_id)
                                    ->where('tahun_anggaran' , $DataParamenter[0]['value'])
                                    ->update(['biaya_total' => $value->JML_bangunan + $value->JML_tanah]);
            }
        }

        $Data = RekapAnggaran::where('tahun_anggaran' , $DataParamenter[0]['value'])->get();

        foreach ($Data as $key => $value) {
            $realisasi = $value->jan + $value->feb + $value->mar + $value->apr + $value->mei + $value->jun + $value->jul + $value->aug + $value->sep + $value->oct + $value->nov + $value->dec;
            $anggaran =  $value->rkap_bangunan + $value->rkap_tanah;
            $sisa = $anggaran - $realisasi;
            $update = RekapAnggaran::where('akun_id' , $value->akun_id)
                                    ->where('subakun_id' , $value->subakun_id)
                                    ->where('tahun_anggaran' , $DataParamenter[0]['value'])
                                    ->update(['sisa_anggaran' => $sisa]);
        }




        return $Data;
    }
}
