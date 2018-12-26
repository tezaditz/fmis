<?php

namespace App\Admin\Controllers;

use App\Models\Permintaan;
use App\Models\Jenis_Complain;
use App\Models\Aset;
use App\Models\Tiket;
use App\Models\TindaklanjutRequest;
use App\Models\Status;

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
class RequestController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        $this->tambah_tindaklanjut();
        return Admin::content(function (Content $content) {

            $content->header('Request');
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

            $content->header('Request');
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
        return Admin::grid(Permintaan::class, function (Grid $grid) {
            $grid->DisableExport();
            $grid->DisableRowSelector();


            $grid->actions(function($action){
                $action->disableDelete();
                $action->append("<a href='/admin/download-pdf/". $action->getKey() ."' class='btn btn-xs' target='_blank'><i class='fa fa-print'></i></a>");
            });


            $grid->model()->orderBy('tanggal_masuk' , 'ASC');
            $grid->column('Tanggal Masuk')->display(function($tanggal){
                return $this->tanggal_masuk;
            });

            $grid->nomor('Nomor Request');

            $grid->nama_pemohon('Nama Pemohon');
            $grid->unit_pelapor('Unit Pelapor');
            $grid->aset()->address('Lokasi');
            $grid->column('Status')->display(function(){
                
                $DataStatus = Status::where('id' , $this->status_id)->get();


                if($this->status_id == 4)
                {
                    return '<a href="javascript:void(0)" class="btn btn-xs btn-danger">' . $DataStatus[0]['keterangan'] . '</a>';
                }
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
        return Admin::form(Permintaan::class, function (Form $form) {

            $form->text('nama_pemohon');
            $form->text('unit_pelapor');
            $form->select('id_jenis_complaint' , 'Jenis Complain')->options(
                    Jenis_Complain::all()->pluck('uraian', 'id')
                );
            $form->text('uraian_komplain');

        });
    }

    protected function form_tambah()
    {
        Admin::script($this->script());
        return Admin::form(Permintaan::class, function (Form $form) {

            $form->setAction('/admin/request/tambah');

            $form->text('nama_pemohon')->rules('required');
            $form->text('unit_pelapor')->rules('required');
            // $form->select('id_jenis_complaint' , 'Jenis Complain')->options(
            //         Jenis_Complain::all()->pluck('uraian', 'id')
            //     )->rules('required');
            $form->select('aset_id' , 'Nama Aset')->options(
                    Aset::all()->pluck('nama', 'id')
                )->rules('required');
            $form->text('lokasi' , 'Lokasi')->attribute(['readonly' => 'true'])->rules('required');
            $form->textarea('uraian_komplain' , 'Uraian Request')->rules('required');

        });
    }



    protected function tambah(Request $request)
    {

        $insert_permintaan                        = new Permintaan();
        $insert_permintaan->tanggal_masuk         = Carbon::now();
        $insert_permintaan->nomor                 = $this->ambil_tiket();
        $insert_permintaan->waktu                 = Carbon::now();
        $insert_permintaan->tanggal               = Carbon::now();
        $insert_permintaan->nama_pemohon          = $request->nama_pemohon;
        $insert_permintaan->unit_pelapor          = $request->unit_pelapor;
        $insert_permintaan->uraian_komplain       = $request->uraian_komplain;
        $insert_permintaan->id_jenis_complaint    = $request->id_jenis_complaint;
        $insert_permintaan->aset_id               = $request->aset_id;
        $insert_permintaan->lokasi                = $request->lokasi;
        $insert_permintaan->status_id                = 4;
        $insert_permintaan->save();

        $this->update_tiket('Request');

        return redirect()->route('requests.index');
    }

    protected function ambil_tiket()
    {
        $data_tiket   = Tiket::where('keterangan' , 'Request')->get();
        if(Count($data_tiket) > 1){
                $get_tiket    = $data_tiket[0]['nilai'];}
        else{
                $insert_data = new Tiket();
                $insert_data->keterangan = 'Request';
                $insert_data->kode = 'Req-';
                $insert_data->nilai = 0;
                $insert_data->save();

                $data_tiket   = Tiket::where('keterangan' , 'Request')->get();
                $get_tiket = 0;
                       }

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
        $data_tiket   = Tiket::where('keterangan' , 'Request')->get();
        $get_tiket    = $data_tiket[0]['nilai'];
        $nilai = $get_tiket + 1;

        $Tiket = Tiket::where('keterangan' , $keterangan)
                            ->update(['nilai' => $nilai]);
    }

    protected function print($id)
    {


        
        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('report.complain')->setPaper('a4' , 'landscape');

        return view('report.complain');
        // return $pdf->stream('WorkOrder.pdf');
    }

    public function tambah_tindaklanjut()
    {
        $DataRequest = Permintaan::where('flag' , 0)->get();
        foreach ($DataRequest as $key => $value) {
            
            $insert = new TindaklanjutRequest();
            $insert->request_id = $value->id;
            $insert->save();

            $update = Permintaan::where('id' , $value->id)
                            ->update(['flag' => 1]);



        }
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
