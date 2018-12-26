<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->resource('asets', AsetController::class);
    $router->resource('provinsis', ProvinsiController::class);
    $router->resource('kotas', KotaController::class);
    $router->resource('slas', SlaController::class);
    $router->resource('detailslas', DetailSlaController::class);
    $router->resource('slaasets', SlaAsetController::class);
    $router->resource('waktus', WaktuController::class);
    $router->resource('frekuensis', FrekuensiController::class);
    $router->resource('settingjadwals', SettingJadwalController::class);
    $router->resource('jadwals', JadwalController::class);
    $router->resource('complaints', ComplaintController::class);
    $router->resource('requests', RequestController::class);
    $router->resource('akuns', AkunController::class);
    $router->resource('subakuns', SubakunController::class);
    $router->resource('anggarans', AnggaranController::class);
    $router->resource('realisasis', RealisasiController::class);
    $router->resource('tindaklanjuts', TindaklanjutController::class);
    $router->resource('penilaians' , PenilaianController::class);
    $router->resource('wilayahs' , WilayahController::class);
    $router->resource('areas' , WilayahAreaController::class);
    $router->resource('listriks' , ListrikController::class);
    $router->resource('airs' , AirController::class);
    $router->resource('solars' , SolarController::class);
    $router->resource('limbahs' , LimbahController::class);
    $router->resource('mpenilaians' , MPenilaianSlaController::class);

    
    $router->resource('JadwalAsets' , JadwalAsetController::class);
    
    $router->resource('transaksi_pengeluarans', TransaksiPengeluaranController::class); 
    $router->resource('Utility', UtilityController::class);
    $router->resource('complaints.tindaklanjuts' , TindaklanjutController::class);

    $router->resource('mjadwalcomplains' , MJadwalComplainController::class);
    $router->resource('detailtindaklanjuts.mjadwalcomplains' , MJadwalComplainController::class);
    $router->resource('djadwalcomplains' , DJadwalComplainController::class);
    $router->resource('mjadwalcomplains.djadwalcomplains' , DJadwalComplainController::class);

    $router->resource('tindaklanjutrequests' , TindaklanjutRequestController::class);
    $router->resource('mjadwalrequests' , MJadwalRequestController::class);
    $router->resource('djadwalrequests' , DJadwalRequestController::class);
    $router->resource('mjadwalrequests.djadwalrequests' , DJadwalRequestController::class);

    $router->resource('jadwalslas' , JadwalSlaController::class);
    $router->resource('JadwalTindakLanjuts' , JadwalTindakLanjutController::class);
    $router->resource('JadwalAsets.JadwalTindakLanjuts' , JadwalTindakLanjutController::class);

    // $router->resource('permintaans', RequestController::class);    
    
    $router->get('/update/slaasets/{id}' , 'SlaAsetController@update_slaaset');
    $router->get('/update/ketersediaan/{id}' , 'PenilaianController@update_ketersediaan');
    $router->get('/update/ketersediaan/all/{asetid}' , 'PenilaianController@update_ketersediaan_all');
    $router->get('/update/sesuai/{id}/{nilai}' , 'PenilaianController@update_sesuai');
    $router->get('/settingjadwals/api/load_alamat/{id}' , 'AsetController@load_alamat');
    $router->get('/api/kota', 'ProvinsiController@kota');
    $router->get('/api/sla', 'SlaAsetController@sla');
    $router->get('/api/detailsla', 'DetailSlaController@detailsla');
    $router->get('/api/subakun', 'SubakunController@subakun');
    $router->post('/simpan/settingjadwal' , 'SettingJadwalController@simpan');
    $router->post('/complain/tambah' , 'ComplaintController@tambah');
    $router->get("/download-complain/{id}","ComplaintController@print");
    $router->get('/penilaian/{ids}' , 'PenilaianController@index2');
    $router->post('/request/tambah' , 'RequestController@tambah');
    $router->get("/download-pdf/{id}","RequestController@print");
    $router->get('/get/jadwal/aset/{idaset}' , 'JadwalController@generateJadwalAset');
    $router->get('/get/jadwal/aset/{id}/{nilai}' , 'JadwalController@editfrekuensi');
    $router->get('/tindaklanjut/complain/{id}' , 'TindaklanjutController@create');
    $router->get('/pemakaian_listrik' , 'ListrikController@generate_wilayah');
    $router->get('/pemakaian_listrik2' , 'ListrikController@generate_pemakaian');
    $router->get('/pemakaian_air' , 'AirController@generate_pemakaian');
    $router->get('/chart' , 'ChartController@index');
    $router->get('/getfrekuensi' , 'FrekuensiController@getfrekuensi');
    $router->get('/getfrekuensi2' , 'JadwalController@getfrekuensi2');
    
    $router->get('/generate_tanggal_frekuensi' , 'FrekuensiController@generate_tanggal');
    $router->get('/JadwalTindakLanjuts/{id}/edit/{idsla}/edit' , 'JadwalTindakLanjutController@edit_tindaklanjut');
    
    $router->get('/update/nilai/{id}/dilaksanakan/{nilai}' , 'PenilaianController@update_laksanakan');
    $router->get('/print/penilaian/list' , 'PenilaianController@print_list');
    $router->get('/print/penilaian/{id}' , 'PenilaianController@print');
    $router->get('/print/penilaian_hasil/{id}' , 'PenilaianController@print_hasil');
    $router->get('/penilaian/generate_nilai/{ids}' , 'PenilaianController@generate_nilai');
    $router->post('/utility/print' , 'UtilityController@print');
    $router->get('/rekap/penilaian' , 'MPenilaianSlaController@getprint');
    $router->post('/rekapnilai/print' , 'MPenilaianSlaController@print');
    $router->get('/getanggaran/{ids}/{thn}' , 'AnggaranController@getanggaran');
    $router->get('/mjadwalcomplains/{mjadwalcomplain}/djadwalcomplains' , 'DJadwalComplainController@index2');
    $router->post('/mjadwalcomplains/{mjadwalcomplain}/djadwalcomplains/{djadwalcomplain}' , 'DJadwalComplainController@simpan');
    $router->get('/anggaran/laporan' , 'AnggaranController@index_laporan');
    $router->get('/anggaran/laporan/print' , 'AnggaranController@laporan');
    $router->get('/generate_rekap_anggaran' , 'AnggaranController@generate_rekap_anggaran');
    $router->get('/pilih/jadwalaset/{id}' , 'JadwalTindakLanjutController@index2');
    $router->get('/generate_jadwal/{id}' , 'JadwalTindakLanjutController@generate_tanggal');
    $router->get('/get/JadwalTindakLanjuts/{id}' , 'JadwalTindakLanjutController@index3');
    $router->get('/utility/air/generate_wilayah' , 'AirController@generate_wilayah');
    $router->get('/mjadwalrequests/{mjadwalrequest}/djadwalrequests' , 'DJadwalRequestController@index2');
    $router->get('/setting/ketersediaan' , 'SlaAsetController@index2');
    $router->get('/setting/ketersediaan/{id}/edit' , 'SlaAsetController@edit2');
    $router->get('/update/ketersediaan_sla/{id}' , 'SlaAsetController@update_ketersediaan');
    $router->get('/get/jadwalaset/pilihaset/{id}' , 'JadwalSlaController@list_pekerjaan');
    $router->get('/get/aset/rutin' , 'JadwalSlaController@list_aset_jadwal');
    $router->get('/generate_tanggal/tindaklanjut/{id}' , 'JadwalSlaController@generate_jadwal');
    $router->get('/list/tanggal/tindaklanjut/{id}' , 'JadwalSlaController@list_tanggal');
    $router->get('/list/tanggal/tindaklanjut/{id}/{ida}/edit' , 'JadwalSlaController@edit_tindaklanjut');
    $router->post('/admin/{id}' , 'JadwalSlaController@simpan');
    $router->get('/admin/JadwalTindakLanjuts/{idJadwal}/edit/{JadwalTindakLanjut}/edit' , 'JadwalTindakLanjutController@edit_tindaklanjut');
    $router->post('/admin/JadwalTindakLanjuts/{idJadwal}/edit/{JadwalTindakLanjut}' , 'JadwalTindakLanjutController@update_tindaklanjut');
    $router->post('/simpan/tindaklanjut/rutin/{id}' , 'JadwalTindakLanjutController@simpan2');
    $router->get('/complain/{id}/lihat-pekerjaan', 'DJadwalComplainController@lihat_pekerjaan');
    $router->get('/complain/report2' , 'ComplaintController@index_report');
    $router->get('/complain/tindaklanjut/{id}/print' , 'ComplaintController@cetak');

    // Utility Repor


});
