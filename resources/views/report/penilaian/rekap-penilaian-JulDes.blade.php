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
	PERIODE JULI S.D DESEMBER <br/>
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
				<th>Jul</th>
				<th>Aug</th>
				<th>Sep</th>
				<th>Okt</th>
				<th>Nov</th>
				<th>Des</th>
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
			$total_jul=0 ;
			$total_aug=0 ;
			$total_sep=0 ;
			$total_oct=0 ;
			$total_nov=0 ;
			$total_des=0 ;
			?>
				@forelse($Rekap as $data)

					@if($dataRegional->RegId == $data->regid)
					<?php $no++ ;?>
					<?php $indeks++ ;?>
					<?php 
						$total_jul= $total_jul + $data->jul ;
						$total_aug= $total_aug + $data->aug ;
						$total_sep= $total_sep + $data->sep ;
						$total_oct= $total_oct + $data->oct ;
						$total_nov= $total_nov + $data->nov ;
						$total_des= $total_des + $data->des ;
					?>
					<tr>
						<td>{{ $no }}</td>
						<td>{{ $data->area }}</td>
						<td>{{ number_format($indeks,2) }}</td>
						<td>{{ $data->aset }}</td>
						<td>
							{{ number_format($data->jul ,2 ) }} %
						</td>
						<td>
							{{ number_format($data->aug ,2 ) }} %
						</td>
						<td>
							{{ number_format($data->sep ,2 ) }} %
						</td>
						<td>
							{{ number_format($data->oct ,2 ) }} %
						</td>
						<td>
							{{ number_format($data->nov ,2 ) }} %
						</td>
						<td>
							{{ number_format($data->des ,2 ) }} %
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
						<strong>{{ number_format($total_jul / $indeks , 2) }} %</strong>
					</td>
					<td>
						<strong>{{ number_format($total_aug / $indeks , 2) }} %</strong>
					</td>
					<td>
						<strong>{{ number_format($total_sep / $indeks , 2) }} %</strong>
					</td>
					<td>
						<strong>{{ number_format($total_oct / $indeks , 2) }} %</strong>
					</td>
					<td>
						<strong>{{ number_format($total_nov / $indeks , 2) }} %</strong>
					</td>
					<td>
						<strong>{{ number_format($total_des / $indeks , 2) }} %</strong>
					</td>
				</tr>
			@empty
			@endforelse



		</tbody>
	</table>
</div>

</body>
</html>