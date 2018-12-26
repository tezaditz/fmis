<?php

namespace App\Admin\Controllers;

use App\Models\SlaAset;
use App\Models\Aset;
use App\Models\Sla;
use App\Models\Penilaian;
use App\Models\DetailSla;
use App\Models\Parameter;
use App\Models\KetersediaanSla;
use App\Models\WilayahArea;


use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

use Encore\Admin\Grid\Tools\BatchAction;


use App\Admin\Extensions\Tools\ReleasePost;
use App\Admin\Extensions\CheckRow;
use App\Admin\Extensions\Tools\UserGender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;

class SlaAsetController extends Controller
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

            $content->header('Setting SLA');
            $content->description('List Aset');

            $content->body($this->gridAset());
        });
    }

    public function index2()
    {
        return Admin::content(function (Content $content) {

            $content->header('Setting Ketersediaan');
            $content->description('List Aset');

            $content->body($this->gridAset_ketersediaan());
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
        $this->create_list_sla($id);
        return Admin::content(function (Content $content) use ($id) {

            $content->header('Memilih SLA');
            $content->description('');

            $content->body($this->form()->edit($id));
            $content->body($this->gridSla($id));
        });
    }

    public function edit2($id)
    {
        $this->create_list_sla_detail($id);
        return Admin::content(function (Content $content) use ($id) {

            $content->header('Setting Ketersediaan');
            $content->description('');

            $content->body($this->form()->edit($id));
            $content->body($this->gridSla2($id));
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

        return Admin::grid(SlaAset::class, function (Grid $grid) {


            $grid->id('ID')->sortable();

            $grid->created_at();
            $grid->updated_at();
        });
    }

    protected function gridAset()
    {
        $roles      = Admin::user()->roles;
        $idRoles    = $roles[0]['id'];

            $user       = Admin::user();
            $idGroupArea    = $user['groupArea'];
            $idGroupWill    = $user['groupWil'];
            $idAset         = $user['aset_id'];
            


            switch ($idRoles) {
                case 4:
                    $DataWill = WilayahArea::where('wilayah_id' , $idGroupWill)->get(['id']);
                    break;
                case 8:
                    $DataWill = WilayahArea::where('id' , $idGroupArea)->get(['id']);
                    break;
                default:
                    $DataWill = WilayahArea::get(['id']);
                    break;
            }
        
        return Admin::grid(Aset::class, function (Grid $grid) use ($idRoles , $idAset , $DataWill) {
                $grid->model()->whereIn('wilayah_area_id' , $DataWill);

            
            
            $grid->nama('Nama Aset');
            $grid->address('Alamat Aset');

            $grid->disableFilter();
            $grid->disableExport();
            $grid->disableCreation();
            $grid->disableRowSelector();


            switch ($idRoles) {

                case 8:
                    $grid->disableActions();
                    break;
                default:
                       $grid->actions(function ($actions) use ($idRoles){

                            $actions->disableDelete();
                            $actions->disableView();
                        });
                    break;
            }


           });
    }

    protected function gridAset_ketersediaan()
    {
        $roles      = Admin::user()->roles;
        $idRoles    = $roles[0]['id'];

        $user       = Admin::user();
        $idGroupArea    = $user['groupArea'];
        $idGroupWill    = $user['groupWil'];
        $idAset         = $user['aset_id'];

         
        return Admin::grid(Aset::class, function (Grid $grid) use ($idGroupWill , $idGroupArea , $idRoles , $idAset) {
            switch ($idRoles) {
                case 4:
                        $DataWill = WilayahArea::where('wilayah_id' , $idGroupWill)->get(['id']);
                    break;
                case 5:
                        $DataWill = WilayahArea::where('id' , $idGroupArea)->get(['id']);
                    break;
                case 8:
                    $DataWill = WilayahArea::where('id' , $idGroupArea)->get(['id']);
                    break;
                default:
                    $DataWill = WilayahArea::get(['id']);
                    break;
            }
            $grid->model()->whereIn('wilayah_area_id' , $DataWill);    

            
            $grid->nama('Nama Aset');
            $grid->address('Alamat Aset');

            $grid->disableFilter();
            $grid->disableExport();
            $grid->disableCreation();
            $grid->disableRowSelector();
            switch ($idRoles) {

                case 8:
                    $grid->disableActions();
                    break;
                default:
                       $grid->actions(function ($actions){

                            $actions->disableDelete();
                            $actions->disableView();
                        });
                    break;
            }


        });
    }

    protected function gridSla($id)
    {
        $roles      = Admin::user()->roles;
        $idRoles    = $roles[0]['id'];

            $user           = Admin::user();
            $idGroupArea    = $user['groupArea'];
            $idGroupWill    = $user['groupWil'];
            $idAset         = $user['aset_id'];

            if($idGroupArea == 1)
            {
                $DataWill   = WilayahArea::where('wilayah_id' , $user['groupWil'])->get(['id']);    
            }
            else
            {
                $DataWill   = WilayahArea::where('id' , $user['groupArea'])->get(['id']);
            }
        
        
        return Admin::grid(SlaAset::class, function (Grid $grid) use ($id , $idGroupWill , $idGroupArea , $idRoles) {
            $grid->model()->where('aset_id' , '=' , $id);
            $grid->Sla()->uraian('Uraian SLA');

            $grid->disableFilter();
            $grid->disableExport();
            $grid->disableCreation();
            $grid->disableRowSelector();
            $grid->disablePagination();

            $grid->tools(function($tools) use ($idGroupWill , $idGroupArea , $idRoles){
                if($idGroupArea == 1)
                {
                    $tools->disableRefreshbutton();
                    $tools->append("<a href ='' class='btn btn-sm btn-success pull-right'><i class='fa fa-save'></i> Simpan</a>");  
                }
            });
            
            $grid->actions(function($actions) use ($idGroupWill , $idGroupArea , $idRoles){
                $actions->disableEdit();
                $actions->disableDelete();
                $actions->disableView();
                $Data = SlaAset::where('id' , '=' ,$actions->getKey())->get();
                if($idGroupArea == 1)
                {
                    $actions->append(new CheckRow($actions->getKey() , $Data[0]['aktif'] ));  
                }
                else
                {
                    if($Data[0]['aktif'] == 1)
                    {
                        $actions->append('<input type="checkbox" checked disabled>');    
                    }
                    else
                    {
                        $actions->append('<input type="checkbox" disabled>');    
                    }
                    
                }

            });
            

        });
    }

    protected function gridSla2($id)
    {
        $this->refresh_ketersediaan($id);
        Admin::Script($this->script());

        $roles      = Admin::user()->roles;
        $idRoles    = $roles[0]['id'];

            $user           = Admin::user();
            $idGroupArea    = $user['groupArea'];
            $idGroupWill    = $user['groupWil'];
            $idAset         = $user['aset_id'];

            if($idGroupArea == 1)
            {
                $DataWill   = WilayahArea::where('wilayah_id' , $user['groupWil'])->get(['id']);    
            }
            else
            {
                $DataWill   = WilayahArea::where('id' , $user['groupArea'])->get(['id']);
            }
        
        return Admin::grid(KetersediaanSla::class, function (Grid $grid) use ($id , $idGroupWill , $idGroupArea , $idRoles) {
                $grid->model()->where('aset_id' , '=' , $id);
                $grid->model()->OrderBy('sla_id' , 'Asc');
                $grid->model()->OrderBy('detail_sla_id' , 'Asc');

            $grid->disableFilter();
            $grid->disableExport();
            $grid->disableCreation();
            $grid->disableRowSelector();
            $grid->disablePagination();
            $grid->disableActions();

            $grid->tools(function($tools) use ($idGroupWill , $idGroupArea , $idRoles){
                if($idGroupArea == 1)
                {
                    $tools->disableRefreshbutton();
                    $tools->append("<a href ='' class='btn btn-sm btn-success pull-right'><i class='fa fa-save'></i> Simpan</a>");  
                }
            });

            $grid->column('Uraian Pekerjaan')->display(function(){
                if($this->detail_sla_id == 0)
                {
                    return '<strong>' . $this->uraian . '</strong>';    
                }
                else
                {
                    return $this->uraian;
                }
            });

            $grid->column('Ketersediaan Fasilitas')->display(function() use ($idGroupWill , $idGroupArea , $idRoles){
                // return $this->ketersediaan;

                if($this->detail_sla_id != 0)
                {
                    if($this->ketersediaan == 1)
                    {
                        if($idGroupArea == 1)
                        {
                            return "<input type='checkbox' class='grid-check-row'  data-id='{$this->id}' checked/>";     
                        }
                        else
                        {
                            return "<input type='checkbox' class='grid-check-row'  data-id='{$this->id}' checked disabled/>";
                        }
                        
                    }
                    else
                    {
                        if($idGroupArea == 1)
                        {
                            return "<input type='checkbox' class='grid-check-row'  data-id='{$this->id}'/>";
                        }
                        else
                        {
                            return "<input type='checkbox' class='grid-check-row'  data-id='{$this->id}'disabled/>";
                        }
                    }  
                }
                else
                {
                    return '';
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
        return Admin::form(Aset::class, function (Form $form) {
            


            $form->display('nama' , 'Nama Aset');

            $form->disableSubmit();
            $form->disableReset();


        });
    }

    public function update_slaaset($id )
    {
        
        $Data = SlaAset::where('id' , '=', $id)->get();
        
        if(Count($Data) > 0)
        {

            if($Data[0]['aktif'] == 0)
            {
                $Data = SlaAset::where('id' , '=', $id)
                    ->Update([
                        'aktif' => 1
                    ]);

            }
            else
            {
                $Data = SlaAset::where('id' , '=', $id)
                    ->Update([
                        'aktif' => 0
                    ]);
            }      
        }
        return response()->json($id);
    }

    public function sla(Request $request)
    {
        $aset_id = $request->get('q');
        $DataSla = SlaAset::where('aset_id' , $aset_id)
                            ->where('aktif' , 1)->get();

        if(Count($DataSla) > 1)
        {
            foreach ($DataSla as $key => $value) {
                $Sla = Sla::where('id' , $value->sla_id)->get(['uraian']);

                $Data[] = [
                    'id'        => $value->sla_id,
                    'text'    => $Sla[0]['uraian']
                ];
            }    
        }
        else
        {
            $Data[] = [];
        }
        

        return $Data;
    }

    public function create_list_sla($id)
    {
        $DataSlaAset = SlaAset::where('aset_id' , $id)->get();

        if(Count($DataSlaAset) == 0)
        {
            $DataSLA = Sla::all();
            foreach ($DataSLA as $key => $value) {
                $insert                 = new SlaAset();
                $insert->aset_id        = $id;
                $insert->sla_id         = $value->id;
                $insert->save();
            }



        }
    }

    public function create_list_sla_detail($id)
    {
        $DataSlaAset = SlaAset::where('aset_id' , $id)
                                ->where('aktif' , 1)->get();
        $ParamTahun     = Parameter::where('id' , 1)->get(['value']);

        foreach ($DataSlaAset as $key => $value) {
            $CekDataPenilaian = KetersediaanSla::where('tahun' , $ParamTahun[0]['value'])
                                                ->where('aset_id' , $value->aset_id)
                                                ->where('sla_id' , $value->sla_id)
                                                ->get();

            if(Count($CekDataPenilaian) == 0)
            {
                $insert             = new KetersediaanSla();
                $insert->tahun      = $ParamTahun[0]['value'];
                $insert->sla_id     = $value->sla_id;
                $insert->aset_id    = $value->aset_id;
                $insert->detail_sla_id = 0;
                
                $DataSLA = Sla::where('id' , $value->sla_id)->get();
                $insert->uraian     = $DataSLA[0]['uraian'];
                $insert->save();
            }

            $DataDetail = DetailSla::where('sla_id' , $value->sla_id)->get();

            foreach ($DataDetail as $key2 => $value2) {
                $CekDataPenilaian = KetersediaanSla::where('tahun' , $ParamTahun[0]['value'])
                                                ->where('aset_id' , $value->aset_id)
                                                ->where('detail_sla_id' , $value2->id)
                                                ->get();
                if(Count($CekDataPenilaian) == 0)
                {
                    $insert             = new KetersediaanSla();
                    $insert->tahun      = $ParamTahun[0]['value'];
                    $insert->sla_id      = $value->sla_id;
                    $insert->aset_id    = $id;
                    $insert->detail_sla_id = $value2->id;
                    $insert->uraian     = $value2->uraian;
                    $insert->save();
                }
            }
        }
    }

    public function update_ketersediaan($id)
    {
        
        $Data = KetersediaanSla::where('id' , '=', $id)->get();
        
        if(Count($Data) > 0)
        {
            if($Data[0]['ketersediaan'] == 0)
            {
                $Data = KetersediaanSla::where('id' , '=', $id)
                    ->Update([
                        'ketersediaan' => 1
                    ]);
            }
            else
            {
                $Data = KetersediaanSla::where('id' , '=', $id)
                    ->Update([
                        'ketersediaan' => 0,
                    ]);
            }      
        }
        return response()->json($id);
    }

    public function refresh_ketersediaan($id)
    {
        $DataSlaAset        = SlaAset::where('aset_id' , $id)->get();

        foreach ($DataSlaAset as $key => $value) {
            if($value->aktif == 0)
            {
                $DataKetersediaan = KetersediaanSla::where('aset_id' , $id)
                                    ->where('sla_id' , $value->sla_id)
                                    ->delete();        
            }
        }
        
    }

 protected function script()
    {
        return <<<SCRIPT



$("#checkAll").click(function(){
    $('input:checkbox').not(this).prop('checked', this.checked);

    var Id = $(this).data('id');

    $.ajax({
                url: '/admin/update/ketersediaan/all/' + Id,
                type: 'GET',
                success: function(response)
                {
                    console.log('Sukses');
                }
            });
});

$('.grid-check-row').on('click', function () {

    // Your code.
    
    var Id = $(this).data('id');
    console.log(Id);
  

    $.ajax({
                url: '/admin/update/ketersediaan_sla/' + Id,
                type: 'GET',
                success: function(response)
                {
                    console.log('Sukses');
                }
            });
});









SCRIPT;
    }
}
