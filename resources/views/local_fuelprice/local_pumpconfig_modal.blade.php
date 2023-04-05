<div class="modal fade pumpConfiguration" 
	id="pumpConfiguration_" tabindex="-1" 
	role="dialog" aria-labelledby="staffNameLabel" 
	aria-hidden="true" data-dirty="0">

	<div class="modal-dialog modal-dialog-centered modal" role="document">
		<div class="modal-content modal-inside bg-blacklobster " >
		<div class="modal-header" style="border-bottom: 1px solid #fff;">
			<h3 style="margin-bottom:0">
			Pump Configuration <small>
				{{$pump->pump_no}} - {{$pump->systemid}}</small>
			</h3>
		</div>
		<div class="modal-body text-center" 
			style="border-bottom-width:0px;border-bottom: 1px solid #fff;">
			<input type="hidden" class="pump_config_pump_id" value="">
			<input type="hidden" class="pump_config_pump_no" value="">
			<div class="col-md-12">
				<div class="row mt-0" style="padding: 0px;">
					<p class="text-left col-md-6" id="info" 
					style="padding-left: 0 !important;padding-right: 0 !important;">
						Pump Configuration Protocol
					</p>
					<div class="dropdown col-md-6">
					<form class="mb-0" name="form1">
						{{$local_pts2_protocol->protocol_name}}
					</form>
					</div>
				</div>
				<div class="row mt-0" style="padding: 0px;">
					<p class="text-left col-md-6" id="info" 
					style="padding-left: 0 !important;padding-right: 0 !important;">
					Baud Rate </p>
					<div class="dropdown col-md-6">
					<form class="mb-0" name="form1">
						{{$baud->baudrate}}
					</form>
					</div>
				</div>
				<div class="row mt-0" style="padding: 0px;">
					<p style="padding-left:0;padding-right:0" 
					class="text-left col-md-6" id="info">
					Pump Port</p>
					<div class="dropdown col-md-6">
					<form class="mb-0" name="form1">
						{{$pump->pump_port}}
					</form>
					</div>
				</div>
				<div class="row mt-0" style="padding: 0px;">
					<p style="padding-left:0;padding-right:0" 
					class="text-left col-md-6" id="info">
					Communication Address</p>
					<div class="dropdown col-md-6">
					<form class="mb-0" name="form1">
						{{$pump->comm_address}}
					</form>
					</div>
				</div>
			</div>
		</div>
		<div class="mb-3 modal-body text-center">
			@foreach ($nozzleData as $nd)
			<div class="col-md-12">
				<div class="mt-0 mb-0 row" style="padding: 0px;">
					<p class="text-left col-md-2 pl-0" id="info">
						Nozzle&nbsp;{{$nd->nozzle_no}}
					</p>
					<div class="text-left col-md-6">
						{{$nd->fuel_name}}
					</div>
					<div class="text-center col-md-2" id="info">MYR</div>
					<div class="col-md-2 nozzle_data_1_price" 
						style="margin: 0;padding: 0;">{{$nd->price}}
					</div>
				</div>
			</div>
			@endforeach
		</div>
	</div>
</div>

