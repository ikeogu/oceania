@extends('common.web')
@include('common.header')

@section('styles')
<style>
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_processing,
.dataTables_wrapper .dataTables_paginate {
	color: black;
}

th, td {
	vertical-align: middle !important;
	text-align: center
}

label, .dataTables_info,
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_processing,
.dataTables_wrapper .dataTables_paginate {
	color: #000 !important;
}

.active_button {
	color: #ccc;
	border: 1px #ccc solid;
}

.active_button:hover, .active_button:active,
.active_button_activated {
	background: transparent !important;
	color: #34dabb !important;
	border: 1px #34dabb solid !important;
	font-weight: bold;
}

.active_button_activated {
	background: transparent;
	color: #34dabb;
	border: 1px #34dabb solid;
	font-weight: bold;
}

.slim-cell {
	padding-top: 2px !important;
	padding-bottom: 2px !important;
}

.pd_column {
	padding-top: 10px;
}
</style>
@endsection

@include('common.menubuttons')
@section('content')

<div class="container-fluid">
	<div class="col-sm-12 pl-0 pr-0" style="">
		<div class="container-fluid" style="padding-left: 0%; padding-right: 0%">
			<div class="row"
				 style="padding-top:0;height:75px;margin-top:0px;margin-bottom:0">
				<div class="col-sm-5" style="align-self:center">
					<h2 class="mb-0">Tank Monitoring</h2>
				</div>

				<div class="col-sm-4"
					 style="align-self:center;float:left;padding-left:0; height: 70px;">
					<div class="row">
						<div class="col-auto" style="align-self:center"
							 id="prd_thumbnail">
						</div>
						<div class="col-auto" style="margin-left: -4%;">
							<h4 style="margin-bottom:0px;padding-top: 0;line-height:1.5;"
								id="prd_name">
							</h4>
							<p style="font-size:18px;margin-bottom:0"
							   id="prd_systemid">
							</p>
						</div>
					</div>
					<div id="prd_atg" style="margin-left: 83px;width: 90%;">
					</div>
				</div>
				<div class="col-sm-3" style="float: right; padding-right: 2.3%">
					<div class="row mb-0" style="float:right;">
						<input type="hidden" name="" id="guide"
							   value="{{route('openitem.openitem_stockout')}}">
						<button onclick=""
								class="btn btn-success btn-guide sellerbutton m-0"
								style="padding:0px;float:right;margin-left:5px;
						border-radius:10px" id="">Guide
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-12 pl-0 pr-0" style="">
		<table id="tableFLocaltionProduct" class="table table-bordered">
			<thead class="thead-dark">
			<tr>
				<th class="text-center" style="">Tank</th>
				<th class="text-center" style="">Status</th>
				<th class="text-center" style="">Product Name</th>
				<th class="text-center" style="">Tank Filling %</th>
				<th class="text-center" style=" font-size: 12pt;">Product mm</th>
				<th class="text-center" style=" ">Water mm</th>
				<th class="text-center" style="">Temperature <span>&#176;</span>C</th>
				<th class="text-center" style=" ">Product (ℓ)</th>
				<th class="text-center" style="">Water (ℓ)</th>
				<th class="text-center" style="">Ullage (ℓ)</th>
				<th class="text-center" style="font-size: .8985em;">TC Volume</th>

			</tr>
			</thead>
			<tbody id="shows">
			</tbody>
		</table>
	</div>
</div>
<input type="hidden" name="" id="ipaddr" value="{{env('PTS_IPADDR') }}">

@endsection

@section('script')
<script src="{{asset('js/number_format.js')}}"></script>

<script>

$.ajaxSetup({
	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
});


let loyalty = "loyalty";
let qty = "qty";

var tableData = {};
var openitemtable = $('#tableFLocaltionProduct').DataTable({
	"processing": false,
	"serverSide": true,
	"autoWidth": true,
	"ajax": {
		/* This is just a sample route */
		"url": "{{route('tank.tankMonitoringList')}}",
		"type": "POST",
		data: function (d) {
			return $.extend(d, tableData);
		},
		'headers': {
			'X-CSRF-TOKEN': '{{ csrf_token() }}'
		},
	},
	columns: [
		{
			data: 'tank', name: 'tank', render: function (data) {
				return JSON.parse(data)["tank"]["tank_no"] == null ? 0 : JSON.parse(data)["tank"]["tank_no"];

			}
		},
		{data: 'water_mm', name: 'water_mm'},
		{
			data: 'product', name: 'product', render: function (data) {
				data = JSON.parse(data);
				return data["tank"]["product"] != null ? data["tank"]["product"]["thumbnail_1"] != null ?
					"<a href='javascript:void(0)' style='text-decoration: none;padding-top: 15px;' onclick='productResponse(" + JSON.stringify(data["tank"]) + ")'> <img width='25px' height='25px' style='margin: 0px 0px 0px 0px' src='/{{\App\Http\Controllers\OpenitemController::$IMG_PRODUCT_LINK}}" + data["tank"]["product"]["systemid"] + "/thumb/" + data["tank"]["product"]["thumbnail_1"] + "' alt=''> " + (getProductionName(data)) + "</a>"
					: "<a href='javascript:void(0)' style='text-decoration: none;padding-top: 15px;' onclick='productResponse(" + JSON.stringify(data["tank"]) + ")'>  <img src='' alt='' width='0px' height='25px'>" + (getProductionName(data)) + "</a>"
					: "<a href='javascript:void(0)' style='text-decoration: none;padding-top: 15px;' onclick='productResponse(" + JSON.stringify(data["tank"]) + ")'>  <img src='' alt='' width='0px' height='25px'>" + (getProductionName(data)) + "</a>";

			}
		},
		{
			data: 'tank_filling_pct', name: 'tank_filling_pct', render: function (data) {
				return number_format((data) / 100, 2);

			}
		},
		{data: 'product_mm', name: 'product_mm'},
		{data: 'water_mm', name: 'water_mm'},
		{data: 'temperature_c', name: 'temperature_c'},
		{
			data: 'product_l', name: 'product_l', render: function (data) {
				return number_format((data) / 100, 2);

			}
		},
		{
			data: 'water_l', name: 'water_l', render: function (data) {
				return number_format((data) / 100, 2);

			}
		},
		{
			data: 'ullage_l', name: 'ullage_l', render: function (data) {
				return number_format((data) / 100, 2);

			}
		},
		{
			data: 'tc_volume', name: 'tc_volume', render: function (data) {
				return number_format((data) / 100, 2);

			}
		},

	],
	"order": [0, 'desc'],
	"columnDefs": [
		{"className": "dt-left vt_middle", "targets": [2]},
	],
});


function goTo(route) {
	window.open($("#" + route).val(), '_blank');
}

function getProductionName(data) {
	return (data["tank"]["product"] == null ? 'Product name' : (data["tank"]["product"]["name"] == null ? 'Product name' : data["tank"]["product"]["name"]));
}


function productResponse(tank) {
	console.log(tank);
	let product = tank["product"];
	if (product==null)
	{
		$("#prd_thumbnail").html('');
		$("#prd_name").html('Product Name');
		$("#prd_name").css("margin-top","15%");
		$("#prd_systemid").html('');
		$("#prd_atg").html('');

	}else{
		$("#prd_name").css("margin-top","0%");

		$("#prd_thumbnail").html('');
		$("#prd_thumbnail").html(product["thumbnail_1"] ? ' <img src="/images/product/' + product["systemid"] + '/thumb/' + product["thumbnail_1"] + '"\n' +
			'                             alt="Logo" width="70px" height="70px" ' +
			'                             style="object-fit:contain;float:right;margin-left:0;margin-top:0;">' : '');
		$("#prd_name").html(product["name"] ? product["name"] : 'Product Name');
		$("#prd_systemid").html('');
		$("#prd_atg").html('');
		$("#prd_systemid").html(product["systemid"]);
		$("#prd_atg").html("<div class='col-4' style='text-align: right; background-color: #e0e0d9'>0/"+tank['max_capacity']+"\n" +
			"                            <span style=\"font-size:20px; padding-right: 2px\">&nbsp;&ell;</span></div>");

	}

}

probeGetTankVolumeForHeight(60,1,10000,$("#ipaddr").val());

function probeGetTankVolumeForHeight(secondes,probe_no,height,ipaddr) {
	setInterval(function(){
		$.ajax({
			method: "get",
			url: "/tank/probeGetTankVolumeForHeight/"+probe_no+"/"+height+"/"+ipaddr,
		})
				.done((data) => {
					console.log("data", data);

				})
				.fail((data) => {
					console.log("data", data)
				});
	}, (1000*secondes));
}
</script>

@endsection
@include('common.footer')
