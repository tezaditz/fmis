<?php

namespace App\Admin\Controllers;

use App\Models\DetailSla;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

use App\Models\Sla;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetailSlaController extends Controller
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

            $content->header('Detail SLA');
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

            $content->header('Ubah');
            $content->description('Detail SLA');

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

            $content->header('Membuat Baru');
            $content->description('Detail SLA');

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

        return Admin::grid(DetailSla::class, function (Grid $grid) use ($idGroupWill , $idGroupArea , $idAset , $idRoles) {
            $grid->model()->orderBy('sla_id' , 'asc');

            $grid->filter(function($filter){
                $filter->like('Uraian' , 'uraian');

                $filter->like('sla.uraian' , 'Uraian SLA');
            });

            $grid->disableExport();
            $grid->disableRowSelector();

                if($idRoles != 1 && $idRoles != 2 && $idRoles != 3)
                {
                      $grid->disableActions();
                      $grid->disableCreation();              
                }
                else
                {
                    $grid->actions(function($actions)  {
                        $actions->disableView();
                    });
                }
            $grid->sla()->uraian('Uraian SLA')->sortable();
            $grid->uraian('Uraian Detail');
        });
    }

    protected function gridCreate()
    {
        return Admin::grid(DetailSla::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->sla()->uraian('Uraian SLA');
            $grid->uraian('Uraian Detail');

            $grid->disableFilter();
            $grid->disableExport();
            $grid->disableCreation();
            $grid->disableRowSelector();
            $grid->actions( function ($action) {
                $action->disableEdit();
                $action->disableDelete();

                
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
        return Admin::form(DetailSla::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->select('sla_id' , 'SLA')->options(
                    Sla::all()->pluck('uraian', 'id')
                )->rules('required');
            $form->text('uraian' , 'Uraian Detail')->rules('required');


        });
    }

    public function detailsla(Request $request)
    {
        $id = $request->get('q');
        $CekData = DetailSla::where('sla_id' , $id)->get();
        
        
        if(Count($CekData) > 0)
        {
            foreach ($CekData as $key => $value) {
                $Data[] = [
                    'id'        => $value->id,
                    'text'      => $value->uraian
                ];
            }    
        }
        else
        {
            $Data[] = [];
        }  

        return $Data;
    }
}
