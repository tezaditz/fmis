<?php

namespace App\Admin\Controllers;

use App\Models\MJadwalComplain;
use App\Models\TindakLanjut;
use App\Models\Complaint;
use App\Models\Jenis_Complain;
use App\Models\Aset;
use App\Models\DJadwalComplain;
use App\Models\WilayahArea;
use App\Models\AdminRoles;
use App\Models\AdminRoleUsers;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

use Illuminate\Http\Request;

use Carbon\Carbon;

class MJadwalComplainController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        // $this->update_status();



        $this->update_flag_mjadwal();
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

            $content->header('header');
            $content->description('description');

            $content->body($this->form()->edit($id));
        });
    }

    public function edit_form($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('header');
            $content->description('description');


            $content->body($this->form_edit($id)->edit($id));
            


        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create($detailtindaklanjut)
    {
        return Admin::content(function (Content $content) use ($detailtindaklanjut) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form($detailtindaklanjut));
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {

        $Data = $this->aset_filter();
        
        return Admin::grid(MJadwalComplain::class, function (Grid $grid) use ($Data ) {
            $grid->model()->whereIn('complain_id' , $Data);

            $grid->disableCreation();
            $grid->disableRowSelector();
            $grid->disableexport();
            $grid->disableActions();

            $grid->complain()->nomor();
            $grid->complain()->nama_pemohon();
            $grid->complain()->unit_pelapor();

            $grid->column('Jenis Complain')->display(function () {            
                $DataComplain = Complaint::where('id' , $this->complain_id)->get();
                $JenisComplain = Jenis_Complain::where('id' , $DataComplain[0]['id_jenis_complaint'])->get();
                return $JenisComplain[0]['uraian'];
            });
            $grid->column('Status Complain')->display(function () {            
                
                if($this->flag == 0)
                {
                    return '<a href="/admin/mjadwalcomplains/'. $this->id .'/edit" class="btn btn-xs btn-danger">Belum Terjadwal</a>';
                }
                elseif($this->flag == 1)
                {
                    return '<a href="javascript:void(0);" class="btn btn-xs btn-success">Terjadwal</a>';   
                }
                elseif($this->flag == 2)
                {
                    return '<a href="/admin/mjadwalcomplains/'. $this->id .'/edit_form" class="btn btn-xs btn-info">Selesai Dikerjakan</a>';
                }
                elseif($this->flag == 3)
                {
                    return '<a href="javascriptvoid(0)" class="btn btn-xs btn-success">Done</a>';
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
        return Admin::form(MJadwalComplain::class, function (Form $form) {

            $form->display('complain.nomor' , 'Nomor Work Order');
            $form->display('complain.nama_pemohon' , 'Nama Pemohon');
            $form->display('complain.unit_pelapor' , 'Unit Pelapor');
            $form->display('complain.uraian_komplain' , 'Uraian complain');

            $form->display('tindaklanjut.pusat_biaya' , 'Pusat Biaya');
            $form->display('tindaklanjut.waktu_pekerjaan' , 'Waktu Pekerjaan');
            $form->display('tindaklanjut.satuan_waktu' , 'Satuan Waktu');


            $form->date('tanggal_awal', 'Tanggal Awal / Tanggal Mulai');

            // 

            $form->saved(function(Form $form) {
                
                
                $datamaster = MJadwalComplain::where('id', $form->model()->id)->get();

                $update_status = Complaint::where('id', $datamaster[0]['complain_id'])
                                ->update(['status_id' => 5]);

                $update_status_mjadwalcomplain = MJadwalComplain::where('id' ,$datamaster[0]['id'])
                                ->update(['flag' => 3]);
                                

                return redirect('/admin/mjadwalcomplains');

            });
            


        });
    }

    protected function form_edit()
    {

        return Admin::form(MJadwalComplain::class, function (Form $form){

            $form->display('complain.nomor' , 'Nomor Work Order');
            $form->display('complain.nama_pemohon' , 'Nama Pemohon');
            $form->display('complain.unit_pelapor' , 'Unit Pelapor');
            $form->display('complain.uraian_komplain' , 'Uraian complain');

            $form->display('tindaklanjut.pusat_biaya' , 'Pusat Biaya');
            $form->display('tindaklanjut.waktu_pekerjaan' , 'Waktu Pekerjaan');
            $form->display('tindaklanjut.satuan_waktu' , 'Satuan Waktu');


            $form->display('tanggal_awal', 'Tanggal Awal / Tanggal Mulai');

            // 

            // $form->saving(function(Form $form) {
                
            //     $form->model()->id;
            //     $datamaster = MJadwalComplain::where('id', $form->model()->id)->get();

            //     $update_status = Complaint::where('id', $datamaster[0]['complain_id'])
            //                     ->update(['status_id' => 5]);

            //     $update_status_mjadwalcomplain = MJadwalComplain::where('id' ,$datamaster[0]['id'])
            //                     ->update(['flag' => 3]);
                                

            //     return redirect('/admin/mjadwalcomplains');

            // });
            
        });
    }
    

    

    public function update_flag_mjadwal()
    {
        $DataJadwal = MJadwalComplain::where('flag' , 0)->get();

        foreach ($DataJadwal as $key => $value) {

            if(!empty($value->tanggal_awal))
            {
              $update_mjadwalcomplain = MJadwalComplain::where('id' , $value->id)
                                            ->update(['flag' => 1]);


                $DataTindaklanjut = TindakLanjut::where('complain_id' , $value->complain_id)->get();
                if($DataTindaklanjut[0]['satuan_waktu'] == 'Hari')
                {
                    
                    $lama = $DataTindaklanjut[0]['waktu_pekerjaan'];

                    for ($i=1; $i <= $lama  ; $i++) { 
                        $tanggal = new Carbon($value->tanggal_awal);
                        $tgl = $tanggal->AddDay($i - 1);

                        $insert_dJadwalComplain                         = new DJadwalComplain();
                        $insert_dJadwalComplain->m_jadwalcomplain_id    = $value->id; 
                        $insert_dJadwalComplain->tindaklanjut_id        = $DataTindaklanjut[0]['id'];
                        $insert_dJadwalComplain->complain_id            = $DataTindaklanjut[0]['complain_id'];
                        $insert_dJadwalComplain->tanggal                = $tgl;
                        $insert_dJadwalComplain->save();

                    }
                }
                else
                {
                        $tgl = new Carbon($value->tanggal_awal);
                        

                        $insert_dJadwalComplain                         = new DJadwalComplain();
                        $insert_dJadwalComplain->m_jadwalcomplain_id    = $value->id; 
                        $insert_dJadwalComplain->tindaklanjut_id        = $DataTindaklanjut[0]['id'];
                        $insert_dJadwalComplain->complain_id            = $DataTindaklanjut[0]['complain_id'];
                        $insert_dJadwalComplain->tanggal                = $tgl;
                        $insert_dJadwalComplain->save();
                }  
            }
                

        }
    }

    public function aset_filter()
    {
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
        $DataComplain = Complaint::whereIn('aset_id' , $DataAset)->get(['id']);
        return $DataComplain;
    }
}
