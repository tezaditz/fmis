<?php

namespace App\Admin\Controllers;

use App\Models\JadwalAset;
use App\Models\Aset;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class JadwalAsetController extends Controller
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

            $content->header('Aset');
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
         return Admin::grid(JadwalAset::class, function (Grid $grid)  {
            $grid->model()->groupby('aset_id');



            $grid->actions(function ($actions){
                $actions->disableDelete();
                $actions->disableEdit();
                $actions->disableView();

                $Data = JadwalAset::where('id' , $this->getKey())->get();


                $actions->append('<a href = "/admin/pilih/jadwalaset/'. $Data[0]['aset_id'] .'" class="btn btn-xs"><i class="fa fa-edit"></i></a>');
            });


            $grid->aset('nama')->nama();
            $grid->aset('alamat')->address();

        });
    }

    public function grid_aset()
    {
        return Admin::grid(JadwalAset::class, function (Grid $grid)  {
            $grid->model()->groupby('aset_id');



            $grid->actions(function ($actions){
                $actions->disableDelete();
                $actions->disableEdit();
                $actions->disableView();

                $Data = JadwalAset::where('id' , $this->getKey())->get();


                $actions->append('<a href = "/admin/pilih/jadwalaset/'. $Data[0]['aset_id'] .'" class="btn btn-xs"><i class="fa fa-edit"></i></a>');
            });


            $grid->aset('nama')->nama();
            $grid->aset('alamat')->address();

        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(JadwalAset::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
