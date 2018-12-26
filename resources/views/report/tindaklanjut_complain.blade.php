<!DOCTYPE html>
<html>
<head>
	<title>Laporan</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>
	<div class="w3-container w3-row w3-tiny w3-cell-row">
		<h3>Laporan</h3>
		<table class="w3-table w3-border" border="1">
			<thead>
				<tr>
					<th class="w3-center">No.</th>
					<th class="w3-center">Jenis</th>
					<th class="w3-center">Lokasi	</th>
					<th class="w3-center">Uraian Kejadian</th>
					<th class="w3-center" colspan="2">Dokumentasi</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$x = 0;	
				 ?>
				@foreach($Data as $Datas)
				<?php
				$x = $x + 1;	
				 ?>
				<tr>
					<td class="w3-center">
				<?php
				echo $x;	
				 ?>
					</td>
					<td>{{ $jenis }}</td>
					<td>{{ $lokasi }}</td>
					<td>{{ $Datas->uraian }}</td>
					<td>
						<img src="{{ url('/uploads/') }}/{{ $Datas->foto_sebelum }}" width="200px" height="300px">
					</td>
					<td>
						
						<img src="{{ url('/uploads/') }}/{{ $Datas->foto_sesudah }}" width="200px" height="300px">
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</body>
</html>