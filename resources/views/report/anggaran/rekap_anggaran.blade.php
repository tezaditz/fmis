
<html>
<head>
	<title>REKAP ANGGARAN</title>
</head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<body>
<div class="container w3-tiny">
  <table class="w3-table " border="1">
    <thead>
        <tr bgcolor="grey" class="w3-center">
          <td>Lingkup Pekerjaan</td>
          <td>Rincian Lingkup Pekerjaan</td>
          <td>RKAP (Bangunan)</td>
          <td>RKAP (Tanah)</td>
          <td>Biaya Total</td>
          <td>Januari</td>
          <td>Februari</td>
          <td>Maret</td>
          <td>April</td>
          <td>Mei</td>
          <td>Juni</td>
          <td>Juli</td>
          <td>Agustus</td>
          <td>September</td>
          <td>Oktober</td>
          <td>November</td>
          <td>Desember</td>
          <td>Sisa Anggaran</td>
        </tr>
    </thead>
    <tbody>



      @foreach($Data as $datas)





      <tr>
      
        <td>{{ $datas->akun_desc }}</td>
        
        <td>{{ $datas->subakun_desc }}</td>
        <td class="w3-right-align">{{ number_format($datas->rkap_bangunan , 0) }}</td>
        <td class="w3-right-align">{{ number_format($datas->rkap_tanah , 0) }}</td>
        
        <td class="w3-right-align" rowspan="0">
          {{ number_format($datas->biaya_total , 0) }}
        </td>
        
        <td class="w3-right-align">{{ number_format($datas->jan , 0) }}</td>
        <td class="w3-right-align">{{ number_format($datas->feb , 0) }}</td>
        <td class="w3-right-align">{{ number_format($datas->mar , 0) }}</td>
        <td class="w3-right-align">{{ number_format($datas->apr , 0) }}</td>
        <td class="w3-right-align">{{ number_format($datas->mei , 0) }}</td>
        <td class="w3-right-align">{{ number_format($datas->jun , 0) }}</td>
        <td class="w3-right-align">{{ number_format($datas->jul , 0) }}</td>
        <td class="w3-right-align">{{ number_format($datas->aug , 0) }}</td>
        <td class="w3-right-align">{{ number_format($datas->sep , 0) }}</td>
        <td class="w3-right-align">{{ number_format($datas->oct , 0) }}</td>
        <td class="w3-right-align">{{ number_format($datas->nov , 0) }}</td>
        <td class="w3-right-align">{{ number_format($datas->dec , 0) }}</td>
        <td class="w3-right-align">{{ number_format($datas->sisa_anggaran , 0) }}</td>
      </tr>

      @endforeach
    </tbody>
  </table>
</div>
</body>
</html>
