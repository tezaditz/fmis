<?php

namespace App\Admin\Controllers;

use App\Models\MJadwalRequest;
use App\Models\TindaklanjutRequest;
use App\Models\Permintaan;
use App\Models\Aset;
use App\Models\DJadwalRequest;



use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

use Carbon\Carbon;

class MJadwalRequestController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        $this->update_flag_mjadwal();
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
       return Admin::grid(MJadwalRequest::class, function (Grid $grid) {

            $grid->disableCreation();
            $grid->disableRowSelector();
            $grid->disableexport();
            $grid->disableActions();

            $grid->permintaan()->nomor();
            $grid->permintaan()->nama_pemohon('Nama Pemohon');
            $grid->permintaan()->unit_pelapor('Unit Pelapor');


            $grid->column('Status Complain')->display(function () {            
                
                if($this->flag == 0)
                {
                    return '<a href="/admin/mjadwalrequests/'. $this->id .'/edit" class="btn btn-xs btn-danger">Belum Terjadwal</a>';
                }
                elseif($this->flag == 1)
                {
                    return '<a href="javascript:void(0)" class="btn btn-xs btn-success">Terjadwal</a>';   
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
        return Admin::form(MJadwalRequest::class, function (Form $form) {

            $form->display('permintaan.nomor' , 'Nomor Work Order');
            $form->display('permintaan.nama_pemohon' , 'Nama Pemohon');
            $form->display('permintaan.unit_pelapor' , 'Unit Pelapor');
            $form->display('permintaan.uraian_komplain' , 'Uraian complain');

            $form->display('tindaklanjutrequest.pusat_biaya' , 'Pusat Biaya');
            $form->display('tindaklanjutrequest.keterangan_biaya' , 'Keterangan Biaya');
            $form->display('tindaklanjutrequest.waktu_pekerjaan' , 'Waktu Pekerjaan');
            $form->display('tindaklanjutrequest.satuan_waktu' , 'Satuan Waktu');


            $form->date('tanggal_awal', 'Tanggal Awal / Tanggal Mulai');
        });
    }

    public function update_flag_mjadwal()
    {
        $DataJadwal = MJadwalRequest::where('flag' , 0)->get();

        foreach ($DataJadwal as $key => $value) {

            if(!empty($value->tanggal_awal))
            {
              $update_mjadwalRequest = MJadwalRequest::where('id' , $value->id)
                                                        ->update(['flag' => 1]);


                $DataTindaklanjut = TindakLanjutRequest::where('request_id' , $value->request_id)->get();
                if($DataTindaklanjut[0]['satuan_waktu'] == 'Hari')
                {
                    
                    $lama = $DataTindaklanjut[0]['waktu_pekerjaan'];

                    for ($i=1; $i <= $lama  ; $i++) { 
                        $tanggal = new Carbon($value->tanggal_awal);
                        $tgl = $tanggal->AddDay($i - 1);

                        $insert_dJadwalComplain                             = new DJadwalRequest();
                        $insert_dJadwalComplain->m_jadwalrequest_id         = $value->id; 
                        $insert_dJadwalComplain->tindaklanjutrequest_id     = $DataTindaklanjut[0]['id'];
                        $insert_dJadwalComplain->request_id                 = $DataTindaklanjut[0]['request_id'];
                        $insert_dJadwalComplain->tanggal                    = $tgl;
                        $insert_dJadwalComplain->save();

                    }
                }
                else
                {
                        $tgl = new Carbon($value->tanggal_awal);
                        

                        $insert_dJadwalComplain                             = new DJadwalRequest();
                        $insert_dJadwalComplain->m_jadwalrequest_id         = $value->id; 
                        $insert_dJadwalComplain->tindaklanjutrequest_id     = $DataTindaklanjut[0]['id'];
                        $insert_dJadwalComplain->request_id                 = $DataTindaklanjut[0]['request_id'];
                        $insert_dJadwalComplain->tanggal                    = $tgl;
                        $insert_dJadwalComplain->save();
                }  
            }
                

        }
    }
}
