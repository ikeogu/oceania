@extends('common.web')
@section('styles')
<script type="text/javascript" src="{{asset('js/console_logging.js')}}"></script>
<script type="text/javascript" src="{{asset('js/qz-tray.js')}}"></script>
<script type="text/javascript" src="{{asset('js/opossum_qz.js')}}"></script>

<style>
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_processing,
.dataTables_wrapper .dataTables_paginate {
	color: black !important;
	font-weight: normal !important;
}

#receipt-table_length, #receipt-table_filter,
#receipt-table_info, .paginate_button {
	color: white !important;
}

#eodSummaryListModal-table_paginate,
#eodSummaryListModal-table_previous,
#eodSummaryListModal-table_next,
#eodSummaryListModal-table_length,
#eodSummaryListModal-table_filter,
#eodSummaryListModal-table_info {
	color: white !important;
}

label, #receipt-table_info {
	color: black !important;
}

.paging_full_numbers a.paginate_button {
	color: #fff !important;
}

.paging_full_numbers a.paginate_active {
	color: #fff !important;
}

.sorting_1 {
	background-color: white !important;
}

table.dataTable th.dt-right, table.dataTable td.dt-right {
	text-align: right !important;
}

td {
	vertical-align: middle !important;
}
</style>
@endsection

@section('content')
@include('common.header')
@include('common.menubuttons')

<div id="loadOverlay" style="background-color: white; position:absolute; top:0px; left:0px;
	width:100%; height:100%; z-index:2000;">
</div>

<div id="landing-view">
	<div class="container-fluid">
		<div class="clearfix"></div>
		<div class="row py-2 align-items-center"
			 style="display:flex;height:75px">
			<div class="col" style="width:70%">
				<h2 style="margin-bottom: 0;">
					Personal Shift List
				</h2>
			</div>
			<div class="col-md-2">
				<h5 style="margin-bottom:0">{{ $location->name??"" }}</h5>
				<h5 style="margin-bottom:0">{{ $location->systemid??"" }}</h5>
			</div>
			<div class="middle;col-md-3">
				<h5 style="margin-bottom:0;">Terminal ID: {{ $terminal->systemid??"" }}</h5>

			</div>
			<div class="col-md-2 text-right">
				<h5 style="margin-bottom:0;"></h5>
			</div>
		</div>

		<div id="pa-view">
			<table id="receipt-table" class="table table-bordered">
				<thead class="thead-dark">
				<tr>
					<th style="text-align:center;width:5%;" >No</th>
					<th style="text-align:center;width:30%">Date</th>
					<th style="text-align:center;width:auto">Staff</th>

				</tr>
				</thead>
				<tbody style="background: white">
					@foreach ($pshift as $row)
				<tr>
					<td style="text-align: center;width:5%;" >
						{{ $loop->index + 1 }}
					</td>
					{{--
					<td style="text-align: center;width: 40%">
						<a href="#" style="text-decoration: none;"
							onclick="pShiftReceiptModal({{$row->systemid}}, '{{$row->created_at}}')">{{date('dMy H:i:s', strtotime($row->created_at??''))}}
						</a>
					</td>
					--}}
					<td style="text-align: center;width: 40%">
						<a href="#" style="text-decoration: none;"
							onclick="pssReceiptPopup('{{date('dMy H:i:s', strtotime($row->login??''))}}',
								'{{empty($row->logout) ? '':date('dMy H:i:s', strtotime($row->logout))}}','{{$row->systemid}}','{{$row->shift_id}}')"
								>{{date('dMy H:i:s', strtotime($row->login??''))}}
						</a>
					</td>

					<td style="text-align: center;width: auto">
						{{$row->systemid}}

					</td>
				</tr>
				@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="modal fade" id="eodSummaryListModal" tabindex="-1" role="dialog"
	 aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered mw-75"
		 style="width:370px" role="document">
		<div id="eodSummaryListModal-table-div"
			 class="modal-content bg-white" style="width:370px;  border-radius:10px;">
		</div>
	</div>
</div>

<div class="modal fade" id="eodModal_1" tabindex="-1" role="dialog"
	 style="overflow:auto;" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered mw-75"
		 style="width:370px" role="document">
		<div id="receipt-model-div" class="modal-content bg-white" style=" border-radius:10px;">
		</div>
	</div>
</div>

<div id="res"></div>
<style>
.btn {
	color: #fff !Important;
}

.form-control:disabled, .form-control[readonly] {
	background-color: #e9ecef !important;
	opacity: 1;
}

#void_stamp {
	font-size: 100px;
	color: red;
	position: absolute;
	z-index: 2;
	font-weight: 500;
	margin-top: 130px;
	margin-left: 10%;
	transform: rotate(45deg);
	display: none;
}
</style>


@section ('script')
<script>

$('#eodModal_1').on('hide.bs.modal', function (e) {
	let save = true;
	let rnoci = $("#non_op_cash_in").val().replace(/,/g, '')
	let rnoco = $('#non_op_cash_out').val().replace(/,/g, '')
	let rsd = $('#sales_drop').val().replace(/,/g, '')
 	let rada = $('#actual_drawer_amount').val().replace(/,/g, '')

  let ps_non_op_cash_in = isNaN(rnoci) ? 0 : rnoci*100;
  let ps_non_op_cash_out = isNaN(rnoco) ? 0 : rnoco*100;
  let ps_sales_drop = isNaN(rsd) ? 0 : rsd*100;
  let ps_actual_drawer_amount = isNaN(rada) ? 0 : rada*100;

	if (save) {
		$.ajax({
		    url: "{{ route('pshift.pss-save-inputs') }}",
		    type: "POST",
		    data: {
				shift_id: shift_id,
		        non_op_cash_in: ps_non_op_cash_in,
		        non_op_cash_out: ps_non_op_cash_out,
		        sales_drop: ps_sales_drop,
		        actual_drawer_amount: ps_actual_drawer_amount,
		    },
		    'headers': {
		        'X-CSRF-TOKEN': '{{ csrf_token() }}'
		    },
		    success: function(response) {
		        console.log(response);
		    },
		    error: function(resp) {
		      console.log(response);
		    }
		});
	}
})

$.ajaxSetup({
	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
});

function pssReceiptPopup(login_time, logout_time, user_systemid, shift_id) {
	shift_id = shift_id
	$('#eodSummaryListModal').modal('hide').html();
	$('#optlistModal').modal('hide').html();
	$('#receiptoposModal').modal('hide');
	$('#eodpssModal_1').modal('hide');

	$.ajax({
		url: "{{route('pshift.details')}}",
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		type: 'post',
		data: {login_time, logout_time, user_systemid, shift_id},
		success: function (response) {
			// console.log(response);
			//res
			$('#receipt-model-div').html(response);
			$('#eodModal_1').modal('show');
		},
		error: function (e) {
			$('#responseeod').html(e);
			$("#msgModal").modal('show');
		}
	});
}


$(document).ready(function() {
	$("#loadOverlay").css("display","none");
	$('#receipt-table').DataTable({})
})

</script>
@endsection
@extends('common.footer')
