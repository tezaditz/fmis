<?php

namespace App\Admin\Controllers;

use App\Models\Transaksi_Pengeluaran;
use App\Models\Akun;
// use App\Models\Aset;
use App\Models\Subakun;
use App\Models\Anggaran;
use App\Models\Realisasi;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

use Illuminate\Http\Request;
use Carbon\Carbon;


class TransaksiPengeluaranController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {   
        $this->update_sisa_anggaran();
        return Admin::content(function (Content $content) {

            $content->header('Transaksi');
            $content->description('Pengeluaran');

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

            $content->header('Ubah Transaksi');
            $content->description('Pengeluaran');

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

            $content->header('Membuat Transaksi');
            $content->description('Pengeluaran');

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
        return Admin::grid(Transaksi_Pengeluaran::class, function (Grid $grid) {

            // $grid->id('ID')->sortable();

            $grid->tanggal();
            $grid->akun()->uraian('Akun');
            $grid->subakun()->uraian('Subakun');
            $grid->column('Jumlah')->display(function(){
                return number_format($this->jumlah , 2 , ',' , '.');
            });
            $grid->keterangan();
            // $grid->created_at();
            // $grid->updated_at();
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
        return Admin::form(Transaksi_Pengeluaran::class, function (Form $form) {

            // $form->display('id', 'ID');



            $form->date('tanggal','Tanggal Transaksi')->default(Carbon::now());
            $form->select('akun_id' , 'Akun ID')->options(
                    Akun::all()->pluck('uraian', 'id')
                )->load('subakun_id' , '/admin/api/subakun');

            $form->select('subakun_id' , 'Subakun ID')->options(
                    Subakun::all()->pluck('uraian', 'id')
                );
            // $form->currency('sisa_anggaran' , 'Sisa Anggaran')->symbol('Rp.');
            $form->text('sisa_anggaran' , 'Sisa Anggaran')->attribute(['readonly' => true, 'style' => 'text-align:right']);
            $form->currency('jumlah','Jumlah Pengeluaran')->symbol('Rp.')->rules('required');
            $form->text('keterangan','Keterangan')->rules('required');
            $form->file('file_1')->options([
                    'previewFileType'=>'pdf',
                    'initialPreviewFileType'=>'pdf']);
            // $form->display('','');
            // $form->display('created_at', 'Created At');
            // $form->display('updated_at', 'Updated At');
        });
    }

    public function update_sisa_anggaran()
    {
        $Data = Transaksi_Pengeluaran::where('hitung' , 0)->get();
       
        foreach ($Data as $key => $value) {
            
            $thn = substr($value->tanggal, 0 , 4);
            
            $Anggaran = Anggaran::where('subakun_id' , $value->subakun_id)
                                ->where('tahun_anggaran' ,$thn)
                                ->get();                               

            $SisaAnggaran   = $Anggaran[0]['sisa_anggaran'];
            $Realisasi      = $Anggaran[0]['realisasi'];

            $sisa = $SisaAnggaran - $value->jumlah;
            $real = $Realisasi + $value->jumlah;
            $update = Anggaran::where('subakun_id' , $value->subakun_id)
                                ->where('tahun_anggaran' , $thn)
                                ->update([
                                    'realisasi' => $real,
                                    'sisa_anggaran' => $sisa
                                ]);
            $update_hitung = Transaksi_Pengeluaran::where('id' , $value->id)
                                                    ->update(['hitung' => 1]);

        }
    }

     protected function script()
    {
        return <<<SCRIPT

$('.subakun_id').on('change', function () {

    // Your code.
    console.log($(this).val());

    var nilai = $(this).val();
    var tgl = $('.tanggal').val();
    var thn = tgl.substr(0,4);

    $.ajax({
                url: '/admin/getanggaran/' + nilai + '/' + thn,
                type: 'GET',
                dataType:'json',
                success: function(response)
                {
                    // console.log(response);
                    if(response == 0)
                    {
                        alert('Belum Ada Anggaran Yang Tersedia!!');
                    };
                   $('.sisa_anggaran').val(response.toFixed(2).replace(/(\d)(?=(\d{3})+(?:\.\d+)?$)/g, "$1,"));
                }
            });

});

$('.jumlah').on('keyup' , function(){

        var sisa1 = $('.sisa_anggaran').val().replace(/,/g, '');
        var sisa = parseFloat(sisa1);

        var b = $(this).val().replace(/,/g, '');
        var a = parseFloat(b);

        if(a > sisa)
        {
            alert('Jumlah Pengeluaran Melebihi Sisa Anggaran !!');
            $(this).val(0);
        }

});

$('.jumlah').on('focus' , function(){
        var sisa1 = $('.sisa_anggaran').val().replace(/,/g, '');
        var sisa = parseFloat(sisa1);

        console.log(sisa);
    if(sisa == 0)
    {
        alert('Tidak ada Anggaran Yang Tersedia !');
        $('.sisa_anggaran').focus();
    }
});



SCRIPT;
    }
}
