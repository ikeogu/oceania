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

.paging_full_numbers a.paginate_button {
	color: #fff !important;
}

.paging_full_numbers a.paginate_active {
	color: #fff !important;
}

table.dataTable th.dt-right, table.dataTable td.dt-right {
	text-align: right !important;
}

td {
	vertical-align: middle !important;
}

table.dataTable.display tbody tr.odd > .sorting_1,
table.dataTable.order-column.stripe tbody tr.odd > .sorting_1 {

	background-color: white !important;
}

.btn-prawn-inactive{
    color: #cccccc;
    border-color: #cccccc;
    background-color: transparent;
    pointer-events: none;
}

.btn-prawn-custom {
    font-weight:normal;
    color:#34dabb;
    border-color:#34dabb;
    background-color: transparent;
    margin-top:-2px !important;
}
.active_button {
   border-color: #34dabb ;
    color: #34dabb !important;
    font-weight: bold;
    pointer-events: none;
}

/*.btn-prawn-inactive:hover {
     border-color: #34dabb ;
    color: #34dabb !important;
    font-weight: bold;
}*/

.btn{
    width: 75px;
} 

.slim-cell {
	padding-top:2px !important;
	padding-bottom:2px !important;
}


</style>

@endsection

@section('content')
@include('common.header')
@include('common.menubuttons')

<div id="landing-view">
	<!--div id="landing-content" style="width: 100%"-->
	<div class="container-fluid">
		<div class="clearfix"></div>
		<div class="row py-2 align-items-center"
			style="display:flex;height:75px">
			<div class="col-md-12" style="">
				<h2 class="mb-0">Vehicle Management</h2>
			</div>
		</div>

		<div id="response"></div>
		<div id="responseeod"></div>
		<table class="table table-bordered display"
			   id="vehicleList" style="width:100%;">
			<thead class="thead-dark">
			<tr>
				<th class="text-center" style="width:30px; text-align: center !important;">No</th>
				<th class="text-center" style="width:200px;">Number Plate</th>
				<th class="text-center" style="width:auto; text-align: center !important;">Location</th>
				<th class="text-center"
				style="width:80px; text-align: center !important;
				padding-bottom:10px !important">
				Status
				</th>
			</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>

<div id="res"></div>
<style>
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
@endsection

@section('script')
<script>
$(document).ready(function () {
		  
	var directoryTable = $('#vehicleList').DataTable({
	   "destroy": true,
		"processing": false,
		"serverSide": true,
		"autoWidth": false,
		"ajax": {
			"url": "{{route('vehicle_mgmt.datatable')}}",
			"type": "POST",
			'headers': {
				'X-CSRF-TOKEN': '{{ csrf_token() }}'
			},
		},
		columns: [
			{data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{data: 'numberPlate', name: 'numberPlate'},
			{data: 'location', name: 'location'},
			{data: 'act', name: 'act'},
		],
		"order": [],
		"columnDefs": [
			{"className": "dt-center", "targets": [0,1,2]},
			{"className": "dt-center slim-cell", "targets": [3]},
			{"targets": -1, 'orderable': false}
		],
	});

});


</script>
@endsection
@extends('common.footer')

