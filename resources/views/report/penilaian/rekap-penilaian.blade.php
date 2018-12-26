<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Rekap Penilaian</title>
</head>
<body>
<div class="w3-container w3-tiny">
	<table class="w3-table w3-bordered" border="1">
		<thead class="w3-center">
			<tr>
				<th rowspan="2">No</th>
				<th rowspan="2">Area</th>
				<th rowspan="2">Indeks</th>
				<th rowspan="2">Lokasi Gedung/Bangunan</th>
				<th colspan="12">Pencapaian SLA</th>
			</tr>
			<tr>
				<th>Jan</th>
				<th>Feb</th>
				<th>Mar</th>
				<th>Apr</th>
				<th>Mei</th>
				<th>Jun</th>
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
			<?php $indeks = 0;?>
			@forelse($regional as $dataRegional)
			<tr>
				<td colspan="16">{{ $dataRegional->RegNama }}</td>
			</tr>
				@forelse($Rekap as $data)
					@if($dataRegional->RegId == $data->regid)
					<?php $no++ ;?>
					<?php $indeks++ ;?>
					<tr>
						<td>{{ $no }}</td>
						<td>{{ $data->area }}</td>
						<td>{{ $indeks }}</td>
						<td>{{ $data->aset }}</td>
						<td>
							<label>{{ number_format($data->jan ,2 ) }} %</label>
							</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					@endif
				@empty
				@endforelse
			@empty
			@endforelse



		</tbody>
	</table>
</div>

</body>
</html>