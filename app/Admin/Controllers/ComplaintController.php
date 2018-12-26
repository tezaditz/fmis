<?php

namespace App\Admin\Controllers;

use App\Models\Complaint;
use App\Models\Jenis_Complain;
use App\Models\Aset;
use App\Models\Tiket;
use App\Models\WilayahArea;
use App\Models\AdminRoles;
use App\Models\AdminRoleUsers;
use App\Models\Tindaklanjut;
use App\Models\Status;
use App\Models\MJadwalComplain;
use App\Models\DJadwalComplain;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use PDF;
class ComplaintController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        // $this->tambah_tindaklanjut();

        
        return Admin::content(function (Content $content) {

            $content->header('Complain');
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

            $content->header('Ubah Complain');
            $content->description('');

            $content->body($this->form()->edit($id));
        });
    }

        /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function show($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('Complain');
            $content->description('');

            $content->body($this->form_show()->edit($id));
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

            $content->header('Complain');
            $content->description('Baru');

            $content->body($this->form_tambah());
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
        $DataWill = WilayahArea::get(['id']);
        $DataAset = Aset::whereIn('wilayah_area_id' , $DataWill)->get(['id']);
        // return $DataAset;
        return Admin::grid(Complaint::class, function (Grid $grid) use ($idRoles , $idGroupArea , $idGroupWill , $idAset ){
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
            $grid->model()->whereIn('aset_id' , $DataAset);

            $grid->model()->orderBy('tanggal_masuk' , 'ASC');
            $grid->column('Tanggal Masuk')->display(function($tanggal){
                return $this->tanggal_masuk;
            });

            $grid->nomor('Nomor Complain');

            $grid->nama_pemohon('Nama Pemohon');
            $grid->unit_pelapor('Unit Pelapor');
            $grid->jenis_complain()->uraian();
            $grid->aset()->address('Lokasi');
            $grid->column('Status')->display(function() use ($idRoles){
                $DataStatus = Status::where('id' , $this->status_id)->get();

                if($idRoles == 10 )
                {
                    return "<a href='javascript:void(0)' class='btn btn-xs btn-info'>". $DataStatus[0]['keterangan'] ."</a>";
                }
                if($this->status_id != 1)
                {
                    return "<a href='javascript:void(0)'  class='btn btn-xs btn-danger'>". $DataStatus[0]['keterangan'] ."</a>";
                }
                elseif($idRoles == 4)
                {
                    $DataTindaklanjut = Tindaklanjut::where('complain_id' , $this->id)->get(['id']);
                	// return "<a href='/admin/complaints/". $this->id ."/tindaklanjuts/". $DataTindaklanjut[0]['id'] ."' class='btn btn-xs btn-info'>". $DataStatus[0]['keterangan'] ."</a>";
                    return "<a href='/admin/tindaklanjuts/". $DataTindaklanjut[0]['id'] ."/edit' class='btn btn-xs btn-info'>". $DataStatus[0]['keterangan'] ."</a>";
                }
                elseif($idRoles == 5)
                {
                    return "<a href='javascript:void(0)' class='btn btn-xs btn-info'>". $DataStatus[0]['keterangan'] ."</a>";
                }
            });
            $grid->column('Posisi Complain')->display(function() use ($idRoles){
                $DataStatus = Status::where('id' , $this->status_id)->get();
                return $DataStatus[0]['posisi_wo'];
            });
            

            $grid->disableActions();
            $grid->disableExport();
            $grid->disableRowSelector();

        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Complaint::class, function (Form $form) {

            $form->text('nama_pemohon');
            $form->text('unit_pelapor');
            $form->select('id_jenis_complaint' , 'Jenis Complain')->options(
                    Jenis_Complain::all()->pluck('uraian', 'id')
                );
            $form->text('uraian_komplain');

        });
    }

    protected function form_show()
    {
        return Admin::form(Complaint::class, function (Form $form) {

            $form->display('nama_pemohon');
            $form->display('unit_pelapor');
            $form->display('id_jenis_complaint' , 'Jenis Complain')->options(
                    Jenis_Complain::all()->pluck('uraian', 'id')
                );
            $form->text('uraian_komplain');

        });
    }

    protected function form_tambah()
    {
        Admin::script($this->script());

       $roles      = Admin::user()->roles;
        $idRoles    = $roles[0]['id'];
        $user           = Admin::user();
        $idGroupArea    = $user['groupArea'];
        $idGroupWill    = $user['groupWil'];
        $idAset         = $user['aset_id'];
        $DataWill = WilayahArea::get(['id']);
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

        return Admin::form(Complaint::class, function (Form $form) use ($DataAset) {

            $form->setAction('/admin/complain/tambah');

            $form->text('nama_pemohon')->rules('required');
            $form->text('unit_pelapor')->rules('required');
            $form->select('id_jenis_complaint' , 'Jenis Complain')->options(
                    Jenis_Complain::all()->pluck('uraian', 'id')
                )->rules('required');
            $form->select('aset_id' , 'Nama Aset')->options(
                    Aset::whereIn('id' , $DataAset)->pluck('nama', 'id')
                )->rules('required');
            $form->text('lokasi' , 'Lokasi')->attribute(['readonly' => 'true'])->rules('required');
            $form->textarea('uraian_komplain' , 'Uraian Complain')->rules('required');

        });
    }



    protected function tambah(Request $request)
    {

        $insert_complain                        = new Complaint();
        $insert_complain->tanggal_masuk         = Carbon::now();
        $insert_complain->nomor                 = $this->ambil_tiket();
        $insert_complain->nama_pemohon          = $request->nama_pemohon;
        $insert_complain->unit_pelapor          = $request->unit_pelapor;
        $insert_complain->uraian_komplain       = $request->uraian_komplain;
        $insert_complain->id_jenis_complaint    = $request->id_jenis_complaint;
        $insert_complain->aset_id               = $request->aset_id;
        $insert_complain->lokasi                = $request->lokasi;
        $insert_complain->status_id             = 1;
        $insert_complain->save();

        $this->update_tiket('Work Order');
        $this->tambah_tindaklanjut();
        return redirect()->route('complaints.index');
    }

    protected function ambil_tiket()
    {
        $data_tiket   = Tiket::where('keterangan' , 'Work Order')->get();
        $get_tiket    = $data_tiket[0]['nilai'];

        switch (strlen($get_tiket)) {
            case '2':
                $nol = "00";
                break;
            case '3':
                $nol = "0";
                break;
            case '4':
                $nol = "";
                break;
            
            default:
                $nol = "000";
                break;
        }

        $nilai = $get_tiket + 1;
        $kode = $data_tiket[0]['kode'] . $nol . $nilai;
        return $kode;
    }

    protected function update_tiket($keterangan)
    {
        $data_tiket   = Tiket::where('keterangan' , 'Work Order')->get();
        $get_tiket    = $data_tiket[0]['nilai'];
        $nilai = $get_tiket + 1;

        $Tiket = Tiket::where('keterangan' , $keterangan)
                            ->update(['nilai' => $nilai]);
    }

    protected function print($id)
    {
        $DataComplain   = Complaint::where('id' , $id)->get();
        $DataAset       = Aset::where('id' , $DataComplain[0]['aset_id'])->get();
        $DataArea       = WilayahArea::where('id' , $DataAset[0]['wilayah_area_id'])->get();
        $area           = $DataArea[0]['nama_area'];


        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('report.complain')->setPaper('A4' , 'landscape');

        // return view('report.complain');

        // $pdf = PDF::loadView('report.complain');
        return $pdf->stream('complain');
    }

    public function tambah_tindaklanjut()
    {
        $DataComplain = Complaint::where('flag' , 0)->get();
        foreach ($DataComplain as $key => $value) {
            
            $insert_tindaklanjut = new TindakLanjut();
            $insert_tindaklanjut->complain_id = $value->id;
            $insert_tindaklanjut->save();

            $update_complain = Complaint::where('id' , $value->id)
                                        ->update(['flag' => 1]);
        }
    }

    public function index_report()
    {
        return Admin::content(function (Content $content) {

            $content->header('Complain');
            $content->description('List');

            $content->body($this->grid_report());
        });
    }

        /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid_report()
    {
        $roles      = Admin::user()->roles;
        $idRoles    = $roles[0]['id'];
        $user           = Admin::user();
        $idGroupArea    = $user['groupArea'];
        $idGroupWill    = $user['groupWil'];
        $idAset         = $user['aset_id'];

        
        return Admin::grid(Complaint::class, function (Grid $grid) use ($idRoles , $idGroupArea , $idGroupWill , $idAset ){
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
            $grid->model()->whereIn('aset_id' , $DataAset);

            $grid->model()->orderBy('tanggal_masuk' , 'ASC');
            $grid->column('Tanggal Masuk')->display(function($tanggal){
                return $this->tanggal_masuk;
            });

            $grid->nomor('Nomor Complain');

            $grid->nama_pemohon('Nama Pemohon');
            $grid->unit_pelapor('Unit Pelapor');
            $grid->jenis_complain()->uraian();
            $grid->aset()->address('Lokasi');
            $grid->column('Status')->display(function() use ($idRoles){
                $DataStatus = Status::where('id' , $this->status_id)->get();

            
                    return "<a href='/admin/complain/tindaklanjut/".$this->id."/print' class='btn btn-xs btn-info' target='_blank'><i class='fa fa-print'></i> Cetak</a>";
       

                // if($this->status_id != 1)
                // {
                //     return "<a href='/admin/download-complain/". $this->id ."' class='btn btn-xs btn-danger'>". $DataStatus[0]['keterangan'] ."</a>";
                // }
                // else
                // {
                //  return "<a href='javascript:void(0)' class='btn btn-xs btn-info'>". $DataStatus[0]['keterangan'] ."</a>";
                // }
            });
            

            $grid->disableActions();
            $grid->disableExport();
            $grid->disableRowSelector();

        });
    }

    public function cetak($id)
    {
        $DataComplain = Complaint::where('id' , $id)->get();
        $Jenis_Complain = Jenis_Complain::where('id' , $DataComplain[0]['id_jenis_complaint'])->get();
        // return $Jenis_Complain;
        $aset = Aset::where('id' , $DataComplain[0]['aset_id'])->get();
        $Data = MJadwalComplain::where('complain_id' , $id)->get();
        $DataDetail = DJadwalComplain::where('complain_id' , $id)->get();

        return view('report.tindaklanjut_complain' , ['Data' => $DataDetail , 
                                                      'jenis' => $Jenis_Complain[0]['uraian'],
                                                      'lokasi'  => $aset[0]['nama'] .' ( '. $aset[0]['address'] . ' )'   ]);
    }

    protected function script()
    {
        return <<<SCRIPT

$('.aset_id').on('change', function () {

    // Your code.
    // console.log($(this).val());

    var nilai = $(this).val();

    $.ajax({
                url: '/admin/settingjadwals/api/load_alamat/' + nilai,
                type: 'GET',
                dataType:'json',
                success: function(response)
                {
                    
                    $('.lokasi').val(response[0]['address']);
                    // console.log(response[0]['address']);
                }
            });

});

SCRIPT;
    }
}
