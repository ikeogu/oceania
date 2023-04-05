@extends('common.web')
@section('styles')
<style>
.Wrapper {
	margin: 0px 15px;
}

.tankbtn {
	padding-left: 0;
	padding-right: 0;
	float: right;
	border-radius: 10px;
	margin: 0px 0px 5px 0px;
}

.guidebtn {
	padding-left: 0;
	padding-right: 0;
	float: right;
	border-radius: 10px;
	margin: 0px 5px 5px 0px;
}

.butns {
	display: none
}

th, td {
	vertical-align: middle !important;
	text-align: center
}

td {
	text-align: center;
}

#ogTankNoModal > .modal-dialog, #inventoryCogsModal > .modal-dialog {
	width: 284px;
}

.f18 {
	font-size: 18px !important;
}

/* second css */


.slim_cell {
	padding-top: 2px !important;
	padding-bottom: 2px !important;
}

#masterBackBar {
	position: relative;
	z-index: 0;
	height: 30px;
}

#myProgress {
	position: absolute;
	z-index: 1;
	width: 100%;
	height: 30px;
	background-color: #ddd;
	color: black;
	padding-top: 2px;
	text-align: right;
	padding-right: 10px;
}

#myBar {
	position: absolute;
	z-index: 2;
	width: 10%;
	opacity: 50%;
	height: 30px;
	text-align: center;
}

#tankMgt_length {
	color: #000 !important;
}

#tankMgt_length, #tankMgt_filter, #tankMgt_info {
	color: #000 !important;
}
</style>
@endsection

@section('content')
@include('common.header')
@include('common.menubuttons')

<div class="Wrapper">
	<div class="d-flex" style="width: 100%; height: 75px;">
		<div style="padding-left:0" class="col align-self-center">
			<h2>Tank Management</h2>
		</div>

		<div style="justify-content:center;display: flex;
		align-items:center;">
		<span
				style="margin-bottom:0;padding-top: 4px;
			width:150px; text-align: center;"
				id="hrefBtn" href_id="">
			<h5>{{\App\Models\Location::getLocation()["name"]}}</h5>
		</span>
		</div>

		<div style="padding-right:0" class="col col-auto align-self-center">
			<button onclick="showTankSave()"
					class="btn btn-success sellerbutton tankbtn"
					id="addTank">+Tank
			</button>

			<button class="btn btn-success btn-guide sellerbutton guidebtn"
					data-toggle="modal"
					data-target="#colorGuide" id="">
				Guide
			</button>
		</div>
	</div>

	<div style="padding-left:0;padding-right:0" class="col-sm-12">
		<table id="tankMgt" class="table table-bordered">
			<thead class="thead-dark">
			<tr>
				<th style="width:30px;">No</th>
				<th style="">Tank&nbsp;No.</th>
				<th style="">Tank&nbsp;ID</th>
				<th style="width: auto" class="text-left">Product Name</th>
				<th style="">ATG</th>
				<th style="text-align:center">Height,&nbsp;mm</th>
				<th style="text-align:center;" data-orderable="false"></th>
			</tr>
			</thead>
			<tbody id="shows">
			</tbody>
		</table>
	</div>
</div>


<div class="modal" id="ogTankNoModal">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<!-- Modal body -->
			<div class="modal-body">
				<input id="ogTankNoInput" onkeypress='validate(event)'
					   class="pl-1" placeholder="Tank No"
					   style="width: 100%; border: 1px solid #ddd;
				text-align: center !important;">
				<input type="hidden" id="buffer_main_no" value="">
			</div>
		</div>
	</div>
</div>

<div class="modal" id="inventoryCogsModal">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<!-- Modal body -->
			<div class="modal-body">
				<input id="inventoryCogsInput" onkeypress='validate(event)'
					   class="pl-1" placeholder="Tank Height"
					   style="width: 100%; border: 1px solid #ddd;
				text-align: center !important;">
				<input type="hidden" id="buffer_main_height" value="">
			</div>
		</div>
	</div>
</div>

<div id="productsModal" class="modal fade" tabindex="-1" role="dialog"
	 aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered"
		 style="max-width: 600px;margin: auto;">
		<div class="modal-content bg-greenlobster">
			<div class="modal-header">
				<h3 class="mb-0">Oil & Gas</h3>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12" style="padding: 8px;margin: 0px auto;">
						<div id="productList" class="creditmodelDV"
							 style="display:flex; flex-wrap: wrap;
					justify-content: left;margin-left: 26px;">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Start Location Modal -->
<div id="locationModal" class="modal fade" tabindex="-1" role="dialog"
	 aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-md" role="document">
		<div class="modal-content bg-greenlobster">
			<div class="modal-header">
				<h3 class="modal-title" id="myModalLabel">Location</h3>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col align-self my-modal">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!--Confirmation Alert Modal-->
<div class="modal fade" id="showMsgModal" tabindex="-1" role="dialog" aria-labelledby="showMsgModal"
	 aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered  mw-75 w-50" role="document">
		<div class="modal-content modal-inside bg-greenlobster">
			<div style="border-width:0" class="modal-header text-center"></div>
			<div class="modal-body text-center">
				<h5 class="modal-title text-white"
					id="statusModalLabel">Do you want to permanently delete this product?</h5>
			</div>
			<div class="modal-footer"
				 style="border-top:0 none; padding-left: 0px; padding-right: 0px;">
				<div class="row"
					 style="width: 100%; padding-left: 0px; padding-right: 0px;">
					<div class="col col-m-12 text-center">
						<button type="button" id="btnConfirm"
								class="btn btn-primary primary-button"
								data-dismiss="modal">Yes
						</button>
						<button type="button"
								class="btn btn-danger primary-button"
								data-dismiss="modal">No
						</button>
					</div>
				</div>
				<form id="status-form" action="{{ route('logout') }}"
					  method="POST" style="display: none;">
					@csrf
				</form>
			</div>
		</div>
	</div>
</div>
</div>


<!-- Modal -->
<div class="modal fade" id="segmentModal" role="dialog" style="">
	<div class="modal-dialog modal-dialog-centered modal" role="document">
		<!-- Modal content-->
		<div class="modal-content bg-greenlobster" style="border-radius:10px">
			<div class="modal-header">
				<h3 class="mb-0">Franchise Operator</h3>
			</div>
			<div class="modal-body text-center">
				<div class="f18">Franchisor: <span id="mfsor"></span></div>
				<div class="f18 mb-3">Merchant ID: <span id="midfsor"></span></div>
				<div class="f18">Franchisee: <span id="mfsee"></span></div>
				<div class="f18 mb-3">Merchant ID: <span id="midfsee"></span></div>
				<div class="f18">Franchise: <span id="mfse"></span></div>
				<div class="f18 mb-2">Franchise ID: <span id="midfse"></span></div>
			</div>
		</div>
	</div>
</div>
<!--- tank size modal -->
<div class="modal fade" id="tankSizeModal" tabindex="-1"
	 role="dialog" aria-labelledby="staffNameLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered  mw- 75 w- 50" role="document">
		<div class="modal-content modal---inside bg-purplelobster">
			<div class="modal-header">
				<h3 class="modal-title text-white" id="statusModalLabel">Tank</h3>
			</div>
			<div class="modal-body">

				<ul style="padding: 0;margin: 0;">

					<li style="display:flex;align-items: center;
			justify-content: center;margin-bottom:5px">
						<span>Max Cpacity&nbsp;&nbsp;</span>
						<input class="form-control vehicle_text_input text-right"
							   type="number" placeholder="0" style="width: 30%;margin: 0px 5px 0 10px;"
							   id="tank_max_capacity"/>
						<span style="font-size:20px">&nbsp;&ell;</span>
					</li>

				</ul>

			</div>
			<!-- div class="modal-footer" style="border:0;">
			</div --->
		</div>
	</div>

</div>


<div class="modal fade" id="fillFields2" tabindex="-1" role="dialog">
	<div class="modal-dialog  modal-dialog-centered mw-75 w-50">

		<!-- Modal content-->
		<div class="modal-content  modal-inside bg-purplelobster">
			<div class="modal-header" style="border:none;">&nbsp;</div>
			<div class="modal-body text-center">
				<h5 class="mb-0" id="return_data2">
					Please fill all fields
				</h5>
			</div>
			<div class="modal-footer" style="border: none;">&nbsp;</div>
		</div>

	</div>
</div>

<div class="modal fade" id="updateModal" tabindex="-1"
	 role="dialog" aria-labelledby="staffNameLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered"
		 role="document" style="width: 300px">
		<div class="modal-content " >

			<div class="modal-body bg-purplelobster">
				<div class='text-center' style="margin:auto">
					<input type="number" id="value" placeholder="0"
						   style="text-align:right"
						   class="form-control" />
					<input type="hidden" id="key"
						   style="text-align:right"
						   value="qty" class="form-control"
						   placeholder='0'/>
					<input type="hidden" id="element"
						   style="text-align:right"
						   value="" class="form-control"
						   placeholder='0'/>
				</div>
			</div>

		</div>
	</div>

</div>

<div class="modal fade" id="deleteOpenModal" tabindex="-1"
	 role="dialog" aria-labelledby="staffNameLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered  mw-75 w-50"
		 role="document">
		<div class="modal-content modal-inside bg-purplelobster">

			<div class="modal-header" style="border-width:0"></div>
			<div class="modal-body text-center">
				<h5 class="modal-title text-white">
					Do you want to permanently
					delete this tank?
				</h5>
			</div>
			<div class="modal-footer text-center" style="border-width:0">
				<div class="row" style="width:100%;">
					<input type="hidden" name="" id="prdId" value="">
					<div class="col col-m-12 text-center">
						<button class="btn btn-primary primary-button"
								onclick="yesDelete()">Yes
						</button>
						<button class="btn btn-danger primary-button"
								onclick="noDelete()">No
						</button>
					</div>
				</div>
			</div>
		</div>
		</ul>
	</div>
</div>


<div id="productResponse"></div>
<div id="showEditInventoryModal"></div>
<div id="showEditInputInventoryModal"></div>
<input type="hidden" name="" id="ipaddr" value="{{env('PTS_IPADDR') }}">
@endsection

@section('script')
<script src="{{asset('js/number_format.js')}}"></script>

<script>
	$.ajaxSetup({
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
	});

	const format = num =>
		String(num).replace(/(?<!\..*)(\d)(?=(?:\d{3})+(?:\.|$))/g, '$1,')
	/*$(document).ready( function () {*/
	var tableData = {};
	var table = $('#tankMgt').DataTable({
		"processing": false,
		"serverSide": true,
		"autoWidth": false,
		"ajax": {
			/* This is just a sample route */
			"url": "{{route('tank.list')}}",
			"type": "POST",
			data: function (d) {
				return $.extend(d, tableData);
			},
			'headers': {
				'X-CSRF-TOKEN': '{{ csrf_token() }}'
			},
		},
		columns: [
			{data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{
				data: 'tank_no', name: 'tank_no', render: function (data) {
					return "<a href='javascript:void(0)' style='text-decoration: none;' onclick='updatePop(" + JSON.parse(data)["id"] + "," + JSON.parse(data)["tank_no"] + ")'>" + (JSON.parse(data)["tank_no"]==null?0: format((JSON.parse(data)["tank_no"])))  + "</a>"
				}
			},
			{data: 'systemid', name: 'systemid'},
			{
				data: 'product', name: 'product', render: function (data) {
					data = JSON.parse(data);
					return data["product"] != null ? data["product"]["thumbnail_1"] != null ?
						"<a href='javascript:void(0)' style='text-decoration: none;padding-top: 15px;' onclick='productResponse(" + data['id'] + ")'> <img width='25px' height='25px' style='margin: 0px 0px 0px 0px' src='/{{\App\Http\Controllers\OpenitemController::$IMG_PRODUCT_LINK}}" + data["product"]["systemid"] + "/thumb/" + data["product"]["thumbnail_1"] + "' alt=''> " + (getProductionName(data)) + "</a>"
						: "<a href='javascript:void(0)' style='text-decoration: none;padding-top: 15px;' onclick='productResponse(" + data['id'] + ")'>  " + (getProductionName(data)) + "</a>"
						: "<a href='javascript:void(0)' style='text-decoration: none;padding-top: 15px;' onclick='productResponse(" + data['id'] + ")'>  " + (getProductionName(data)) + "</a>";
				}
			},
			{data: 'max_capacity', name: 'max_capacity', render: function (data) {
					return "<div class='col-12 p-1' style='text-align: right; background-color: #e0e0d9'>0/"+(data==null?0:data)+" <span style=\"font-size:20px; padding-right: 5px\">&nbsp;&ell;</span></div>"
				}},
			{data: 'height', name: 'height', render: function (data) {
					return "<a href='javascript:void(0)' style='text-decoration: none;' onclick='updatePopH(" + JSON.parse(data)["id"] + "," + JSON.parse(data)["height"] + ")'>" + (JSON.parse(data)["height"]==null?0: format((JSON.parse(data)["height"]))) + "</a>"
				}},
			{data: 'action', name: 'action'},
		],

		"columnDefs": [
			{"width": "3%", "targets": [0,6]},
			{"width": "5%", "targets": [1,5]},
			{"width": "50%", "targets": 3},
			{"width": "10%", "targets": [2]},
			{"width": "15%", "targets": [4]},
			{"className": "dt-left vt_middle", "targets": [3]},
			{orderable: false, targets: [-1]},
		],

	});


	function  showTankSave(){
		$("#tank_max_capacity").val('');
		$("#tankSizeModal").modal('show');
	}


	$('#tankSizeModal').on('hidden.bs.modal', function (e) {
		if ($("#tank_max_capacity").val()!='' && parseInt($("#tank_max_capacity").val())>0) {
			save();
		}
	});


	function save() {
		$.ajax({
			method: "post",
			url: "{{route('tank.save')}}",
			data:{max_capacity:$("#tank_max_capacity").val()}
		})
		.done((data) => {
			console.log("data", data);
			table.ajax.reload();
			$("#return_data2").html("Tank added successfully");
			$("#fillFields2").modal('show');
			setTimeout(function () {
				$('#fillFields2').modal('hide');
			}, 2000);

		})
		.fail((data) => {
			console.log("data", data)
		});
	}


	function getProductionName(data) {
		return (data["product"] == null ? 'Product name' :
			(data["product"]["name"] == null ? 'Product name' :
			 data["product"]["name"]));
	}


	function updatePop(data, old_value) {
		$("#updateModal").modal("show");
		$("#key").val("tank_no");
		$("#value").val(old_value);
		$("#element").attr("value", data);
	}


	function updatePopH(data, old_value) {
		$("#updateModal").modal("show");
		$("#key").val("height");
		$("#value").val(old_value);
		$("#element").attr("value", data);
	}


	$('#updateModal').on('hidden.bs.modal', function (e) {
		let key = $("#key").val();
		let value = $("#value").val();
		let element = $("#element").val();
		if (value!=null){
			$.ajax({
				method: "post",
				url: "{{route('tank.update_key')}}",
				data: {key: key, value: value, element: element}
			})
			.done((data) => {
				table.ajax.reload();
			})
			.fail((data) => {
				console.log("data", data)
			});
		}
	});


	function productResponse(tank) {
		$.ajax({
			method: "post",
			url: "{{route('tank.products')}}",
			data:{tank:tank}
		}).done((data) => {
			console.log("data",data);
			$("#productResponse").html(data);
			$("#modal").modal('show');
		})
		.fail((data) => {
			console.log("data", data)
		});
	}


	function deleteMe(id) {
		$("#deleteOpenModal").modal("show");
		$("#prdId").attr("value", id);
	}


	function noDelete(){
		$("#deleteOpenModal").modal("hide");
	}


	function yesDelete(){
		$.ajax({
			method: "post",
			url: "{{route('tank.delete')}}",
			data: {id: $("#prdId").val()}
		})
		.done((data) => {
			console.log("data", data);
			$("#deleteOpenModal").modal("hide");
			table.ajax.reload();
			$("#return_data2").html("Tank deleted successfully");
			$("#fillFields2").modal('show');
			setTimeout(function() {
				$('#fillFields2').modal('hide');
			}, 2000);
		})
		.fail((data) => {
			console.log("data", data)
		});
	}


	//probeGetTankVolumeForHeight
	probeGetTankVolumeForHeight(60,1,10000,$("#ipaddr").val());

	function probeGetTankVolumeForHeight(secondes,probe_no,height,ipaddr) {
		setInterval(function(){
			$.ajax({
				method: "get",
				url: "/tank/probeGetTankVolumeForHeight/"+probe_no+"/"+height+"/"+ipaddr,
			})
					.done((data) => {
						console.log("data probeGetTankVolumeForHeight", data);

					})
					.fail((data) => {
						console.log("data probeGetTankVolumeForHeight", data)
					});
		}, (1000*secondes));
	}


	//probeGetTankVolumeForHeight
	probeGetMeasurements(60,10000,$("#ipaddr").val());

	function probeGetMeasurements(secondes,probe_no,ipaddr) {
		setInterval(function(){
			$.ajax({
				method: "get",
				url: "/tank/probeGetMeasurements/"+probe_no+"/"+ipaddr,
			})
					.done((data) => {
						console.log("data probeGetMeasurements", data);

					})
					.fail((data) => {
						console.log("data probeGetMeasurements", data)
					});
		}, (1000*secondes));
	}


</script>
@endsection

@extends('common.footer')
