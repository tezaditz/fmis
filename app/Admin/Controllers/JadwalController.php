<?php

namespace App\Admin\Controllers;

use App\Models\Jadwal;
use App\Models\Aset;
use App\Models\SettingJadwal;
use App\Models\Sla;
use App\Models\DetailSla;
use App\Models\Frekuensi;
use App\Models\Wilayah;
use App\Models\WilayahArea;
use App\Models\JadwalAset;
use App\Models\AdminRoles;
use App\Models\AdminRoleUsers;
use App\Models\KetersediaanSla;
use App\Models\Parameter;
use Carbon;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\InfoBox;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Auth\Database\Role;

class JadwalController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        
        Admin::script($this->script());
        return Admin::content(function (Content $content) {
        
            $content->header('Master Jadwal');
            $content->description('');

            $content->body($this->grid_aset());

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
    protected function grid_jadwal($idAset)
    {
        
        Admin::script($this->script());
        return Admin::grid(JadwalAset::class , function (Grid $grid) use ($idAset){
                $grid->model()->where('aset_id' , $idAset);

            $grid->disableRowSelector();
            $grid->disableCreation();
            $grid->disableExport();
            $grid->disableactions();
            $grid->disablepagination();

            $grid->uraian('Uraian');

            $frekuensi = Frekuensi::all(["id" , "uraian"]);

            foreach ($frekuensi as $key => $value) {
                $idfrekuensi[$key] = $value->id;
                $uraian_frekuensi[$key] = $value->uraian;
            };

            $grid->column('Frekuensi')->display(function(){
                    return "<select class='form-control pilih_{$this->id} select' id='pilih_{$this->id}' data-id='{$this->id}'></select>";
                
            });
            

        });
    }

    protected function grid_aset()
    {
        $roles      = Admin::user()->roles;
        $idRoles    = $roles[0]['id'];
        $user           = Admin::user();
        $idGroupArea    = $user['groupArea'];
        $idGroupWill    = $user['groupWil'];
        $idAset         = $user['aset_id'];      

        $DataWill = WilayahArea::where('id' , $idGroupArea)->get(['id']);

        // return $DataWill;


        return Admin::grid(Aset::class , function (Grid $grid) use ($idRoles , $idGroupArea , $idGroupWill , $idAset ){
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
            $grid->model()->whereIn('wilayah_area_id' , $DataWill);
           
            

            $grid->disableRowSelector();
            $grid->disableCreation();
            $grid->disableExport();


            $grid->nama('Nama Aset');
            $grid->provinsi()->name();
            $grid->kota()->name();
            $grid->address('Alamat');

            $grid->actions(function($action){
                $action->disableDelete();
                $action->disableEdit();
                $action->disableView();

                $action->append("<a href='/admin/get/jadwal/aset/". $action->getKey() ."' class='btn btn-xs' ><i class='fa fa-check'></i></a>");
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
        return Admin::form(Jadwal::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

    public function generate_jadwal()
    {

        $DataSetting = SettingJadwal::where('generate' , 0)->get();

        if(Count($DataSetting) > 0)
        {
            foreach ($DataSetting as $key => $value) {

                $aset = Aset::where('id' , $value->aset_id)->get();


                $Cek        = Jadwal::where('level' ,0)
                                ->where('keterangan' , 'SLA')
                                ->where('aset_id' , $value->aset_id)
                                ->where('id_ket' , $value->sla_id)
                                ->get();
                
                if(Count($Cek) == 0)
                {
                    $uraian_SLA = Sla::where('id' , $value->sla_id)->get(['uraian']);
                    

                    $insert_SLA = new Jadwal();
                    $insert_SLA->id_ket     = $value->sla_id;
                    $insert_SLA->keterangan = 'SLA';
                    $insert_SLA->level      = 0;
                    $insert_SLA->uraian     = $uraian_SLA[0]['uraian'];
                    $insert_SLA->aset_id    = $value->aset_id;
                    $insert_SLA->nama_aset  = $aset[0]['nama'];
                    $insert_SLA->address    = $aset[0]['address'];
                    $insert_SLA->save();
                }

                $Cek_Detail = Jadwal::where('level' , 1)
                                    ->where('keterangan' , 'Detail SLA')
                                    ->where('aset_id' , $value->aset_id)
                                    ->where('id_ket' , $value->detail_sla_id)
                                    ->where('setting_jadwal_id' , $value->id)
                                    ->get();

                if(Count($Cek_Detail) == 0)
                {
                   $uraian = DetailSla::where('id' , $value->detail_sla_id)->get(['uraian']);
                    
                    $insert_SLA                         = new Jadwal();
                    $insert_SLA->id_ket                 = $value->sla_id;
                    $insert_SLA->setting_jadwal_id      = $value->id;
                    $insert_SLA->keterangan             = 'Detail SLA';
                    $insert_SLA->level                  = 1;
                    $insert_SLA->uraian                 = $uraian[0]['uraian'];
                    $insert_SLA->aset_id                = $value->aset_id;
                    $insert_SLA->nama_aset              = $aset[0]['nama'];
                    $insert_SLA->address                = $aset[0]['address'];

                    $Datafrekuensi = Frekuensi::where('id' , $value->frekuensi_id)->get();
                    $DataWaktu = $Datafrekuensi[0]['waktu_id'];

                    switch ($DataWaktu) {
                        case 1:
                            //Harian
                        $insert_SLA->jan_1 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->jan_2 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->jan_3 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->jan_4 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->feb_1 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->feb_2 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->feb_3 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->feb_4 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->mar_1 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->mar_2 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->mar_3 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->mar_4 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->apr_1 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->apr_2 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->apr_3 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->apr_4 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->may_1 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->may_2 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->may_3 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->may_4 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->jun_1 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->jun_2 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->jun_3 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->jun_4 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->jul_1 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->jul_2 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->jul_3 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->jul_4 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->aug_1 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->aug_2 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->aug_3 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->aug_4 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->sep_1 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->sep_2 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->sep_3 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->sep_4 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->oct_1 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->oct_2 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->oct_3 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->oct_4 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->nov_1 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->nov_2 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->nov_3 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->nov_4 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->dec_1 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->dec_2 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->dec_3 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        $insert_SLA->dec_4 = $Datafrekuensi[0]['kode'] . ';' . $Datafrekuensi[0]['warna'];
                        
                            break;
                        
                        default:
                            # code...
                            break;
                    }



                    $insert_SLA->save();
                }
            }

            $update_setting = SettingJadwal::where('id' , $value->id)
                    ->where('id' , '=' , $value->id)
                    ->update([
                        'generate'  => 1
                    ]);
        }
    }


    public function generateJadwalAset($idaset)
    {

        $ParamTahun = Parameter::where('id' , 1)->get();

        $DataKetersediaan = KetersediaanSla::where('aset_id' , $idaset)
                                            ->where('tahun' , $ParamTahun[0]['value'])
                                            ->where('ketersediaan' , 0)
                                            ->get();

        foreach ($DataKetersediaan as $key => $value) {
            $cek = JadwalAset::where('tahun' , $ParamTahun[0]['value'])
                                ->where('aset_id' , $value->aset_id)
                                ->where('sla_id' , $value->sla_id)
                                ->where('detail_sla_id' , $value->detail_sla_id)
                                ->get();
            if(Count($cek) != 0)
            {
                $hapus = JadwalAset::where('tahun' , $ParamTahun[0]['value'])
                                ->where('aset_id' , $value->aset_id)
                                ->where('sla_id' , $value->sla_id)
                                ->where('detail_sla_id' , $value->detail_sla_id)
                                ->delete();
            }

        }




        $DataKetersediaan = KetersediaanSla::where('aset_id' , $idaset)
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

        return Admin::content(function (Content $content) use ($idaset) {
            
            $content->header('List');
            $content->description('SLA Aset');
            $content->body($this->form_edit($idaset)->edit($idaset));
            $content->body($this->grid_jadwal($idaset));

        });
    }

    public function form_edit($id)
    {
        return Admin::form(Aset::class, function (Form $form) use ($id) {
            $form->model()->where('id' , $id);

            $form->tools(function(Form\Tools $tools){
                $tools->disableBackButton();
    
                // Disable list btn
                $tools->disableListButton();
                
                $tools->add('<div class="btn-group pull-right" style="margin-right: 10px">
    <a href="/admin/jadwals" class="btn btn-sm btn-default form-history-back"><i class="fa fa-arrow-left"></i>&nbsp;Back</a>
</div>');
            });


            $form->display('id', 'ID');
            $form->display('nama', 'Nama Aset');
            $form->display('address', 'Alamat Aset');
          

            $form->disableSubmit();
            $form->disableReset();

        });
    }

    public function editfrekuensi($id , $nilai)
    {
        $Datafrekuensi = Frekuensi::where('id' , $nilai)->get();
        $uraian_frekuensi = $Datafrekuensi[0]['uraian'];

        $Data = JadwalAset::where('id' , $id)
                            ->update([
                                'frekuensi_id'      => $nilai,
                                'uraian_frekuensi'  => $uraian_frekuensi
                            ]);
    }

    public function getfrekuensi2()
    {
        $DataJadwalAset = JadwalAset::all(['id' , 'frekuensi_id']);
        $Datafrekuensi = Frekuensi::all(['id' , 'uraian']);

        $Data = [
            'DataJadwalAset' => $DataJadwalAset,
            'Datafrekuensi' => $Datafrekuensi
        ];

        return $Data;
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
