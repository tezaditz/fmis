<div>
	<form action="/admin/utility/print" target="_blank" method="POST" accept-charset="UTF-8" class="form-horizontal" enctype="multipart/form-data">
		<div class="form-group">
			<label for="period" class="col-sm-2 control-label">Tahun</label>
			<div class="col-sm-8">
				<div class="input-group">	
                        <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
            			<input type="text" id="tahun" name="tahun" value="{{$tahun}}" class="form-control tahun" readonly="true" />
        		</div>
			</div>
		</div>
<!-- 		<div class="form-group">
			<label for="period" class="col-sm-2 control-label" >Period</label>
			<div class="col-sm-8">
				<select class="form-control" style="width: 100%;" name="period" id="period">
					@foreach($bulan as $bulans)
					<option value="{{ $bulans->uraian }}">{{ $bulans->uraian }}</option>
					@endforeach
				</select>	
			</div>
		</div> -->
		<div class="form-group">
			<label for="period" class="col-sm-2 control-label" >Jenis Utility</label>
			<div class="col-sm-8">
				<select class="form-control" style="width: 100%;" name="type" id="type">
					
					<option value="Listrik">Listrik</option>
					<option value="Air">Air</option>
					<option value="Limbah">Limbah</option>
					<option value="Solar">Solar</option>
					
				</select>	
			</div>
		</div>

		<div class="box-footer">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<div class="form=group">
				<div class="col-sm-2">
					
				</div>
				<div class="col-md-8">
					<button type="submit" class="btn btn-info pull-left">Submit</button>
					<!-- <a href="/admin/utility/print" class="btn btn-info pull-left" target="_blank"> Proses</a> -->
				</div>	
			</div>
			
		</div>
	</form>
</div>