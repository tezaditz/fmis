<?php

namespace App\Admin\Controllers;

use App\Models\Penilaian;
use App\Models\Aset;
use App\Models\Sla;
use App\Models\DetailSla;
use App\Models\SlaAset;
use App\Models\AdminRoles;
use App\Models\AdminRoleUsers;
use App\Models\MPenilaianSla;
use App\Models\Parameter;
use App\Models\bulan;
use App\Models\Wilayah;
use App\Models\WilayahArea;
use App\Models\KetersediaanSla;
use App\Models\HasilPenilaianSla;


use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\Admin\Extensions\CheckKetersediaan;

use Illuminate\Support\MessageBag;
use PDF;

class PenilaianController extends Controller
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

            $content->header('Penilaian SLA');
            $content->description('Pilih Aset');

            $content->body($this->grid_master());
        });
    }

    public function index2($ids)
    {
        $this->generate_list_nilai($ids);
        $this->update_ketersediaan2($ids);
        return Admin::content(function (Content $content) use ($ids) {

            $content->header('Penilaian SLA');
            $content->description('Pilih Aset');

            $content->body($this->grid($ids));
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

            $content->header('Edit Master');
            $content->description('Penilaian');

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

            $content->header('Membuat Baru');
            $content->description('Penilaian SLA');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid($ids)
    {
        Admin::Script($this->script());
        return Admin::grid(Penilaian::class, function (Grid $grid) use ($ids) {
            $grid->model()->where('m_penilaian_id' , $ids);

            $grid->disableRowSelector();
            $grid->disableCreation();
            $grid->disableExport();
            $grid->disableFilter();
            $grid->disablePagination();
            $grid->disableActions();


            $grid->tools(function($tools) use ($ids) {
                $tools->append('<input type="checkbox" id="checkAll" data-id="'. $ids .'" >Pilih Semua</input>');
                $tools->append('<a href ="/admin/penilaian/generate_nilai/'. $ids .'" class="btn btn-sm btn-success pull-right"><i class="fa fa-floppy-o"></i> Save</a>');
            });


            // $grid->id('ID')->sortable();
            $grid->column('Uraian Pekerjaan')->display(function(){
                if($this->detail_sla_id == 0)
                {
                    return '<strong>' . $this->uraian . '</strong>';    
                }
                else
                {
                    return $this->uraian;
                }
            })->setAttributes(['width' => '25%']);
            $grid->column('Ketersediaan Fasilitas')->display(function(){
                if($this->detail_sla_id != 0)
                {
                    if($this->ketersediaan_fasilitas == 1)
                    {
                        return "<input type='checkbox' class='grid-check-row'  data-id='{$this->id}' checked disabled/>"; 
                    }
                    else
                    {
                        return "<input type='checkbox' class='grid-check-row'  data-id='{$this->id}' disabled/>";     
                    }  
                }
                else
                {
                    return '';
                }
            })->setAttributes(['width' => '5%']);
            $grid->column('Dilaksanakan')->display(function(){
                if($this->detail_sla_id != 0)
                {
                    if($this->ketersediaan_fasilitas == 1)
                    {
                        switch ($this->dilaksanakan) {
                            case 1:
                                return '<input type="radio" class="form-input rd-laksana rd-laksana_'.$this->id.'" name="laksanakan_'.$this->id.'" id="laksanakan_'.$this->id.'" data-value=0 data-id="'. $this->id .'"/>Tidak<br />
                                    <input type="radio" class="form-input rd-laksana rd-laksana_'.$this->id.'" name="laksanakan_'.$this->id.'" id="laksanakan_'.$this->id.'" data-value=1 data-id="'. $this->id .'" checked/>Ya<br />';
                                break;
                            default:
                                return '<input type="radio" class="form-input rd-laksana rd-laksana_'.$this->id.'" name="laksanakan_'.$this->id.'" id="laksanakan_'.$this->id.'" data-value=0 data-id="'. $this->id .'" />Tidak<br />
                                <input type="radio" class="form-input rd-laksana rd-laksana_'.$this->id.'" name="laksanakan_'.$this->id.'" id="laksanakan_'.$this->id.'" data-value=1 data-id="'. $this->id .'"/>Ya<br />';
                                break;
                        }
                    }
                    else
                    {
                                return '<input type="radio" class="form-input rd-laksana rd-laksana_'.$this->id.'" name="laksanakan_'.$this->id.'" id="laksanakan_'.$this->id.'" data-value=0 data-id="'. $this->id .'" disabled/>Tidak<br />
                                <input type="radio" class="form-input rd-laksana rd-laksana_'.$this->id.'" name="laksanakan_'.$this->id.'" id="laksanakan_'.$this->id.'" data-value=1 data-id="'. $this->id .'" disabled/>Ya<br />';
                    }

                    
                }
                else
                {
                    return '';
                }
            })->setAttributes(['width' => '5%']);
            $grid->column('Pencapaian SLA')->display(function(){
                if($this->detail_sla_id != 0)
                {
                    if($this->ketersediaan_fasilitas == 1)
                    {
                        switch ($this->sesuai) 
                        {
                            case 3:
                                return 
                                '<input type="radio" class="form-input rd-sesuai rd-sesuai_'. $this->id .'" name="sesuai'.$this->id.'" id="sesuai'.$this->id.'" data-value=3 data-id="'. $this->id .'" checked />Baik<br />
                                <input type="radio" class="form-input rd-sesuai rd-sesuai_'. $this->id .'" name="sesuai'.$this->id.'" id="sesuai'.$this->id.'" data-value=2 data-id="'. $this->id .'" />Cukup<br />
                                <input type="radio" class="form-input rd-sesuai rd-sesuai_'. $this->id .'" name="sesuai'.$this->id.'" id="sesuai'.$this->id.'" data-value=3 data-id="'. $this->id .'" />Kurang';
                                break;
                            case 2:
                                return 
                                '<input type="radio" class="form-input rd-sesuai rd-sesuai_'. $this->id .'" name="sesuai'.$this->id.'" id="sesuai'.$this->id.'" data-value=3 data-id="'. $this->id .'"  />Baik<br />
                                <input type="radio" class="form-input rd-sesuai rd-sesuai_'. $this->id .'" name="sesuai'.$this->id.'" id="sesuai'.$this->id.'" data-value=2 data-id="'. $this->id .'" checked/>Cukup<br />
                                <input type="radio" class="form-input rd-sesuai rd-sesuai_'. $this->id .'" name="sesuai'.$this->id.'" id="sesuai'.$this->id.'" data-value=1 data-id="'. $this->id .'" />Kurang';
                                break;
                            case 1:
                                return 
                                '<input type="radio" class="form-input rd-sesuai rd-sesuai_'. $this->id .'" name="sesuai'.$this->id.'" id="sesuai'.$this->id.'" data-value=3 data-id="'. $this->id .'"  />Baik<br />
                                <input type="radio" class="form-input rd-sesuai rd-sesuai_'. $this->id .'" name="sesuai'.$this->id.'" id="sesuai'.$this->id.'" data-value=2 data-id="'. $this->id .'" />Cukup<br />
                                <input type="radio" class="form-input rd-sesuai rd-sesuai_'. $this->id .'" name="sesuai'.$this->id.'" id="sesuai'.$this->id.'" data-value=1 data-id="'. $this->id .'" checked/>Kurang';
                                break;
                            default:
                             return 
                                '<input type="radio" class="form-input rd-sesuai rd-sesuai_'. $this->id .'" name="sesuai'.$this->id.'" id="sesuai'.$this->id.'" data-value=3 data-id="'. $this->id .'" />Baik<br />
                                <input type="radio" class="form-input rd-sesuai rd-sesuai_'. $this->id .'" name="sesuai'.$this->id.'" id="sesuai'.$this->id.'" data-value=2 data-id="'. $this->id .'" />Cukup<br />
                                <input type="radio" class="form-input rd-sesuai rd-sesuai_'. $this->id .'" name="sesuai'.$this->id.'" id="sesuai'.$this->id.'" data-value=1 data-id="'. $this->id .'" />Kurang';
                                break;
                        }
                    }
                    else
                    {
                        return 
                            '<input type="radio" class="form-input rd-sesuai rd-sesuai_'. $this->id .'" name="sesuai'.$this->id.'" id="sesuai'.$this->id.'" data-value=3 data-id="'. $this->id .'" disabled />Baik<br />
                            <input type="radio" class="form-input rd-sesuai rd-sesuai_'. $this->id .'" name="sesuai'.$this->id.'" id="sesuai'.$this->id.'" data-value=2 data-id="'. $this->id .'" disabled />Cukup<br />
                            <input type="radio" class="form-input rd-sesuai rd-sesuai_'. $this->id .'" name="sesuai'.$this->id.'" id="sesuai'.$this->id.'" data-value=1 data-id="'. $this->id .'" disabled />Kurang';
                    }
                   
                }
                else
                {
                    return '';
                }  
            })->setAttributes(['width' => '20%']);
            $grid->column('Keterangan')->display(function(){
                if($this->detail_sla_id != 0)
                {
                    return '<textarea></textarea>';    
                }
                else
                {
                    return '';
                }              
            })->setAttributes(['width' => '20%']);


            

           

            
        });
    }

    protected function grid_aset()
    {
        $iduser     = Admin::user()->id;  
        $groupWil   = Admin::user()->groupWil;
        $groupArea  = Admin::user()->groupArea;
        $RolesUsers = AdminRoleUsers::where('user_id' , $iduser)->get();
        $Roles      = AdminRoles::where('id' , $RolesUsers[0]['role_id'] )->get();
        $RoleId   = $Roles[0]['id'];

        return Admin::grid(Aset::class, function (Grid $grid) use ($groupArea , $groupWil , $RoleId ) {
            
            switch ($RoleId) {
                case 5:
                    $grid->model()->where('wilayah_area_id' , $groupArea);
                    break;
                
                default:
                    # code...
                    break;
            }



            $grid->nama('Nama Aset');
            $grid->address('Alamat Aset');

            $grid->disableFilter();
            $grid->disableExport();
            $grid->disableCreation();
            $grid->disableRowSelector();
            $grid->actions(function ($actions){
                $actions->disableDelete();
                $actions->disableEdit();

                $actions->append("<a href='/admin/penilaian/". $actions->getKey() ."' class='btn btn-xs'><i class='fa fa-eye'></i></a>");
            });
        });
    }

    protected function grid_master()
    {
        $roles      = Admin::user()->roles;
        $idRoles    = $roles[0]['id'];
        $user           = Admin::user();
        $idGroupArea    = $user['groupArea'];
        $idGroupWill    = $user['groupWil'];
        

        switch ($idRoles) {
            case 4:
                $DataWill = WilayahArea::whereIn('wilayah_id' , $idGroupWill)->get(['id']);
                $DataAset = Aset::whereIn('wilayah_area_id' , $DataWill)->get(['id']);
                break;
            case 5:
                $DataWill = WilayahArea::where('id' , $idGroupArea)->get(['id']);
                $DataAset = Aset::whereIn('wilayah_area_id' , $DataWill)->get(['id']);
                break;
            
            default:
                $DataWill = WilayahArea::all(['id']);
                $DataAset = Aset::whereIn('wilayah_area_id' , $DataWill)->get(['id']);
                break;
        }



        return Admin::grid(MPenilaianSla::class, function (Grid $grid) use ($idRoles , $idGroupArea , $idGroupWill , $DataWill ,$DataAset ) {
            
            
                $grid->model()->whereIn('aset_id',  $DataAset);




            $grid->tahun('Tahun');
            $grid->bulan()->uraian('Bulan');
            $grid->aset()->nama('Aset');
            $grid->aset()->address('Alamat Aset');
            $grid->column('Persentase (%)')->display(function(){
                return number_format($this->persentase , 2);
            });
            $grid->column('Pencapaian SLA (%)')->display(function(){
                return number_format($this->pencapaian_sla , 2);
            });
            // $grid->persentase('Persentase (%)');
            // $grid->persentase('Pencapaian SLA (%)');
            $grid->actions(function ($actions){
                $actions->disableDelete();
                $actions->disableView();
                

                $getdata = MPenilaianSla::where('id' , $actions->getKey())->get();
                $asetid = $getdata[0]['aset_id'];



                $actions->append("<a href='/admin/penilaian/". $actions->getKey() ."' class='btn btn-xs'><i class='fa fa-eye'></i></a>");

                $actions->append("<a href='/admin/print/penilaian_hasil/". $actions->getKey() ."' target='_blank' class='btn btn-xs'><i class='fa fa-print'></i></a>");

            });


            $grid->disableFilter();
            $grid->disableExport();
            $grid->disableRowSelector();

            
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        
        return Admin::form(MPenilaianSla::class, function (Form $form) {
            $roles      = Admin::user()->roles;
            $idRoles    = $roles[0]['id'];
            $user           = Admin::user();
            $idGroupArea    = $user['groupArea'];
            $idGroupWill    = $user['groupWil'];
            $idAset         = $user['aset_id'];

            $param  = Parameter::where('id' , 1)->get();
            $Thn    = $param[0]['value'];



            switch ($idRoles) {
                case 4:

                $will = WilayahArea::whereIn('wilayah_id' , $idGroupWill)->get(['id']);

                   
                    break;

                case 5:

                $will = WilayahArea::where('id' , $idGroupArea)->get(['id']);
               
                    break;
                
                default:
                    $will = WilayahArea::all(['id']);
                    break;
            }
                $form->text('tahun' , 'Tahun')->Attribute(['value' => $Thn , 'readonly' => 'true']);
                $form->select('bulan_id' , 'Period')->options(
                    bulan::all()->pluck('uraian' , 'id')
                );
                $form->select('aset_id' , 'Aset')->options(
                        Aset::whereIn('wilayah_area_id' , $will)->pluck('nama' , 'id')
                    );
                $form->text('sales_area_name' , 'Sales Area');
 

                // $form->saving(function ($form) {
                //     $DataMPenilaian = MPenilaianSla::where('aset_id' , dump($form->aset_id))
                //                                 ->where('bulan_id' , dump($form->bulan_id))
                //                                 ->get();
                //     if(Count($DataMPenilaian) != 0)
                //     {
                //       $error = new MessageBag([
                //         'title'   => 'Kesalahan!',
                //         'message' => 'Penilaian Untuk Aset dan Period ini Sudah Tersedia!',
                //         ]);  
                //       return back()->with(compact('error'));
                //     }
                // });
        });
    }


    public function update_ketersediaan($id)
    {
        
        $Data = Penilaian::where('id' , '=', $id)->get();
        
        if(Count($Data) > 0)
        {
            if($Data[0]['ketersediaan_fasilitas'] == 0)
            {
                $Data = Penilaian::where('id' , '=', $id)
                    ->Update([
                        'ketersediaan_fasilitas' => 1
                    ]);
            }
            else
            {
                $Data = Penilaian::where('id' , '=', $id)
                    ->Update([
                        'ketersediaan_fasilitas' => 0,
                        'dilaksanakan'  => 0,
                        'sesuai'    => 0
                    ]);
            }      
        }
        return response()->json($id);
    }

    public function update_ketersediaan_all($asetid)
    {
        
        $Data = Penilaian::where('aset_id' , '=', $asetid)->get();
        
        if(Count($Data) > 0)
        {
            if($Data[0]['ketersediaan_fasilitas'] == 0)
            {
                $Data = Penilaian::where('aset_id' , '=', $asetid)
                    ->Update([
                        'ketersediaan_fasilitas' => 1
                    ]);
            }
            else
            {
                $Data = Penilaian::where('aset_id' , '=', $asetid)
                    ->Update([
                        'ketersediaan_fasilitas' => 0
                    ]);
            }      
        }
        return response()->json();
    }

    public function update_sesuai($id , $nilai)
    {
        
        $Data = Penilaian::where('id' , '=', $id)->get();
        
        if(Count($Data) > 0)
        {
                $Data = Penilaian::where('id' , '=', $id)
                    ->Update([
                        'sesuai' => $nilai
                    ]);
                
        }
        return response()->json();
    }

    public function update_laksanakan($id , $nilai)
    {
        $Data = Penilaian::where('id' , $id)->get();

        $Data = Penilaian::where('id' , '=', $id)
                    ->Update([
                        'dilaksanakan' => $nilai
                    ]);

        return response()->json();
    }

    public function print_list()
    {
        return Admin::content(function (Content $content) {

            $content->header('Penilaian SLA');
            $content->description('Pilih Aset');

            $content->body($this->grid_print());
        });
    }

    public function grid_print()
    {
        return Admin::grid(MPenilaianSla::class, function (Grid $grid)  {
            
            $grid->tahun('Tahun');
            $grid->bulan()->uraian('Bulan');
            $grid->aset()->nama('Aset');
            $grid->aset()->address('Alamat Aset');
            $grid->status('Status');
            $grid->actions(function ($actions){
                $actions->disableDelete();
                $actions->disableEdit();

                $getdata = MPenilaianSla::where('id' , $actions->getKey())->get();
                $asetid = $getdata[0]['aset_id'];

                $actions->append("<a href='/admin/print/penilaian/". $actions->getKey() ."' target='_blank' class='btn btn-xs'><i class='fa fa-print'></i></a>");
            });


            $grid->disableFilter();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableCreation();

            
        });
    }

    public function print_hasil($id)
    {
        
        $this->generate_nilai($id);
        $DataPenilaian      = MPenilaianSla::where('id' , $id)->get();
        $Penilaian          = Penilaian::where('m_penilaian_id' , $id)->get();
        $DataAset           = Aset::where('id', $DataPenilaian[0]['aset_id'])->get();
        $DataWilayah        = WilayahArea::where('id' , $DataAset[0]['wilayah_area_id'])->get();
        $DataBulan          = bulan::where('id' , $DataPenilaian[0]['bulan_id'])->get();
        $area               = $DataWilayah[0]['nama_area'];
        $period             = $DataBulan[0]['uraian'] . ' ' . $DataPenilaian[0]['tahun'];
        $nama_aset          = $DataAset[0]['nama'];
        $nama_sales         = $DataPenilaian[0]['sales_area_name'];
        $nama               = Admin::user()->name;

        

        // $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('report.penilaian_sla' , [
        //     'area'         => $area,
        //      'period'        => $period, 
        //     'nama_aset'     => $aset,
        //     'DataPenilaian' => $Penilaian
        // ])->setPaper('A4' , 'landscape');
        

        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('report.penilaian_sla' ,
         ['period' => $period , 'area' => $area , 'Penilaian' => $Penilaian, 'DataPenilaian' => $DataPenilaian , 'nama_aset' => $nama_aset , 'nama_sales' => $nama_sales , 'nama' => $nama ] )->setPaper('A4' , 'landscape');

        return view('report.penilaian_sla' , ['period' => $period , 'area' => $area , 'Penilaian' => $Penilaian, 'DataPenilaian' => $DataPenilaian , 'nama_aset' => $nama_aset , 'nama_sales' => $nama_sales , 'nama' => $nama]);
        // return $pdf->stream('Penilaian_SLA.pdf');
    }

    public function print($id)
    {
        
        $DataPenilaian   = MPenilaianSla::where('id' , $id)->get();
        $Penilaian        = Penilaian::where('aset_id' , $DataPenilaian[0]['aset_id'])->get();
        $DataAset       = Aset::where('id', $DataPenilaian[0]['aset_id'])->get();
        $DataWilayah    = WilayahArea::where('id' , $DataAset[0]['wilayah_area_id'])->get();
        $DataBulan      = bulan::where('id' , $DataPenilaian[0]['bulan_id'])->get();
        $area = $DataWilayah[0]['nama_area'];
        $period = $DataBulan[0]['uraian'] . ' ' . $DataPenilaian[0]['tahun'] ;
        $aset           = $DataAset[0]['nama'];


        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('report.penilaian_sla_kosong',
            ['area'         => $area,
            'period'        => $period, 
            'nama_aset'     => $aset,
            'DataPenilaian' => $Penilaian
            ])->setPaper('A4' , 'landscape');

        // return view('report.penilaian_sla');
        return $pdf->stream('WorkOrder.pdf');
    }

    public function generate_nilai($ids)
    {
        // ketersediaan fasilitas
        $JmlKetersediaan = 0;
        $DataPenilaian= Penilaian::where('m_penilaian_id' , $ids)
                                    ->where('ketersediaan_fasilitas' , 1)->get();
        $JmlKetersediaan = Count($DataPenilaian);
        if($JmlKetersediaan > 0)
        {
            //dilaksanakan 
        $Jmldilaksanakan = 0;
        $DataPenilaian= Penilaian::where('m_penilaian_id' , $ids)
                                    ->where('ketersediaan_fasilitas' , 1)
                                    ->where('dilaksanakan' , 1)
                                    ->get();
        $Jmldilaksanakan = Count($DataPenilaian);

        //pencapaianSLA

        $sesuai_1 = 0;
        $DataPenilaian= Penilaian::where('m_penilaian_id' , $ids)
                                    ->where('ketersediaan_fasilitas' , 1)
                                    ->where('dilaksanakan' , 1)
                                    ->where('sesuai' , 1)->get();
        $sesuai_1 = Count($DataPenilaian) * 1;
        $sesuai_2 = 0;
        $DataPenilaian= Penilaian::where('m_penilaian_id' , $ids)
                                    ->where('ketersediaan_fasilitas' , 1)
                                    ->where('dilaksanakan' , 1)
                                    ->where('sesuai' , 2)->get();
        $sesuai_2 = Count($DataPenilaian) * 2;
        $sesuai_3 = 0;
        $DataPenilaian= Penilaian::where('m_penilaian_id' , $ids)
                                    ->where('ketersediaan_fasilitas' , 1)
                                    ->where('dilaksanakan' , 1)
                                    ->where('sesuai' , 3)->get();
        $sesuai_3 = Count($DataPenilaian) * 3;




        $nilaiMax   =   $JmlKetersediaan * 3;
        $persentase =   ( $Jmldilaksanakan / $JmlKetersediaan ) * 100;
        $pencapaian = ((($sesuai_3) + ($sesuai_2) + ($sesuai_1 )) / $nilaiMax ) * 100;

        

        $update = MPenilaianSla::where('id' , $ids)                        
                                ->update([
                                    'ketersediaan_fasilitas'    => $JmlKetersediaan,
                                    'jmldilaksanakan'           => $Jmldilaksanakan,
                                    'nilai_maksimum'            => $nilaiMax,
                                    'persentase'                => $persentase,
                                    'pencapaian_sla'            => $pencapaian,
                                ]);

        foreach ($DataPenilaian as $key => $value) {
            $insert                 = new HasilPenilaianSla();
            $insert->m_penilaian_id = $value->m_penilaian_id;
            $insert->tahun          = $value->tahun;
            $insert->bulan_id       = $value->bulan_id;
            $insert->aset_id        = $value->aset_id;
            $insert->sla_id         = $value->sla_id;
            $insert->detail_sla_id  = $value->detail_sla_id;
            $insert->uraian         = $value->uraian;
            $insert->ketersediaan_fasilitas = $value->ketersediaan_fasilitas;
            $insert->dilaksanakan   = $value->dilaksanakan;
            $insert->sesuai         = $value->sesuai;
            $insert->keterangan     = $value->keterangan;
            $insert->save();             
        }

        }
        

        return redirect('/admin/penilaians');
        
    }

    public function update_ketersediaan2($id)
    {
        $ParamTahun     = Parameter::where('id' , 1)->get();
        $DataPenilaian =  Penilaian::where('tahun' , $ParamTahun[0]['value'])
                                        ->where('m_penilaian_id' , $id)
                                        ->get();
        
        foreach ($DataPenilaian as $key => $value) {
            $CheckKetersediaan = KetersediaanSla::where('tahun' , $value->tahun)
                                                ->where('aset_id' , $value->aset_id)
                                                ->where('sla_id' , $value->sla_id)
                                                ->where('detail_sla_id' , $value->detail_sla_id)
                                                ->get();
            if(Count($CheckKetersediaan) != 0)
            {
                $update = Penilaian::where('id' , $value->id)
                                    ->update(['ketersediaan_fasilitas' => $CheckKetersediaan[0]['ketersediaan']]);
            }
        }
    }


    public function generate_list_nilai($ids)
    {
        $Data = MPenilaianSla::where('id' , $ids)->get();
        $buln = $Data[0]['bulan_id'];
        $ParamTahun = Parameter::where('id' , 1)->get();

        $DataKetersediaan = KetersediaanSla::where('aset_id' , $Data[0]['aset_id'])->get();
        foreach ($DataKetersediaan as $key => $value) {
            

            $DataPenilaian = Penilaian::where('tahun' , $value->tahun)
                                        ->where('aset_id' , $value->aset_id)
                                        ->where('sla_id' , $value->sla_id)
                                        ->where('detail_sla_id' , $value->detail_sla_id)
                                        ->delete();


            $insert                 = new penilaian();
            $insert->m_penilaian_id = $ids;
            $insert->tahun          = $ParamTahun[0]['value'];
            $insert->aset_id        = $Data[0]['aset_id'];
            $insert->sla_id         = $value->sla_id;
            $insert->detail_sla_id  = $value->detail_sla_id;
            $insert->uraian         = $value->uraian;
            $insert->save();
        }
    }

    protected function script()
    {
        return <<<SCRIPT



$("#checkAll").click(function(){
    $('input:checkbox').not(this).prop('checked', this.checked);

    var Id = $(this).data('id');

    $.ajax({
                url: '/admin/update/ketersediaan/all/' + Id,
                type: 'GET',
                success: function(response)
                {
                    console.log('Sukses');
                }
            });
});

$('.grid-check-row').on('click', function () {

    // Your code.
    
    var Id = $(this).data('id');
    console.log(Id);
    if($(this).not(':checked').length)
    {
        $('.rd-laksana_' + Id + '').removeAttr("checked");
        $('.rd-sesuai_' + Id + '').removeAttr("checked");
        $('.rd-laksana_' + Id + '').attr('disabled', 'disabled');
        $('.rd-sesuai_' + Id + '').attr('disabled', 'disabled');
    }
    else
    {
        $('.rd-laksana_' + Id + '').removeAttr("disabled");

        $('.rd-sesuai_' + Id + '').removeAttr("disabled");
    };

    

    $.ajax({
                url: '/admin/update/ketersediaan/' + Id,
                type: 'GET',
                success: function(response)
                {
                    console.log('Sukses');
                }
            });
});



$('.rd-laksana').on('change' , function(){
    console.log($(this).data('value'));
    var id      = $(this).data('id');
    var nilai   = $(this).data('value');
    $.ajax({
                url: '/admin/update/nilai/' + id + '/dilaksanakan/' + nilai,
                type: 'GET',
                success: function(response)
                {
                    console.log('Sukses');
                }
            });


    });

$('.rd-sesuai').on('change' , function(){
    console.log($(this).data('value'));
    var id      = $(this).data('id');
    var nilai   = $(this).data('value');
    $.ajax({
                url: '/admin/update/sesuai/' + id + '/' + nilai,
                type: 'GET',
                success: function(response)
                {
                    console.log('Sukses');
                }
            });


    });



SCRIPT;
    }
}
