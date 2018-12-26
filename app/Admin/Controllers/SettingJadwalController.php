<?php

namespace App\Admin\Controllers;

use App\Models\SettingJadwal;
use App\Models\Aset;
use App\Models\Sla;
use App\Models\DetailSla;
use App\Models\SlaAset;
use App\Models\Frekuensi;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingJadwalController extends Controller
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
        return Admin::grid(SettingJadwal::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

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
        Admin::script($this->script());
        return Admin::form(SettingJadwal::class, function (Form $form) {

            // $form->disableReset();
            $form->display('id', 'ID');

            $form->select('aset_id' , 'Nama Aset')->options(
                    Aset::all()->pluck('nama', 'id')
                )->load('sla_id' , '/admin/api/sla')->rules('required');

            $form->text('address' , 'alamat')->attribute(['disabled' => 'false']);
            
            $form->select('sla_id' , 'SLA')->options(function ($id) {
                    return SlaAset::options($id);
                })->load('detail_sla_id' , '/admin/api/detailsla')->rules('required');

            $form->select('detail_sla_id' , 'Detail SLA')->options(function ($id) {
                    return DetailSla::options($id);
                })->rules('required');
            $form->select('frekuensi_id' , 'Frekuensi')->options(
                    Frekuensi::all()->pluck('uraian', 'id')
                );


        });
    }

    protected function script()
    {
        return <<<SCRIPT

$('.aset_id').on('change', function () {

    // Your code.
    // console.log($(this).val());

    var nilai = $(this).val();

    $.ajax({
                url: '/admin/settingjadwals/api/load_alamat/' + nilai,
                type: 'GET',
                dataType:'json',
                success: function(response)
                {
                    
                    $('.address').val(response[0]['address']);
                    // console.log(response[0]['address']);
                }
            });

});

SCRIPT;
    }
}
