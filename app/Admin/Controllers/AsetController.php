<?php

namespace App\Admin\Controllers;

use App\Models\Aset;
use App\Models\Provinsi;
use App\Models\kota;
use App\Models\WilayahArea;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AsetController extends Controller
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

            $content->header('ASET');
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

            $content->header('Ubah Aset');
            $content->description('');

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

            $content->header('Membuat Aset');
            $content->description('');

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
        $roles      = Admin::user()->roles;
        $idRoles    = $roles[0]['id'];
        $user           = Admin::user();
        $idGroupArea    = $user['groupArea'];
        $idGroupWill    = $user['groupWil'];
        $idAset         = $user['aset_id'];

        
        
        return Admin::grid(Aset::class, function (Grid $grid) use ($idRoles , $idGroupArea , $idGroupWill , $idAset ) {

            //filter
            if($idRoles == 4)
            {
                $DataWill = WilayahArea::where('wilayah_id' , $idGroupWill)->get(['id']);
            }
            elseif($idRoles == 5)
            {
                $DataWill = WilayahArea::where('id' , $idGroupArea)->get(['id']);
            }
            else
            {
                $DataWill = WilayahArea::get(['id']);
            }

            $grid->model()->whereIn('id' , $DataWill);

            $grid->filter(function($filter){
                $filter->disableIdFIlter();

                $filter->like('nama' , 'Nama Aset');
                $filter->like('address' , 'Alamat Aset');
            });


            $grid->nama('Nama Aset');
            $grid->provinsi()->name();
            $grid->kota()->name();
            $grid->address('Alamat');
            $grid->telp();
            // $grid->foto()->image('/admin/public/upload/');
            $grid->foto()->image();
            $grid->pln_id('ID PLN');
            $grid->pam_id('ID PAM');

           



        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Aset::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('nama' , 'Nama Aset');
            $form->select('wilayah_area_id' , 'Area')->options(
                WilayahArea::all()->pluck('nama_area' , 'id')
            );
            $form->text('address' , 'Alamat');
            $form->select('type' , 'Jenis Aset')->options(['Bangunan' => 'Bangunan', 'Tanah' => 'Tanah']);

            $form->number('volume', 'Luas Aset (M2)');
            $form->image('foto');
            $form->text('telp');
            $form->text('pln_id' , 'ID PLN');
            $form->text('pam_id' , 'ID PAM');


        });
    }

    public function load_alamat($id)
    {
        
        $Data =Aset::where('id', $id)->get(['address']); 
        return response()->json($Data);
    
    }
}
