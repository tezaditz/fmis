<?php

namespace App\Admin\Controllers;

use App\Models\TindaklanjutRequest;
use App\Models\Permintaan;
use App\Models\Status;
use App\Models\MJadwalRequest;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class TindaklanjutRequestController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        $this->update_status();
        return Admin::content(function (Content $content) {

            $content->header('Tindaklanjut');
            $content->description('Request');

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
        return Admin::grid(TindaklanjutRequest::class, function (Grid $grid) {

            $grid->DisableRowSelector();
            $grid->DisableExport();
            $grid->DisableActions();



            $grid->Permintaan()->nomor();
            $grid->Permintaan()->nama_pemohon();
            $grid->Permintaan()->unit_pelapor();
            $grid->column('Status')->display(function () {            
                $DataRequest    = Permintaan::where('id' , $this->request_id)->get();
                $DataStatus     = Status::where('id' , $DataRequest[0]['status_id'])->get();
                if($DataRequest[0]['status_id'] == '4')
                {
                    return '<a href="/admin/tindaklanjutrequests/'. $this->id .'/edit" class="btn btn-xs btn-danger">' . $DataStatus[0]['keterangan'] . '</a>';
                }
                elseif($DataRequest[0]['status_id'] == '2')
                {
                    return '<a href="javascript:void(0)" class="btn btn-xs btn-success">' . $DataStatus[0]['keterangan'] . '</a>';
                }
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form($mode = 'create')
    {

        Admin::script($this->script());

        if($mode == 'edit')
        {
            Admin::script($this->script_edit());            
        }

        return Admin::form(TindaklanjutRequest::class, function (Form $form) {

            


            $form->hidden('request_id' , 'Request ID');
            $form->select('pusat_biaya' , 'Pusat Biaya')->options([ '0' => ' ' , 'PGNMAS' => 'PGNMAS' , 'PGN' => 'PGN' , 'Lainnya' => 'Lainnya']);
            $form->text('keterangan_biaya' , 'Keterangan Biaya')->attribute('readonly');
            $form->currency('biaya' , 'Biaya')->symbol('Rp.');
            $form->number('waktu_pekerjaan' , 'Waktu Pekerjaan');
            $form->select('satuan_waktu' , 'Satuan Waktu')->options(['Menit' => 'Menit' , 'Jam' => 'Jam' , 'Hari' => 'Hari']);
            $form->text('keterangan' , 'Keterangan');
            $form->hidden('flag' , 'Flag');
        });
    }

    public function update_status()
    {
        $DataTindaklanjut = TindaklanjutRequest::where('flag' , 1)->get();
        foreach ($DataTindaklanjut as $key => $value) {
            $Update_Complain = Permintaan::where('id' , $value->request_id)
                                ->update(['status_id' => 2]);

            $insert_jadwal = new MJadwalRequest();
            $insert_jadwal->request_id = $value->request_id;
            $insert_jadwal->save();

            $update_tindaklanjut = TindaklanjutRequest::where('id' , $value->id)
                                            ->update(['flag' => 2]);
        }
    }
protected function script()
    {
        return <<<SCRIPT

$('.pusat_biaya').on('change', function () {

    if($(this).val() == 'Lainnya')
    {
        // console.log($(this).val());
        $('.keterangan_biaya').val('');    
        $('.keterangan_biaya').removeAttr('readonly');
        $('.keterangan_biaya').focus();
    }
    else
    {
        $('.keterangan_biaya').val($(this).val());
        $('.keterangan_biaya').attr('readonly' , true);
    }
});

$('.flag').val(1);

SCRIPT;
    }
protected function script_edit()
    {
        return <<<SCRIPT
$('.flag').val(1);

SCRIPT;
    }
}


