<?php

namespace App\Admin\Controllers;

use App\Models\Solar;
use App\Models\Parameter;
use App\Models\Aset;
use App\Models\RekapPemakaian;
use App\Models\RekapWilayah;
use App\Models\Wilayah;
use App\Models\WilayahArea;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use DB;

class SolarController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        $this->generate_pemakaian();
        $this->generate_wilayah();
        return Admin::content(function (Content $content) {

            $content->header('Pemakaian Solar');
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

            $content->header('Pemakaian Solar');
            $content->description('Edit');

            $content->body($this->form_edit()->edit($id));
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

            $content->header('Pemakaian');
            $content->description('Solar');

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
            $roles      = Admin::user()->roles;
            $idRoles    = $roles[0]['id'];
            $user           = Admin::user();
            $idGroupArea    = $user['groupArea'];
            $idGroupWill    = $user['groupWil'];
            $idAset         = $user['aset_id'];
            if($idRoles == 4)
            {
            $DataWill = WilayahArea::where('wilayah_id' , $idGroupWill)->get(['id']);
            }
            elseif($idRoles == 5)
            {
            $DataWill = WilayahArea::where('id' , $idGroupArea)->get(['id']);
            }
            else
            {
            $DataWill = WilayahArea::get(['id']);
            }
            $DataAset = Aset::whereIn('wilayah_area_id' , $DataWill)->get(['id']);
        return Admin::grid(Solar::class, function (Grid $grid) use ($DataAset) {
            $grid->model()->whereIn('aset_id' , $DataAset);
            $grid->disableExport();
            $grid->disableRowSelector();
            
            $A = Admin::user()->roles;

            switch ($A[0]['name']) {
                case 'General Manager':
                    $grid->disableCreation();
                    $grid->disableActions();
                    break;
                case 'Manager Pengelola(AdminHO)':
                    $grid->disableCreation();
                    $grid->disableActions();
                    break;
                case 'KoorWil':
                    $grid->disableCreation();
                    $grid->disableActions();
                    break;
                case 'KoorArea':
                    
                    break;
                default:
                    # code...
                    break;
            }

            $grid->aset()->nama('Nama Aset');
            $grid->column('Pemakaian (Liter)')->display(function(){
                return number_format($this->pemakaian , 2 , ',' , '.');
            });
            $grid->column('Pemakaian (Rp)')->display(function(){
                return number_format($this->rupiah , 2 , ',' , '.');
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Solar::class, function (Form $form) {

            $Parameter = Parameter::where('description'  , 'Tahun Anggaran')->get(['value']);
            $Thn = $Parameter[0]['value'];



            $form->display('id', 'ID');
            $form->text('tahun' , 'Tahun')->Attribute(['value' => $Thn , 'readonly' => 'true'])->rules('required');
            $form->select('period' , 'Period')->options([
                'Jan'   => 'Januari',
                'Feb'   => 'Februari',
                'Mar'   =>  'Maret' ,
                'Apr'   =>  'April' ,
                'May'   =>  'Mei' ,
                'Jun'   =>  'Juni' ,
                'Jul'   =>  'Juli' ,
                'Aug'   =>  'Agustus' ,
                'Sep'   =>  'September' ,
                'Oct'   =>  'Oktober' ,
                'Nov'   =>  'November' ,
                'Dec'   =>  'Desember'])->rules('required');
            
            $idArea = Admin::user()->groupArea;

            if($idArea != 1)
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
                    }
                    elseif($idRoles == 5)
                    {
                    $DataWill = WilayahArea::where('id' , $idGroupArea)->get(['id']);
                    }
                    else
                    {
                    $DataWill = WilayahArea::get(['id']);
                    }

                $form->select('aset_id' , 'Aset')->options(
                    Aset::whereIn('wilayah_area_id' , $DataWill)->pluck('nama' , 'id')
                )->rules('required');
            }
            else
            {
                $form->select('aset_id' , 'Aset')->options(
                    Aset::whereIn('wilayah_area_id' , $DataWill)->pluck('nama' , 'id')
                )->rules('required');
            }

            $form->number('pemakaian' , 'Pemakaian (Liter)')->rules('required');
            $form->number('rupiah','Rupiah Pemakaian (Rp)')->rules('required');
            $form->image('foto_before' , 'Foto Sebelum')->rules('required');
            $form->image('foto_after' , 'Foto Sesudah')->rules('required');

        });
    }

    protected function form_edit()
    {
        return Admin::form(Solar::class, function (Form $form) {

            $Parameter = Parameter::where('description'  , 'Tahun Anggaran')->get(['value']);
            $Thn = $Parameter[0]['value'];

            
            $form->text('tahun' , 'Tahun')->Attribute(['value' => $Thn , 'readonly' => 'true']);
            $form->select('period' , 'Period')->options([
                'Jan'   => 'Januari',
                'Feb'   => 'Februari',
                'Mar'   =>  'Maret' ,
                'Apr'   =>  'April' ,
                'May'   =>  'Mei' ,
                'Jun'   =>  'Juni' ,
                'Jul'   =>  'Juli' ,
                'Aug'   =>  'Agustus' ,
                'Sep'   =>  'September' ,
                'Oct'   =>  'Oktober' ,
                'Nov'   =>  'November' ,
                'Dec'   =>  'Desember'])->Attribute(['disabled' => 'false']);;


            $idArea = Admin::user()->groupArea;

            if($idArea != 1)
            {
                $form->select('aset_id' , 'Aset')->options(
                    Aset::all()->pluck('nama' , 'id')
                )->Attribute(['disabled' => 'false']);;;
            }
            else
            {
                $form->select('aset_id' , 'Aset')->options(
                    Aset::all()->pluck('nama' , 'id')
                )->Attribute(['disabled' => 'false']);;;
            }

            $form->number('pemakaian' , 'Pemakaian (Liter)');
            $form->number('rupiah','Rupiah Pemakaian (Rp)');
            $form->image('foto_before' , 'Foto Sebelum');
            $form->image('foto_before' , 'Foto Sesudah');

        });
    }

    public function generate_pemakaian()
    {

        $DataSolar = Solar::all();
        foreach ($DataSolar as $key => $value) {
            
            $cek_data = RekapPemakaian::where('aset_id' , $value->aset_id)
                                        ->where('tahun' , $value->tahun)
                                        ->where('jenis_pemakaian' , 'Solar')
                                        ->get();

            if(Count($cek_data) > 0)
            {
                $update_data = RekapPemakaian::where('aset_id' , $value->aset_id)
                                        ->where('tahun' , $value->tahun)
                                        ->where('jenis_pemakaian' , 'Solar')
                                        ->update([$value->period => $value->pemakaian]);
            }
            else
            {
                $insert_data = new RekapPemakaian();
                $insert_data->aset_id = $value->aset_id;
                
                $DataAset    = Aset::where('id' , $value->aset_id)->get();
                $DataWilayah =  WilayahArea::where('id' , $DataAset[0]['wilayah_area_id'])->get();
                $insert_data->nama_aset = $DataAset[0]['nama'];
                $insert_data->tahun     = $value->tahun;
                $insert_data->wilayah_id     = $DataWilayah[0]['wilayah_id'];
                $insert_data->jenis_pemakaian     = 'Solar';

                switch ($value->period) {
                    case 'Jan':
                        $insert_data->jan     = $value->pemakaian;
                        break;
                    case 'Feb':
                        $insert_data->feb     = $value->pemakaian;
                        break;
                    case 'Mar':
                        $insert_data->mar     = $value->pemakaian;
                        break;
                    case 'Apr':
                        $insert_data->apr     = $value->pemakaian;
                        break;
                    case 'May':
                        $insert_data->mei     = $value->pemakaian;
                        break;
                    case 'Jun':
                        $insert_data->jun     = $value->pemakaian;
                        break;
                    case 'Jul':
                        $insert_data->jul     = $value->pemakaian;
                        break;
                    case 'Aug':
                        $insert_data->aug     = $value->pemakaian;
                        break;
                    case 'Sep':
                        $insert_data->sep     = $value->pemakaian;
                        break;
                    case 'Oct':
                        $insert_data->oct     = $value->pemakaian;
                        break;
                    case 'Nov':
                        $insert_data->nov     = $value->pemakaian;
                        break;
                    case 'Dec':
                        $insert_data->dec     = $value->pemakaian;
                        break;
                    default:
                        # code...
                        break;
                }
                $insert_data->save();  
            }
        }
    }

    public function generate_wilayah()
    {
        $Parameter = Parameter::where('id' , 1)->get();
        $DataPemakaian = DB::table('rekap_pemakaian')
                            ->select('wilayah_id', 'tahun' , DB::raw('SUM(rekap_pemakaian.jan) AS JAN , SUM(rekap_pemakaian.feb) AS FEB , SUM(rekap_pemakaian.mar) AS MAR , SUM(rekap_pemakaian.apr) AS APR , SUM(rekap_pemakaian.may) AS MEI , SUM(rekap_pemakaian.jun) AS JUN , SUM(rekap_pemakaian.jul) AS JUL , SUM(rekap_pemakaian.aug) AS AUG , SUM(rekap_pemakaian.sep) AS SEP , SUM(rekap_pemakaian.oct) AS OKT , SUM(rekap_pemakaian.nov) AS NOV , SUM(rekap_pemakaian.dec) AS DES'))
                            ->where('jenis_pemakaian' , 'Solar')
                            ->where('tahun' , $Parameter[0]['value'])
                            ->groupBy('wilayah_id')
                            ->get();

        foreach ($DataPemakaian as $key => $value) {
            
            $DataWilayah = Wilayah::where('id' , $value->wilayah_id)->get();

            $DataRekapWil = RekapWilayah::where('tahun' , $value->tahun)
                                        ->where('jenis_pemakaian' , 'Solar')
                                        ->where('wilayah_id' , $value->wilayah_id)
                                        ->get();

            

            if(Count($DataRekapWil) > 0)
            {
                $Update_RekapWil = RekapWilayah::where('tahun' , $value->tahun)
                                        ->where('jenis_pemakaian' , 'Solar')
                                        ->where('wilayah_id' , $value->wilayah_id)
                                        ->update([
                                            'Jan' => $value->JAN,
                                            'Feb' => $value->FEB,
                                            'Mar' => $value->MAR,
                                            'Apr' => $value->APR,

                                            'May' => $value->MEI,
                                            'Jun' => $value->JUN,
                                            'Jul' => $value->JUL,
                                            'Aug' => $value->AUG,

                                            'Sep' => $value->SEP,
                                            'Oct' => $value->OKT,
                                            'Nov' => $value->NOV,
                                            'Dec' => $value->DES,
                                        ]);
            }
            else
            {

            $insert = new RekapWilayah();
            $insert->wilayah_id = $value->wilayah_id;
            $insert->nama = $DataWilayah[0]['nama'];
            $insert->jenis_pemakaian = 'Solar';
            $insert->tahun = $value->tahun;
            $insert->Jan = $value->JAN;
            $insert->Feb = $value->FEB;
            $insert->Mar = $value->MAR;
            $insert->Apr = $value->APR;
            $insert->May = $value->MEI;
            $insert->Jun = $value->JUN;
            $insert->Jul = $value->JUL;
            $insert->Aug = $value->AUG;
            $insert->Sep = $value->SEP;
            $insert->Oct = $value->OKT;
            $insert->Nov = $value->NOV;
            $insert->Dec = $value->DES;
            $insert->save();
            }


        }


        return $DataPemakaian;
    }

   
}
