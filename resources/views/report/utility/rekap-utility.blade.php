<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
	<title>Rekap Utility</title>
</head>
<body>
	<div class="w3-container">
		<div class="w3-center">

			DATA PEMAKAIAN {{ strtoupper($Utility) }}<br />
			TAHUN {{ $tahun }}
		</div>
		<br />
		<br />
<table class="w3-table w3-border w3-tiny" border="1">
	<tr>
		<th>NO</th>
		<th>NAMA BANGUNAN/TANAH</th>
		<th>JAN</th>
		<th>FEB</th>
		<th>MAR</th>
		<th>APR</th>
		<th>MEI</th>
		<th>JUN</th>
		<th>JUL</th>
		<th>AUG</th>
		<th>SEP</th>
		<th>OKT</th>
		<th>NOV</th>
		<th>DES</th>
	</tr>
		<?php $no = 0;?>
		@forelse($RekapPemakaian as $data)
		<?php $no++ ;?>
		<tr>
			<td class="w3-center" >{{ $no }}</td>
			<td>{{ $data->nama_aset }}</td>
			<td>{{ number_format($data->jan,2) }}</td>
			<td>{{ number_format($data->feb ,2) }}</td>
			<td>{{ number_format($data->mar ,2) }}</td>
			<td>{{ number_format($data->apr ,2) }}</td>
			<td>{{ number_format($data->may ,2) }}</td>
			<td>{{ number_format($data->jun ,2) }}</td>
			<td>{{ number_format($data->jul ,2) }}</td>
			<td>{{ number_format($data->aug ,2) }}</td>
			<td>{{ number_format($data->sep ,2) }}</td>
			<td>{{ number_format($data->oct ,2) }}</td>
			<td>{{ number_format($data->nov ,2) }}</td>
			<td>{{ number_format($data->dec ,2) }}</td>
		</tr>
		
		@empty
		@endforelse
	
</table>		
	</div>

</body>
</html>