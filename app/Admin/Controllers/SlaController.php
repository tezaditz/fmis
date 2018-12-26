<?php

namespace App\Admin\Controllers;

use App\Models\sla;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class SlaController extends Controller
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

            $content->header('Service Level Agreement');
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
            $content->description('Service Level Agreement');

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
            $content->description('Service Level Agreement');

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

        return Admin::grid(sla::class, function (Grid $grid) use ($idGroupWill , $idGroupArea , $idAset , $idRoles) {

            $grid->filter(function($filter){

            // Remove the default id filter
            $filter->disableIdFilter();

            // Add a column filter
            $filter->like('uraian', 'uraian');
            

            });


            $grid->id('ID')->sortable();
            $grid->uraian('Uraian');

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
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(sla::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('uraian' , 'Uraian')->rules('required');
        });
    }
}
