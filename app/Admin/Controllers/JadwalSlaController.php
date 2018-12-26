<?php

namespace App\Admin\Controllers;

use App\Models\JadwalSLA;
use App\Models\Aset;
use App\Models\JadwalAset;
use App\Models\KetersediaanSla;
use App\Models\Parameter;
use App\Models\DetailFrekuensi;
use App\Models\JadwalTindakLanjut;
use App\Models\WilayahArea;




use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class JadwalSlaController extends Controller
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

            $content->header('Jadwal Pekerjaan');
            $content->description('Rutin');

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

            $content->header('Pilih Aset');
            $content->description('');

            $content->body($this->grid_aset());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(JadwalSLA::class, function (Grid $grid) {

            $grid->id('ID')->sortable();


        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(JadwalSLA::class, function (Form $form) {

            $form->display('id', 'ID');


            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

    //===========================================================================================================================================================

    public function grid_aset()
    {
        return Admin::grid(Aset::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->nama('Nama Aset');
            $grid->address('Alamat Aset');

            $grid->disableFilter();
            $grid->disableExport();
            $grid->disableCreation();
            $grid->disableRowSelector();
            $grid->actions(function ($actions){
                $actions->disableDelete();
                $actions->disableView();
                $actions->disableEdit();

                $actions->append("<a href='/admin/get/jadwalaset/pilihaset/" . $actions->getKey() . "' class='btn btn-md' title='Pilih Aset'><i class='fa fa-check'></i></a>");
            });


        });
    }

    public function list_pekerjaan($id)
    {
        $this->generate_pekerjaan($id);
        return Admin::content(function (Content $content) use ($id) {

            $content->header('List Pekerjaan');
            $content->description('');

            $content->body($this->grid_pekerjaan($id));
        });
    }

    public function grid_pekerjaan($id)
    {
        Admin::script($this->script());
        return Admin::grid(JadwalAset::class, function (Grid $grid) use ($id) {

            $grid->uraian('Uraian Pekerjaan');
            

            $grid->column('Frekuensi')->display(function(){
                    return "<select class='form-control pilih_{$this->id} select' id='pilih_{$this->id}' data-id='{$this->id}'></select>";
                
            });

            $grid->disableFilter();
            $grid->disableExport();
            $grid->disableCreation();
            $grid->disableRowSelector();
            $grid->disableActions();
        });
    }

    public function generate_pekerjaan($id)
    {
        $ParamTahun = Parameter::where('id' , 1)->get();
        $DataKetersediaan = KetersediaanSla::where('aset_id' , $id)
                                            ->where('tahun' , $ParamTahun[0]['value'])
                                            ->where('ketersediaan' , 1)
                                            ->get();

        foreach ($DataKetersediaan as $key => $value) {
            $cek = JadwalAset::where('tahun' , $ParamTahun[0]['value'])
                                ->where('aset_id' , $value->aset_id)
                                ->where('sla_id' , $value->sla_id)
                                ->where('detail_sla_id' , $value->detail_sla_id)
                                ->get();
            
            if(Count($cek) == 0)
            {
                $insert = new JadwalAset();
                $insert->tahun          = $ParamTahun[0]['value'];
                $insert->aset_id        = $value->aset_id;
                $insert->sla_id         = $value->sla_id;
                $insert->detail_sla_id  = $value->detail_sla_id;
                $insert->uraian         = $value->uraian;
                $insert->save();
            }
        }
    }

    public function list_aset_jadwal()
    {
        return Admin::content(function (Content $content) {

            $content->header('List Pekerjaan');
            $content->description('');

            $content->body($this->grid_aset_jadwal());
        });
    }

    public function grid_aset_jadwal()
    {
        $roles      = Admin::user()->roles;
        $idRoles    = $roles[0]['id'];

            $user           = Admin::user();
            $idGroupArea    = $user['groupArea'];
            $idGroupWill    = $user['groupWil'];
            $idAset         = $user['aset_id'];

        return Admin::grid(JadwalAset::class, function (Grid $grid) use ($idAset) {
            $grid->model()->where('aset_id' , $idAset);


            $grid->uraian('Uraian Pekerjaan');
            $grid->uraian_frekuensi('Frekuensi');

            $grid->disableFilter();
            $grid->disableExport();
            $grid->disableCreation();
            $grid->disableRowSelector();
            $grid->actions(function($actions){
                $actions->disableDelete();
                $actions->disableEdit();
                $actions->disableView();

                $Data = JadwalAset::where('id' , $actions->getKey())->get();

                if(!empty($Data[0]['frekuensi_id']))
                {
                 $actions->append("<a href='/admin/JadwalAsets/". $actions->getKey() ."/JadwalTindakLanjuts' class='btn btn-xs btn-primary'><i class='fa fa-check'></i></a>");   
                }

                
            });
        });
    }

    public function list_jadwal_pekerjaan()
    {
        return Admin::content(function (Content $content) {

            $content->header('List ');
            $content->description('');

            $content->body($this->grid_jadwal_pekerjaan());
        });
    }

    public function grid_jadwal_pekerjaan()
    {
        return Admin::grid(JadwalAset::class, function (Grid $grid)  {

            $grid->uraian('Uraian Pekerjaan');
            $grid->uraian_frekuensi('Frekuensi');

            $grid->disableFilter();
            $grid->disableExport();
            $grid->disableCreation();
            $grid->disableRowSelector();
            $grid->actions(function($actions){
                $actions->disableDelete();
                $actions->disableEdit();
                $actions->disableView();

                $Data = JadwalAset::where('id' , $actions->getKey())->get();

                if(!empty($Data[0]['frekuensi_id']))
                {
                 $actions->append("<a href='/admin/generate_tanggal/tindaklanjut/". $actions->getKey() ."' class='btn btn-xs btn-primary'><i class='fa fa-check'></i></a>");   
                }

                
            });
        });
    }

    public function generate_jadwal($id)
    {
        $DataJadwal         = JadwalAset::where('id' , $id)->get();
        $DetailFrekuensi    = DetailFrekuensi::where('frekuensi_id' , $DataJadwal[0]['frekuensi_id'])->get();

        foreach ($DetailFrekuensi as $key => $value) {
            $cek = JadwalTindakLanjut::where('jadwal_sla_aset_id' , $id)
                                    ->where('tanggal' , $value->tanggal)
                                    ->get();
            if(Count($cek) == 0)
            {
                $insert                     = new JadwalTindakLanjut();
                $insert->jadwal_sla_aset_id = $id;
                $insert->tanggal            = $value->tanggal;
                $insert->save();
            }
        }

        return redirect('/admin/list/tanggal/tindaklanjut/'. $id .'');
    }

    public function list_tanggal($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('List ');
            $content->description('');

            $content->body($this->grid_tanggal($id));
        });
    }

    public function grid_tanggal($id)
    {
        return Admin::grid(JadwalTindakLanjut::class, function (Grid $grid)  {

            $grid->tanggal('Tanggal');
            

            $grid->disableFilter();
            $grid->disableExport();
            $grid->disableCreation();
            $grid->disableRowSelector();
            $grid->actions(function($actions){
                $actions->disableDelete();
                $actions->disableEdit();
                $actions->disableView();

                $actions->append("<a href='/admin/JadwalTindakLanjuts/". $actions->getKey() ."/edit'>Tindaklanjut</a>");
            });
        });
    }

    public function edit_tindaklanjut($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form_tindaklanjut()->edit($id));
        });
    }

    protected function form_tindaklanjut()
    {
        return Admin::form(JadwalTindakLanjut::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

    protected function simpan(Request $request)
    {
        return $request; 
    }

    protected function list_aset_leader()
    {

    }

    protected function grid_aset_leader()
    {
        
    }


    protected function script()
    {
        return <<<SCRIPT

        $.getJSON('/admin/getfrekuensi2', function (result) {

            for (var i = 0; i < result['DataJadwalAset'].length; i++) {
                $('.pilih_' + result['DataJadwalAset'][i].id + '').append("<option value=0>-- Pilih --</option>");
                    for (var j = 0; j < result['Datafrekuensi'].length; j++) {

                        if(result['DataJadwalAset'][i].frekuensi_id == result['Datafrekuensi'][j].id)
                        {
                        $('.pilih_' + result['DataJadwalAset'][i].id + '').append("<option value="+ result['Datafrekuensi'][j].id +" selected>"+ result['Datafrekuensi'][j].uraian +"</option>");
                        }
                        else
                        {
                         $('.pilih_' + result['DataJadwalAset'][i].id + '').append("<option value="+ result['Datafrekuensi'][j].id +">"+ result['Datafrekuensi'][j].uraian +"</option>");   
                        }

                    }
                }
        });

$('.select').on('change', function () {

// Your code.
console.log($(this).val());

        var id      = $(this).data('id');
        var nilai   = $(this).val();

        $.ajax({
                url: '/admin/get/jadwal/aset/' + id + '/' + nilai,
                type: 'GET',
                success: function(response)
                {
                    // console.log(response);
                    console.log('Sukses');
                }
            });
});





SCRIPT;
    }


}
