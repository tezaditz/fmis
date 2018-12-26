<?php

namespace App\Admin\Controllers;

use App\Models\DJadwalRequest;
use App\Models\Permintaan;
use App\Models\TindaklanjutRequest;
use App\Models\MJadwalRequest;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class DJadwalRequestController extends Controller
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

            $content->header('header');
            $content->description('description');

            $content->body($this->gridMaster());
        });
    }

    public function index2($mjadwalcomplain)
    {
        return Admin::content(function (Content $content) use ($mjadwalcomplain) {

            $content->header('Master');
            $content->description($mjadwalcomplain);

            $content->body($this->grid($mjadwalcomplain));
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
    protected function grid($mjadwalrequest)
    {
        
        return Admin::grid(DJadwalRequest::class, function (Grid $grid) use ($mjadwalrequest) {
            $grid->model()->where('m_jadwalrequest_id' , $mjadwalrequest);

            $grid->id('ID')->sortable();
            $grid->tanggal('Tanggal Pelaksanaan');
            $grid->uraian('Uraian Pelaksanaan');
            $grid->uraian('Uraian Pelaksanaan');
            $grid->foto_sebelum('Foto Sebelum')->image();
            $grid->foto_sesudah('Foto Sesudah')->image();


            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();

                $actions->append("<a href='/admin/djadwalrequests/". $actions->getKey() . "/edit' class='btn btn-xs'><i class='fa fa-check'></i></a>");
            });
        });
    }

    protected function gridMaster()
    {
        return Admin::grid(MJadwalRequest::class, function (Grid $grid) {

            $grid->disableCreation();
            $grid->disableRowSelector();
            $grid->disableExport();
            $grid->disableActions();


            $grid->permintaan()->nomor();
            $grid->permintaan()->nama_pemohon();
            $grid->permintaan()->unit_pelapor();


            $grid->column('Status permintaan')->display(function () {            
                
                if($this->flag == 0)
                {
                    return '<a href="/admin/mjadwalrequests/'. $this->id .'/edit" class="btn btn-xs btn-danger">Belum Terjadwal</a>';
                }
                elseif($this->flag == 1)
                {
                    return '<a href="/admin/mjadwalrequests/'. $this->id .'/djadwalrequests" class="btn btn-xs btn-success">Terjadwal</a>';   
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
        return Admin::form(DJadwalRequest::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->display('tanggal');
            $form->textarea('uraian');
            $form->text('keterangan');
            $form->image('foto_sebelum');
            $form->image('foto_sesudah');
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
