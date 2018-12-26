<?php

namespace App\Admin\Controllers;

use App\Models\Tindaklanjut;
use App\Models\Complaint;
use App\Models\Jenis_Complain;
use App\Models\MJadwalComplain;
use App\Models\Status;
use App\Models\WilayahArea;
use App\Models\Aset;


use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class TindaklanjutController extends Controller
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

            $content->header('Tindak Lanjut Complain');
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

            $content->header('Tindaklanjut');
            $content->description();

            $getData = Tindaklanjut::where('id' , $id)->get();
            $DataComplain = Complaint::where('id' , $getData[0]['complain_id'])->get(['id']);
            $content->body($this->form_master($DataComplain[0]['id'])->edit($DataComplain[0]['id']));
            $content->body($this->form('edit')->edit($id));
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

            $content->header('Tindaklanjut');
            $content->description();

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
            $DataComplain = Complaint::whereIn('aset_id' , $DataAset)->get(['id']);

        return Admin::grid(Tindaklanjut::class, function (Grid $grid) use ($idRoles , $idGroupArea , $idGroupWill , $idAset , $DataComplain ) {
           
            $grid->model()->whereIn('complain_id' , $DataComplain);

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
                $DataComplain   = Complaint::where('id' , $this->complain_id)->get();
                $DataStatus     = Status::where('id' , $DataComplain[0]['status_id'])->get();
                if($DataComplain[0]['status_id'] == '1')
                {
                    return '<a href="/admin/tindaklanjuts/'. $this->id .'/edit" class="btn btn-xs btn-danger">' . $DataStatus[0]['keterangan'] . '</a>';
                }
                elseif($DataComplain[0]['status_id'] == '2')
                {
                    return '<a href="javascript:void(0)" class="btn btn-xs btn-success">' . $DataStatus[0]['keterangan'] . '</a>';
                }
            });
           
        });
    }

    public function form_master($id)
    {
        return Admin::form(Complaint::class, function (Form $form)  {
                $form->display('id' , 'ID');
                $form->display('nama_pemohon' , 'Nama Pemohon');
                $form->display('unit_pelapor' , 'Unit Pelapor');
                $form->display('jenis_complain.uraian' , 'Jenis Complain');

                $form->disableReset();
                $form->disableSubmit();
        
            $form->tools(function (Form\Tools $tools) {

                // Disable back btn.
                $tools->disableBackButton();

                // Disable list btn
                $tools->disableListButton();

                // Add a button, the argument can be a string, or an instance of the object that implements the Renderable or Htmlable interface
                $tools->add('<a href="/admin/tindaklanjuts" class="btn btn-sm btn-default"><i class="fa fa-arrow-left"></i>&nbsp;&nbsp;Back</a>');
                $tools->add('<a href="/admin/tindaklanjuts" class="btn btn-sm btn-default"><i class="fa fa-list"></i>&nbsp;&nbsp;List</a>');
            });
        });


    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form($mode = 'create')
    {
        Admin::script($this->script());

        if($mode == 'edit')
        {
            Admin::script($this->script_edit());            
        }

        return Admin::form(Tindaklanjut::class, function (Form $form) use ($mode)  {

            $form->hidden('complain_id' , 'Complain ID');
            $form->select('pusat_biaya' , 'Pusat Biaya')->options([ '0' => ' ' , 'PGNMAS' => 'PGNMAS' , 'PGN' => 'PGN' , 'Lainnya' => 'Lainnya']);
            $form->text('keterangan_biaya' , 'Keterangan Biaya')->attribute('readonly');
            $form->currency('biaya' , 'Biaya')->symbol('Rp.');
            $form->number('waktu_pekerjaan' , 'Waktu Pekerjaan');
            $form->select('satuan_waktu' , 'Satuan Waktu')->options(['Menit' => 'Menit' , 'Jam' => 'Jam' , 'Hari' => 'Hari']);
            $form->text('keterangan' , 'Keterangan');
            $form->hidden('flag' , 'Flag');

            $form->tools(function (Form\Tools $tools) {

                // Disable back btn.
                $tools->disableBackButton();

                // Disable list btn
                $tools->disableListButton();

                // Add a button, the argument can be a string, or an instance of the object that implements the Renderable or Htmlable interface
                
            });



            $form->saved(function (Form $form) {
                $getData = Tindaklanjut::where('id' , $form->model()->id)->get();
                $Update = Complaint::where('id' , $getData[0]['complain_id'] )
                                    ->update(['status_id' => 2]);

                    $insert_jadwal = new MJadwalComplain();
                    $insert_jadwal->complain_id = $getData[0]['complain_id'];
                    $insert_jadwal->status_id = 1;
                    $insert_jadwal->save();
            });
        });
    }

    public function update_status()
    {
        // $DataTindaklanjut = Tindaklanjut::where('flag' , 1)->get();
        // return $DataTindaklanjut;
        // // foreach ($DataTindaklanjut as $key => $value) {
        //     $Update_Complain = Complaint::where('id' , $value->complain_id)
        //                         ->update(['status_id' => 2]);

        //     $insert_jadwal = new MJadwalComplain();
        //     $insert_jadwal->complain_id = $value->complain_id;
        //     $insert_jadwal->save();

        //     $update_tindaklanjut = Tindaklanjut::where('id' , $value->id)
        //                                     ->update(['flag' => 2]);
        // }
    }





protected function script()
    {
        return <<<SCRIPT

$('.pusat_biaya').on('change', function () {

    if($(this).val() == 'Lainnya')
    {
        // console.log($(this).val());
        $('.keterangan_biaya').val('');    
        $('.keterangan_biaya').removeAttr('readonly');
        $('.keterangan_biaya').focus();
    }
    else
    {
        $('.keterangan_biaya').val($(this).val());
        $('.keterangan_biaya').attr('readonly' , true);
    }
});

$('.flag').val(1);

SCRIPT;
    }
protected function script_edit()
    {
        return <<<SCRIPT
$('.flag').val(1);

SCRIPT;
    }
}
