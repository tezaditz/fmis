<?php

namespace App\Admin\Controllers;

use App\Models\Subakun;
use App\Models\Akun;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubakunController extends Controller
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

            $content->header('Anggaran');
            $content->description('Sub Akun');

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
        return Admin::grid(Subakun::class, function (Grid $grid) {

            
            $grid->akun()->uraian('Akun');
            $grid->uraian('Uraian');

            $grid->filter(function($filter){
                $filter->disableIdFilter();

                $filter->like('akun.uraian', 'Akun');
                $filter->like('uraian', 'Uraian');
            });

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
        return Admin::form(Subakun::class, function (Form $form) {

            

            // $form->text('akun_id', 'Akun Id');
            $form->select('akun_id' , 'Akun ID')->options(
                    Akun::all()->pluck('uraian', 'id')
                );
            $form->text('uraian', 'Uraian');
            
        });
    }

    public function subakun(Request $request)
    {
        $id = $request->get('q');
        $CekData = Subakun::where('akun_id' , $id)->get();
        
        if(Count($CekData) > 0)
        {
            foreach ($CekData as $key => $value) {
                $Data[] = [
                    'id'        => $value->id,
                    'text'      => $value->uraian
                ];
            }    
        }
        else
        {
            $Data[] = [];
        }  

        return $Data;
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
