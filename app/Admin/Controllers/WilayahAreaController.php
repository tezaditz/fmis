<?php

namespace App\Admin\Controllers;

use App\Models\WilayahArea;
use App\Models\Wilayah;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class WilayahAreaController extends Controller
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

            $content->header('Area');
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

            $content->header('Area');
            $content->description('Edit');

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

            $content->header('Area');
            $content->description('New');

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
        return Admin::grid(WilayahArea::class, function (Grid $grid) {

            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->filter(function($filter){
                $filter->disableIdFilter();


                $filter->like('nama_area' , 'Area');

            });

            // $grid->id('ID')->sortable();
            $grid->wilayah()->nama('Wilayah');
            $grid->nama_area('Area');
            $grid->created_at();
            $grid->updated_at();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(WilayahArea::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->select('wilayah_id' , 'Wilayah')->options(
                    Wilayah::all()->pluck('nama', 'id')
                );
            $form->text('nama_area' , 'Nama Area');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
