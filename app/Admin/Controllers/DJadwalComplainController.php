<?php

namespace App\Admin\Controllers;

use App\Models\DJadwalComplain;
use App\Models\MJadwalComplain;
use App\Models\Complaint;
use App\Models\Jenis_Complain;
use App\Models\Aset;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;

class DJadwalComplainController extends Controller
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

            $content->header('Master');
            $content->description('Jadwal');

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
    protected function grid($mjadwalcomplain)
    {
        return Admin::grid(DJadwalComplain::class, function (Grid $grid) use ($mjadwalcomplain) {
            $grid->model()->where('m_jadwalcomplain_id' , $mjadwalcomplain);

            // $grid->id('ID')->sortable();
            $grid->tanggal('Tanggal Pelaksanaan');
            $grid->uraian('Uraian Pelaksanaan');
            $grid->uraian('Uraian Pelaksanaan');
            $grid->foto_sebelum('Foto Sebelum')->image();
            $grid->foto_sesudah('Foto Sesudah')->image();

            $grid->disableCreation();
            $grid->disableExport();
            $grid->disableRowSelector();

            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
                $actions->disableView();

                $actions->append("<a href='/admin/djadwalcomplains/". $actions->getKey() . "/edit' class='btn btn-xs'><i class='fa fa-check'></i></a>");
            });

        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */


    protected function gridMaster()
    {
        return Admin::grid(MJadwalComplain::class, function (Grid $grid) {

            $grid->disableCreation();
            $grid->disableRowSelector();
            $grid->disableExport();
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
                    return '<a href="/admin/mjadwalcomplains/'. $this->id .'/djadwalcomplains" class="btn btn-xs btn-success">Terjadwal</a>'; 


                }
            });
             $grid->column('Lihat Pekerjaan')->display(function () {            
                
                    return '<a href="/admin/complain/'. $this->id .'/lihat-pekerjaan" class="btn btn-xs"><i class="fa fa-check"></i></a>'; 

                      
                
            });
        });
    }

    protected function gridPekerjaan($id)
    {
        return Admin::grid(DJadwalComplain::class, function (Grid $grid) use ($id) {
            $grid->model()->where('m_jadwalcomplain_id' , $id);

            // $grid->id('ID')->sortable();
            $grid->tanggal('Tanggal Pelaksanaan');
            $grid->uraian('Uraian Pelaksanaan');
            $grid->uraian('Uraian Pelaksanaan');
            $grid->foto_sebelum('Foto Sebelum')->image();
            $grid->foto_sesudah('Foto Sesudah')->image();

            $grid->disableCreation();
            $grid->disableExport();
            $grid->disableRowSelector();

            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
                $actions->disableView();

                $actions->append("<a href='/admin/djadwalcomplains/". $actions->getKey() . "/edit' class='btn btn-xs'><i class='fa fa-check'></i></a>");
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
        return Admin::form(DJadwalComplain::class, function (Form $form) {

            // $form->setAction('/admin/djadwalcomplains');

            // $form->display('id', 'ID');
            $form->display('tanggal');
            $form->textarea('uraian');
            $form->text('keterangan');
            $form->image('foto_sebelum');
            $form->image('foto_sesudah');
            $form->hidden('status')->default('Selesai');
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

    protected function form_edit($mjadwalcomplain , $djadwalcomplain)
    {

        return Admin::form(DJadwalComplain::class, function (Form $form) use ($mjadwalcomplain , $djadwalcomplain) {

            $form->setAction('admin/mjadwalcomplains/'.$djadwalcomplain.'/djadwalcomplains');

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

    public function simpan(Request $request , $mjadwalcomplain , $djadwalcomplain)
    {
        return $request;
    }

    public function lihat_pekerjaan($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('Master');
            $content->description('Jadwal');

            $content->body($this->gridPekerjaan($id));
        });
    }
}
