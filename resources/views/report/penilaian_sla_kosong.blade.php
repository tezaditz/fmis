
<html>
<head>
	<title>PENILAIAN SERVICE LEVEL AGREEMENT (SLA)</title>
</head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>


<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">

<body>
<div class="w3-container w3-row">
	

<table id="header" class="w3-table ">
	<tr>
		<td>
			<p style="text-align: center;">PENILAIAN SERVICE LEVEL AGREEMENT (SLA) </p>
			<p style="text-align: center;">JASA MANAJEMEN GEDUNG, KAWASAN , DAN FASILITAS KANTOR</p>
		</td>
	</tr>
</table>

<table class="w3-table">
  <tr>
    <td width="20%">Periode </td>
    <td width="5%">:</td>
    <td width="75%">{{ $period }}</td>
  </tr>
  <tr>
    <td>Area</td>
    <td>:</td>
    <td>{{ $area }}</td>
  </tr>
  <tr>
    <td>Lokasi</td>
    <td>:</td>
    <td>{{ $nama_aset }}</td>
  </tr>
  <tr>
    <td>Tanggal Pemeriksaan</td>
    <td>:</td>
    <td>.......................</td>
  </tr>
</table>

<table border="1" class="w3-table w3-border">
 <tr>
    
    <td rowspan="2" colspan="2"><p style="text-align: center;">URAIAN PEKERJAAN DAN LINGKUP KEGIATAN / SLA</p></td>
    <td rowspan="2"><p style="text-align: center;">Ketersediaan fasilitas (Ada / Tidak Ada)</p></td>
  	<td colspan="2"><p style="text-align: center;">Pelaksanaan Pekerjaan</p></td>
    <td colspan="3"><p style="text-align: center;"> Pencapaian SLA*</p></td>
    <td rowspan="2"><p style="text-align: center;"> Keterangan</p></td>
  </tr>
  
  <tr>
    <td ><p style="text-align: center;">Dilaksanakan</p></td>
    <td ><p style="text-align: center;">Tidak Dilaksanakan</p></td>
    <td ><p style="text-align: center;">Baik<br />
      (nilai =3)</p></td>
    <td ><p style="text-align: center;">Cukup<br />
      (nilai =2)</p></td>
    <td ><p style="text-align: center;">Kurang <br />
      (nilai =1)</p></td>
      
  </tr>
  @foreach($DataPenilaian as $data)
    <tr>
      @if($data['sla_id'] != 0)
      <td colspan="9">
      <strong>{{ $data['uraian'] }}</strong>
      </td>
      @else
      <td colspan="2">
        {{ $data['uraian'] }}
      </td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      @endif
    </tr>
  @endforeach
  
</table>

<p style="text-align: center;"><i>*Jika tidak terdapat fasilitas dilokasi yang menjadi objek, cukup beri tanda "-" pada pencapaian SLA</i></p>

<table border="1" class="w3-table w3-border">
  <tr>
    <td colspan="9">Kriteria Penilaian</td>
    
  </tr>
  <tr>
    <td colspan="2" ></td>
    <td colspan="4" >PERSENTASE KEGIATAN</td>
    <td colspan="3" >.............</td>
    
  </tr>
  <tr>
    <td>Ketersediaan Fasilitas</td>
    <td> ...... </td>
    <td colspan="4" rowspan="2" >PENCAPAIAN SLA</td>
    <td colspan="3" rowspan="2" >......</td>
  </tr>
  <tr>
    <td>Nilai maksimum SLA</td>
    <td >....... </td>
  </tr>
</table>

<p>Keterangan :<br/>
*) isi dengan tanda "v"</p>


<table class="w3-table">
  <tr>
    <td>
    	<p style="text-align: center;">Dibuat Oleh <br/>
    	PT Permata Graha Nusantara <br/>
    	Koordinator Area {{ $area }}</p>
    	
    </td>

    <td>
    	<p style="text-align: center;">Menyetujui <br/>
    	.....................<br/>
    	.....................</p>
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr >
    <td><p style="text-align: center;">.............</p></td>

    <td><p style="text-align: center;">.............</p></td>
  </tr>
</table>

</div>

</body>
</html>