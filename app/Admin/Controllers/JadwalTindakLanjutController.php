<?php

namespace App\Admin\Controllers;

use App\Models\JadwalTindakLanjut;
use App\Models\JadwalAset;
use App\Models\DetailFrekuensi;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

use Illuminate\Http\Request;

class JadwalTindakLanjutController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index($JadwalAset)
    {
        
        $this->generate_tanggal($JadwalAset);
        return Admin::content(function (Content $content) use ($JadwalAset){

            $content->header('Tindaklanjut');
            $content->description('Pekerjaan Rutin');

            // $JadwalSlaAset = JadwalAset::where('')


            $content->body($this->form_tindaklanjut($JadwalAset)->edit($JadwalAset));
            $content->body($this->grid($JadwalAset));
        });
    }

    public function form_tindaklanjut($id)
    {
         return Admin::form(JadwalAset::class, function (Form $form) use ($id) {
            $form->display('uraian' , 'Uraian');

            $form->tools(function($tools){
                // Disable back btn.
                $tools->disableBackButton();

                // Disable list btn
                $tools->disableListButton();
            });

            $form->disableSubmit();
            $form->disableReset();

        });
    }

    public function index2()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->grid_sla());
        });
    }

    public function index3($id)
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
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
    public function edit($JadwalAset,$JadwalTindakLanjut)
    {
        return Admin::content(function (Content $content) use ($JadwalAset,$JadwalTindakLanjut) {

            $content->header('Tindaklanjut');
            $content->description('Pekerjaan Rutin');

            $content->body($this->form($JadwalAset,$JadwalTindakLanjut)->edit($JadwalTindakLanjut));
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
    protected function grid($idAset)
    {

        return Admin::grid(JadwalTindakLanjut::class, function (Grid $grid) use ($idAset) {
            $grid->model()->where('jadwal_sla_aset_id' , $idAset);
            
            $grid->tanggal('Tanggal');
            $grid->foto_before('Foto Sebelum')->image();
            $grid->foto_after('Foto Sesudah')->image();
            $grid->keterangan();

            $grid->actions(function ($actions){
               $actions->disableDelete();
               $actions->disableView();
               $actions->disableEdit();
               $Data = JadwalTindakLanjut::where('id' , $actions->getKey())->get();

               $actions->append("<a href='/admin/JadwalAsets/". $Data[0]['jadwal_sla_aset_id'] ."/JadwalTindakLanjuts/". $actions->getKey() ."/edit' class='btn btn-sm'><i class='fa fa-pencil'></i></a>");
            });

            $grid->filter(function($filter){
                $filter->disableIdFilter();
                $filter->date('tanggal' , 'Tanggal');
            });

            $grid->disableExport();
            $grid->disableCreation();
            $grid->disableRowSelector();

        });
    }

    protected function grid_sla()
    {
        return Admin::grid(JadwalAset::class, function (Grid $grid) {

            $grid->uraian('Uraian');
            $grid->uraian_frekuensi('uraian_frekuensi');

            $grid->actions(function ($actions){
                $actions->append('<a href = "/admin/get/JadwalTindakLanjuts/'. $actions->getKey() .'" class="btn btn-xs"><i class="fa fa-edit"></i></a>');
            });


            $grid->created_at();
            $grid->updated_at();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form($JadwalAset = null ,$JadwalTindakLanjut = null  )
    {
        
        return Admin::form(JadwalTindakLanjut::class, function (Form $form) use ($JadwalAset , $JadwalTindakLanjut) {

            $form->setAction( url('/') . '/admin/JadwalTindakLanjuts/'. $JadwalTindakLanjut .'');
            
            $form->date('tanggal');
            $form->image('foto_before');
            $form->image('foto_after');
            $form->text('keterangan');

            $form->saved(function (Form $form) use ($JadwalAset , $JadwalTindakLanjut) {
                //...
            });
        });
    }

    protected function edit_tindaklanjut($idJadwal , $JadwalTindakLanjut)
    {

        return Admin::content(function (Content $content) use ($JadwalTindakLanjut , $idJadwal) {

            $content->header('Tindaklanjut');
            $content->description('Pekerjaan Rutin');

            // $content->body($this->form($JadwalTindakLanjut)->edit($JadwalTindakLanjut));
            // $content->body($JadwalTindakLanjut);
            $content->body($this->form($idJadwal , $JadwalTindakLanjut)->edit($JadwalTindakLanjut));
        });
    }

    public function generate_tanggal($id)
    {
        $DataJadwalAset = JadwalAset::where('id' , $id)->get();
        $DataTanggal    = DetailFrekuensi::where('frekuensi_id' , $DataJadwalAset[0]['frekuensi_id'])->get();

        $CekJadwal      = JadwalTindakLanjut::where('jadwal_sla_aset_id' , $id)->get();
        if(count($CekJadwal) != 0)
        {
            JadwalTindakLanjut::where('jadwal_sla_aset_id' , $id)->delete();
        }

        foreach ($DataTanggal as $key => $value) {
            $insert = new JadwalTindakLanjut();
            $insert->jadwal_sla_aset_id = $id;
            $insert->tanggal = $value->tanggal;
            $insert->save();
        }
        
    }






}
