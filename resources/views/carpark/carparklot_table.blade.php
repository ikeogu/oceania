<tr style="color: white; height: 40px;padding-top: 20px;border-bottom: 2px #a0a0a0 solid;
	color:white;">
	<th  class="text-center" style="width: 65px;">
	Lot&nbsp;No
	</th>
	<th class="text-center" style="width: 140px;">
	Lot&nbsp;ID
	</th>
	<th class="text-center" style="width: 128px;">
	In
	</th>
	<th class="text-center" style="width: 128px;">
	Out
	</th>
	<th class="text-center" style="width: 110px;">
	Charger
	</th>
	<th class="text-center" style="width:100px;">
	@if( $current_setting_mode == 'hour' )
			Hour
		@else
			kWh
		@endif
	</th>
	<th class="text-center" style="width:100px;">
	Rate
	</th>
	<th class="text-center" style="width: 120px;">
	{{empty($terminal->currency) ? 'MYR': $terminal->currency }}

	</th>
	<th class="text-center" style="width: 110px;">
	
	</th>
</tr>

@foreach($carparkOperas as $opera)
@php
@endphp

<tr style="color: white; height: 40px;padding-top: 20px;">
	<td class="text-center" style="width: 65px;">
		{{$opera->lot_no}}
	</td>
	<td class="text-center" style="width: 140px;">
		{{$opera->systemid}}
	</td>

	<td class="text-center" style="width: 128px;">
		@if( !empty( $opera->carparkoper->in ) )
			{{$opera->carparkoper->in==null?'-':date("dMy h:i:s",
				strtotime($opera->carparkoper->in))}}
		@endif
	</td>
	<td class="text-center" style="width: 128px;">
		@if( !empty( $opera->carparkoper->out ) )
			{{$opera->carparkoper->out==null?'-':date("dMy h:i:s",
				strtotime($opera->carparkoper->out))}}
		@endif
	</td>
	@if(( isset( $opera->carparkoper->in ) && $opera->carparkoper->in!=null &&
		  isset( $opera->carparkoper->out ) && $opera->carparkoper->out!=null &&
		 $opera->carparkoper->payment==0))
		<td class="text-center" style="width: 110px;padding-top:10px;">
			<button class="btn-prawn-inactive btn
				trigger_active_{{$opera->carparkoper->id}}
				active_button  active_button_active "
				style="min-width: 75px; height: 37.53px; margin-bottom: 5%;cursor: text;pointer-events:none;">Active
			</button>
		</td>
		@else
		<td class="text-center" style="width: 110px;padding-top:10px;">
			<button onclick="statusClick({{$opera->id}},'{{$opera->amount}}',
				{{@$opera->carparkoper->id}})"
				class="btn
				@if( ( !empty( $opera->carparkoper->in ) &&
					   !isset($opera->carparkoper->out) ) ) btn-prawn-custom
				@else btn-prawn-inactive
				@endif
				active_button active_button_active "
				style="min-width: 75px; height: 37.53px; margin-bottom: 5%">Active
			</button>
		</td>
		@endif

	<td class="text-center" style="width:100px;">

		{{-- {{($opera->carparkoper->in==null || $opera->carparkoper->out==null)?"0":$opera->hours}} --}}
		@if( $current_setting_mode == 'hour' )
		@if( empty( $opera->carparkoper->in ) OR empty( $opera->carparkoper->out ) )
			0
		@else
			{{$opera->hours}}
		@endif
		@else
		@if($opera->stop_meter == 0 or $opera->stop_meter == null)
		0.000

		@else
		{{number_format(($opera->stop_meter - $opera->start_meter) / 1000,3)}}

		@endif
		@endif
	</td>

	<td class="text-center" style="width:100px;">
	@if( $current_setting_mode == 'hour' )
	{{number_format($opera->rate / 100,2)}}

		@else
		{{number_format($opera->kwh / 100,2)}}

		@endif
	</td>
	
	<td class="text-center" style="width: 120px;">
	@if( $current_setting_mode == 'hour' )
	{{number_format(($opera->rate / 100) * $opera->hours,2)}}

		@else
		@if($opera->stop_meter == 0)
		0.00

		@else
		{{number_format((((($opera->stop_meter - $opera->start_meter) / 1000)*$opera->kwh) / 100),2)}}

		@endif
		@endif
	</td>
	<!-- <td class="text-center" style="width:5%;">
	@if( $opera->heartbeat != 'no' )
	<i class="fas fa-circle " style="color: green;"></i>
	@else
	<i class="fas fa-circle " style="color: grey;"></i>
	@endif
	</td> -->
	<td class="text-center" style="width: 110px;padding-top:10px;">
		@if((isset( $opera->carparkoper->in ) && $opera->carparkoper->in!=null &&
			isset( $opera->carparkoper->out ) && $opera->carparkoper->out!=null))
			@if($opera->carparkoper->payment!=0)
			<div style="position:relative;left:30px">
				<button
					class="btn-pay-prawn-inactive btn
					trigger_save_{{$opera->systemid}} active_button"
					style="margin-left:10px;min-width: 75px;
					height: 37.53px; margin-bottom: 5%; cursor: text;pointer-events:none;">Pay
				</button>
			</div>
			@else
			<div style="position:relative;">
				<button onclick="payClick(
					{{$opera->systemid}},
					{{$opera->hours}},
					{{$opera->rate}},
					'{{$opera->rate}}',
					'{{(($opera->stop_meter - $opera->start_meter) / 1000)*$opera->rate}}',
					{{(($opera->stop_meter - $opera->start_meter) / 1000)*$opera->rate}},
					'{{$opera->carparkoper->id}}',
					'{{$opera->id}}',
					{{($opera->stop_meter - $opera->start_meter) / 1000}},
					'{{($opera->stop_meter - $opera->start_meter) / 1000}}',
					'{{$current_setting_mode}}',
					'{{$opera->kwh}}'
					)"
					class="btn-pay-prawn-custom btn
					trigger_save_{{$opera->systemid}} active_button"
					style="min-width: 75px;
					height: 37.53px; margin-bottom: 5%">Pay
				</button>
			</div>
			@endif

		@else

		<div style="position:relative;">
		<button
			class="btn-pay-prawn-inactive btn
				trigger_save_{{$opera->systemid}} active_button"
			style="min-width: 75px;
				height: 37.53px; margin-bottom: 5%;cursor: text;pointer-events:none;">Pay
		</button>
		</div>

		@endif
	</td>
</tr>

@endforeach
@if(isset($showmodal))
<script>
$(function() {
	$('#statusModalLabelMsg').html('No connection could be made');
	$('#modalMessage').modal('show');
});
</script>
@endif

<script>
	$(document).ready(function(){
		localStorage.setItem('stop_count_var', {{$stop_count}});
		localStorage.setItem('transaction_count_var', {{$transaction_count}});
		localStorage.setItem('paid_count_var', {{$paid_count}});
		});
</script>
