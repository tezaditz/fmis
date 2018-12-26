<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Rekap Penilaian</title>
</head>
<body>
<div class="w3-center">
	<strong>
	REKAP PENILAIAN SLA <br/>
	PERIODE JANUARI S.D JUNI <br/>
	TAHUN {{ $tahun }}
	</strong>
</div>
<br />
<br />
<div class="w3-tiny">
	<table class="w3-table w3-bordered" border="1">
		<thead class="w3-center">
			<tr>
				<th rowspan="2">No</th>
				<th rowspan="2">Area</th>
				<th rowspan="2">Indeks</th>
				<th rowspan="2">Lokasi Gedung/Bangunan</th>
				<th colspan="6">Pencapaian SLA</th>
			</tr>
			<tr>
				<th>Jan</th>
				<th>Feb</th>
				<th>Mar</th>
				<th>Apr</th>
				<th>Mei</th>
				<th>Jun</th>
			</tr>
		</thead>
		<tbody>
			<?php $no = 0;?>
			
			@forelse($regional as $dataRegional)
			<?php $indeks = 0;?>
			<tr>
				<td colspan="10">{{ $dataRegional->RegNama }}</td>
			</tr>
			<?php 
			$total_jan=0 ;
			$total_feb=0 ;
			$total_mar=0 ;
			$total_apr=0 ;
			$total_mei=0 ;
			$total_jun=0 ;
			?>
				@forelse($Rekap as $data)





					@if($dataRegional->RegId == $data->regid)
					<?php $no++ ;?>
					<?php $indeks++ ;?>
					<?php 
						$total_jan= $total_jan + $data->jan ;
						$total_feb= $total_feb + $data->feb ;
						$total_mar= $total_mar + $data->mar ;
						$total_apr= $total_apr + $data->apr ;
						$total_mei= $total_mei + $data->mei ;
						$total_jun= $total_jun + $data->jun ;
					?>
					<tr>
						<td>{{ $no }}</td>
						<td>{{ $data->area }}</td>
						<td>{{ number_format($indeks,2) }}</td>
						<td>{{ $data->aset }}</td>
						<td>
							{{ number_format($data->jan ,2 ) }} %
						</td>
						<td>
							{{ number_format($data->feb ,2 ) }} %
						</td>
						<td>
							{{ number_format($data->mar ,2 ) }} %
						</td>
						<td>
							{{ number_format($data->apr ,2 ) }} %
						</td>
						<td>
							{{ number_format($data->mei ,2 ) }} %
						</td>
						<td>
							{{ number_format($data->jun ,2 ) }} %
						</td>
					</tr>
					@endif
				@empty
				@endforelse
				<tr>
					<td colspan="4">
						<strong>Total Pencapaian Jasa Management Kawasan Gedung dan Fasilitas {{ $dataRegional->RegNama }}</strong>
					</td>
					<td>
						<strong>{{ number_format($total_jan / $indeks , 2) }} %</strong>
					</td>
					<td>
						<strong>{{ number_format($total_feb / $indeks , 2) }} %</strong>
					</td>
					<td>
						<strong>{{ number_format($total_mar / $indeks , 2) }} %</strong>
					</td>
					<td>
						<strong>{{ number_format($total_apr / $indeks , 2) }} %</strong>
					</td>
					<td>
						<strong>{{ number_format($total_mei / $indeks , 2) }} %</strong>
					</td>
					<td>
						<strong>{{ number_format($total_jun / $indeks , 2) }} %</strong>
					</td>
				</tr>
			@empty
			@endforelse



		</tbody>
	</table>
</div>

</body>
</html>