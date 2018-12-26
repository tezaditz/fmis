<?php

namespace App\Admin\Controllers;

use App\Models\Realisasi;
use App\Models\Anggaran;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

use Illuminate\Http\Request;
use App\Models\Akun;
use App\Models\Aset;
use App\Models\Subakun;

use Carbon\Carbon;

class RealisasiController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */

    public function index(Request $id)
    {   
        
        $DataSisa = Realisasi::where('id' ,'>' ,'0')->get();
      
        if(count($DataSisa) != 0)
        {
            foreach ($DataSisa as $key => $value) {
            $nilai_sisa = $value->anggaran - $value->realisasi;

            // return $nilai_sisa;
            $update= Realisasi::where('id' , $value->id)
            ->update(['sisa_anggaran'=>$nilai_sisa]);
            }
            

        }

        return Admin::content(function (Content $content) use ($id) {
        
            $content->header('header');
            $content->description('description');

            $content->body($this->grid($id));
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
            return Admin::grid(Realisasi::class, function (Grid $grid) {

            $grid->akun()->uraian('Akun');
            $grid->subakun()->uraian('Subakun');
            $grid->uraian();
            $grid->column('Anggaran')->display(function(){
                return number_format($this->anggaran , 2 , ',' , '.');
            });
            $grid->column('Realisasi')->display(function(){
                return number_format($this->realisasi , 2 , ',' , '.');
            });
            $grid->column('Sisa Anggaran')->display(function(){
                return number_format($this->sisa_anggaran , 2 , ',' , '.');
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
        
        return Admin::form(Realisasi::class, function (Form $form) {


            $form->display('id', 'ID');
            $form->text('uraian', 'Uraian');
            $form->text('anggaran', 'Anggaran');
            $form->text('realisasi', 'Realisasi');
            $form->text('sisa_anggaran', 'Sisa Anggaran');
        });
    }

    
}
