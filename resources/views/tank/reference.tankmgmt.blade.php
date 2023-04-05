@extends('industry.oil_gas.og_oilgas')

@section('content_landing')
<style>
.butns{
	display: none
}
th, td{
	vertical-align: middle !important;
	text-align: center
}
td{
	text-align: center;
}
#ogTankNoModal > .modal-dialog, #inventoryCogsModal > .modal-dialog {
	width: 284px;
}
.f18 {
	font-size: 18px !important;
}
</style>
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
			<h5>{{$location->branch}}</h5>
		</span>
	</div>

	<div style="padding-right:0" class="col col-auto align-self-center">

	@if (!empty(request()->t_id)) 
		<button class="btn btn-success sellerbutton"
			style="padding-left:0;padding-right:0;float:right;
				margin: 0px 0px 5px 0px;"
			id="addTank">+Tank
		</button>
	@endif
		
		<button class="btn btn-success bg-guide sellerbutton"
			style="padding-left:0;padding-right:0;float:right;
				margin: 0px 5px 5px px;"
				 data-toggle="modal" data-target="#colorGuide"
			id="">Guide
		</button>
	</div>
</div>

<div style="padding-left:0;padding-right:0" class="col-sm-12">
	<table id="tankMgt" class="table table-bordered">
		<thead class="thead-dark">
			<tr>
				<th style="width:30px">No</th>
				<th style="width: 50px">Tank&nbsp;No.</th>
				<th style="width: 15px">Tank&nbsp;ID</th>
				<th class="text-left">Product Name</th>
				<th style="width: 200px">ATG</th>
				<th style="width: 90px">Segment</th>
				<th style="width: 15px;text-align:center">Height,&nbsp;mm</th>
				<th style="width:30px;text-align:center;"></th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
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
			<div class="modal-body" >
				<div class="row">
					<div class="col align-self my-modal">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!--Confirmation Alert Modal-->
<div class="modal fade" id="showMsgModal" tabindex="-1" role="dialog" aria-labelledby="showMsgModal" aria-hidden="true">
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
                                data-dismiss="modal">Yes</button>
                        <button type="button"
                                class="btn btn-danger primary-button"
                                data-dismiss="modal">No</button>
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
  <div class="modal-content bg-greenlobster" style="border-radius:10px" >
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
<div class="modal fade" id="tankSizeModal"  tabindex="-1" 
	role="dialog" aria-labelledby="staffNameLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered  mw- 75 w- 50" role="document">
            <div class="modal-content modal---inside bg-greenlobster" >
		<div class="modal-header" >
			<h3 class="modal-title text-white"  id="statusModalLabel">Tank</h3>
            	</div>
		<div class="modal-body">
	
		<ul style="padding: 0;margin: 0;">
		
			<li style="display:flex;align-items: center;
				justify-content: center;margin-bottom:5px">
				<span>Max Cpacity&nbsp;&nbsp;</span>
				<input class="form-control vehicle_text_input text-right"
					type="text" style="width: 30%;margin: 0px 5px 0 10px;"
					id="tank_max_capacity" />
				<span style="font-size:20px">&nbsp;&ell;</span>
			</li>

		</ul>

		</div>
                <!-- div class="modal-footer" style="border:0;"> 
                </div --->
            </div>
        </div>

    </div>
 
<div id="productResponse"></div>
<div id="showEditInventoryModal"></div>
<div id="showEditInputInventoryModal"></div>
<style>
.slim_cell {
	padding-top: 2px !important;
	padding-bottom: 2px !important;
}

#masterBackBar {
	position:relative;
	z-index:0;
	height: 30px;
}

#myProgress {
	position:absolute;
	z-index:1;
	width: 100%;
	height: 30px;
	background-color: #ddd;
	color: black;
	padding-top: 2px;
	text-align: right;
	padding-right:10px;
}

#myBar {
	position:absolute;
	z-index:2;
	width: 10%;
	opacity:50%;
	height: 30px;
	text-align: center;
}

@if (request()->path() == 'industry/show-tank_Mangement-view-franchise') 
.direct {
	display:none !important;
}
@else
.franchise {
	display:none !important;
}
@endif
/*
#myProgress {
  width: 100%;
  background-color: #ddd;
}

#myBar {
  width: 10%;
  height: 30px;
  background-color: #4CAF50;
  text-align: center;
  line-height: 30px;
  color: white;
}
*/
</style>
{!!$color_guide!!}
@section('js')
<script type="text/javascript">

var location_id = "";
var tankTable;

@if (request()->path() == 'industry/show-tank_Mangement-view-franchise') 
	var franchise_view = 'true';
@else
	var franchise_view = 'false';
@endif

tableData = {
	@if (!empty($location))
		locID: {{$location->id}},
	@else
		locID:"", 
	@endif
	franchise_view:franchise_view,
	@if (!empty(request()->t_id)) 
	t_id:{{request()->t_id}}, 
	@endif 
};


var tankTable = $('#tankMgt').DataTable({
	"processing": false,
	"bDestroy": true,
	"serverSide": true,
	 "bAutoWidth": false,
	"ajax": {
		"url": "{{route('industryoilgas.ajax.showTankManagement')}}",
		"type": "POST",
		data: function ( d ) {
			return  $.extend(d, tableData);
		},

		'headers': {
			'X-CSRF-TOKEN': '{{ csrf_token() }}'
		},
	},
	columns: [
		{data: 'DT_RowIndex', name: 'DT_RowIndex'},
		{data: 'tank_no', name: 'tank_no'},
		{data: 'tank_id', name: 'tank_id'},
		{data: 'product', name: 'product'},
		{data: 'atg', name: 'atg'},
		{data: 'segment', name: 'segment'},
		{data: 'height', name: 'height'},
		{data: 'deleted', name: 'deleted'},
	],
	"columnDefs": [
		{"className": "dt-center", "targets": [0, 1, 2, 4]},
	],
	"order": [0, 'desc'],
	'aoColumnDefs': [{
		'bSortable': false,
		'aTargets': [-1] /* 1st one, start by the right */
	}]
});
l_events();

$("#hrefBtn").attr("href_id","");

$('#addTank').on('click',function(){
	$('#tankSizeModal').modal('show');
});

$('#tankSizeModal').on('hidden.bs.modal', function (e) {
	location_id = $("#hrefBtn").attr("href_id");
	tank_size = $("#tank_max_capacity").val();
	addTank(location_id,tank_size);
	$("#tank_max_capacity").val('');
});

@if (!empty(request()->t_id)) 
setInterval(function () {
	if (location_id === '') {
		$("#addTank").css({"background-color": "#7c837e", "border-color": "#7c837e" , "cursor":"not-allowed"});
	
		$("#addTank").attr("disabled", true);

	} else {
		$("#addTank").css({"background-color": "#28a745", "border-color": "#28a745", "cursor":"pointer"});
		$("#addTank").attr("disabled", false);
	}
}, 1000);
@endif

var product = tankTable;
var prd = false;

@if (request()->path() == 'industry/show-tank_Mangement-view-franchise')
	segment_ =	'franchise';
@else
	segment_ = 'direct';
@endif

function addTank(location_id, tankSize) {

	if (prd == true) {
		return null
	}
	
	if (tankSize == '' || tankSize == 0) {
		return;
	}

	prd = true;
	$.ajax({
		url: "{{route('industryoilgas.storeTank')}}",
		type: "GET",
		enctype: 'multipart/form-data',
		data: { location_id:location_id,
			tankSize:tankSize, 
			@if (request()->path() == 'industry/show-tank_Mangement-view-franchise')
				@if (!empty(request()->t_id)) t_id:{{request()->t_id}} @endif 
			@endif
			},
		success: function (response) {
			tables_Reload_After_DateInsert(location_id);
			$("#showEditInventoryModal").html(response);

			prd = false;

		}, error: function (e) {
			console.log(e.message);
		}
	});
}


function commandDelete(systemid , location_id) {
	$.ajax({
		url: "{{route('industryoilgas.destroy.tank')}}",
		type: 'POST',
		'headers': {
			'X-CSRF-TOKEN': '{{ csrf_token() }}'
		},
		async: false,
		data: {
			"TankID": systemid
		},
		cache: false,
		success: function (response) {
			$('#showMsgModal').modal('hide');
			$("#productResponse").html(response);
			tables_Reload_After_DateInsert(location_id);
			delete_ = false;
			l_events();
		},

		error: function (e) {
			console.log('error', e);
		}
	});
}


var tankID=0;
var systemid=0;
function showogFuelHeightModel(tankID,systemid){
	this.tankID=tankID;
	this.systemid=systemid;
	jQuery('#inventoryCogsModal').modal('show');
}


function showLocModal(){
	$.ajax({
		url: "{{route('location.branch.get')}}",
		type: 'POST',
		'headers': {
			'X-CSRF-TOKEN': '{{ csrf_token() }}'
		},
		success: function (response) {
			let temp_id  = parseFloat(0);
			let text = "<h5 class='loc' id='' style='font-weight:normal;cursor: pointer;text-transform: capitalize;'>All Location</h5>";
			for (i = 0, len = response.length; i < len; i++) {

				if (response[i].warehouse == 1 || response[i].foodcourt == 1) {
					continue;
				}


				if (segment_ == 'direct') {
					if (response[i].direct != 1) {
						continue;
					}
				}

				if (segment_ == 'franchise') {
					if (response[i].franchise != 1 && response[i].franchisor != true) {
						continue;
					}
				}

				if (segment_ == 'food') {
					if (response[i].foodcourt != 1) {
						continue;
					}
				}

				text += "<h5 class='loc' id='"+response[i].id+
					"' fr='"+response[i].franchise+"' style='font-weight:normal;cursor: pointer;text-transform: capitalize;'>"+
					response[i].branch+
					"</h5>";
			}

			$('.my-modal').html(""+text);
			$("#locationModal").modal('show');
		},
		error: function (e) {
			console.log('error', e);
		}
	});
}


jQuery(document).ready(function($) {
	$('body').on('click', '.loc', function() {
		let this_anchor = $(this);

		location_id = $(this).attr("id");
		fr = $(this).attr('fr');
		//console.log("Location is Selected - "+location_id);
		//console.log(this_anchor.text());
		//console.log(this_anchor.attr("id"));
		document.getElementById("hrefBtn").innerHTML = '<h5>'+this_anchor.text()+'</h5>';
		document.getElementById("hrefBtn").setAttribute("href_id",this_anchor.attr("id"));

		showTank_AccordingLocation(location_id);
		
		
		@if (empty(request()->t_id))
			if (fr == 1) {
				location_id = '';
			}
		@endif

		// tankTable.ajax.reload();
		$("#locationModal").modal('hide');
		l_events();
	});
});


function showTank_AccordingLocation(location_ID) {
	tableData.locID = location_ID;
	tankTable.ajax.reload();
	l_events();
}



$('#inventoryCogsModal').on('hidden.bs.modal', function (e) {

	let inventoryCogs = $("#inventoryCogsInput").val();
	if (inventoryCogs && (inventoryCogs.trim().length) && (inventoryCogs > 0)) { // Check If value exist
		updateTankHeight();
	}
});


function updateTankHeight(){
	$("#tank_height_"+tankID).text(($("#inventoryCogsInput").val())*100);
	$.ajax({
		url: "{{route('industrytank.update.height')}}",
		type: 'POST',
		'headers': {
		  'X-CSRF-TOKEN': '{{ csrf_token() }}'
		},
		data: {
			"TankID": this.systemid,
			"ogTankHeight":$("#inventoryCogsInput").val()
		},
		success: function (response) {
			$("#productResponse").html(response);
			$('#modal').modal('show');
			//tankTable.ajax.reload();
			tables_Reload_After_DateInsert(location_id);
			l_events();
		},
		error: function (e) {
			console.log('error', e);
		}
	});
}


var prev_thumb_id = "";
function showProducts(tank_id){
//	$(this).parent().remove();
	$.ajax({
		url: "{{ route('industryoilgas.ajax.showTankProducts') }}",
		type:"GET",
		data: { length: 8,tank_id:tank_id,franchise_view:franchise_view,@if (!empty(request()->t_id)) t_id:{{request()->t_id}} @endif  },
		dataType:"JSON",
		success: function(response) {
			console.log('****** showProducts() success! *****');
			var productList = response.og_fuel;
			var procutsModal = $("#productsModal");
		   // var html = (productList.length) ? '' : 'No Products Available';
			$('#productList').html(response.output);
			var procutsModal = $("#productsModal");
			$('#productsModal').modal('show');
		},
		error: function(response) {
			console.log('****** showProducts() ERROR! *****');
			//console.log(JSON.stringify(response));
		},
	});
}


function add_product_pump(product_id,tank_id){
	$.ajax({
		url: "{{route('industryoilgas.ajax.saveTankProducts')}}",
		type: 'post',
		'headers': {
			'X-CSRF-TOKEN': '{{ csrf_token() }}'
		},
		data: {
			'product_id': product_id,
			'tank_id': tank_id,
		},
		success: function (response) {
			//console.log(response);
			$("#productsModal").modal('hide');
			$("#productResponse").html(response);
			$('#modal').modal('show');

			$.ajax({
				url: "{{route('industryoilgas.ajax.getTankProducts')}}",
				type: 'get',
				'headers': {
					'X-CSRF-TOKEN': '{{ csrf_token() }}'
				},
				data: {
					'tank_id': tank_id,
				},
				success: function (response){
					$("#tank_product_"+tank_id).text(response[0]);
					
					$("#tank_product_"+tank_id).parent().find("img:first-child").remove();
					$("<img id='img-product-thumb-' src='/images/product/"+response[1]+"/thumb/"+response[2]+"' data-field='og_product_name' style=' width: 25px;height: 25px;display: inline-block;margin-right: 8px;float:left;object-fit:contain;'/>").insertBefore("#tank_product_"+tank_id);
					
					tables_Reload_After_DateInsert(location_id)
				}
			});
		},
		error: function (response) {
			$("#productResponse").html(response);
			$('#modal').modal('show');
		}
	});
}


var tank_i=0;
function storeTankID(tank_id,tank_no){
	jQuery('#ogTankNoModal').modal('show');
	document.getElementById("ogTankNoInput").value = $("#tank_no_"+tank_id).text();
	this.tank_id=tank_id;
}


function validate(evt) {
	var theEvent = evt || window.event;

	// Handle paste
	if (theEvent.type === 'paste') {
		key = event.clipboardData.getData('text/plain');
	} else {
		// Handle key press
		var key = theEvent.keyCode || theEvent.which;
		key = String.fromCharCode(key);
	}
	var regex = /[0-9]|\./;
	if( !regex.test(key) ) {
		theEvent.returnValue = false;
		if(theEvent.preventDefault) theEvent.preventDefault();
	}
}

$('#ogTankNoModal').on('hidden.bs.modal', function (e) {
	let ogTankNo = $("#ogTankNoInput").val();
	if (ogTankNo && (ogTankNo.trim().length) && (ogTankNo > 0)) { // Check If value exist
		updateTankNo();
	}
});


function updateTankNo(){
	$.ajax({
		url: "{{route('industryoilgas.ajax.saveTankNo')}}",
		type: 'post',
		'headers': {
			'X-CSRF-TOKEN': '{{ csrf_token() }}'
		},
		data: {
			'tank_id': tank_id,
			'tank_no' : $("#ogTankNoInput").val()
		},
		success: function (response){
			//console.log(response);
			$("#productResponse").html(response);
			$('#modal').modal('show');
			$("#tank_no_"+tank_id).text($("#ogTankNoInput").val());

		},
		error: function (response) {
			$("#productResponse").html(response);
			$('#modal').modal('show');
		}
	});
}


function tables_Reload_After_DateInsert(locID){
	tableData.locID = locID;
	tankTable.ajax.reload();
	l_events();
}

	
function segment(type, target) {
	disable_loc = false;

	$(".loc_list_item").attr('disabled');
	$(".loc_list_item").addClass('disabled');

	if (type == 'direct') {
		$("#segment_modal").html('Direct<br/>Segment');
		$(".direct_loc").removeAttr('disabled');
		$(".direct_loc").removeClass('disabled');
	} else if (type == 'franchise') {
		$("#segment_modal").html('Franchise<br/>Segment');
		$(".franchise_loc").removeAttr('disabled');
		$(".franchise_loc").removeClass('disabled');
	} else if (type == 'food') {
		$("#segment_modal").html('Food Court<br/>Segment');
		$(".foodcourt_loc").removeAttr('disabled');
		$(".foodcourt_loc").removeClass('disabled');
	} else {
		$("#segment_modal").html('All<br/>Segment');
		$(".loc_list_item").removeAttr('disabled');
		$(".loc_list_item").removeClass('disabled');
	}

	$(".segment_link > h5").removeClass("active");
	$(target).addClass("active");

	$("#segment_modal").val(type);
	document.getElementById("hrefBtn").innerHTML = '<h5>All Location</h5>';
	document.getElementById("hrefBtn").setAttribute("href_id",null);
	showTank_AccordingLocation()
}

function franchise_popup(fsor,idfsor, fse, idfse, fsee, idfsee) {
	$('#mfsor').html(fsor);
	$('#mfse').html(fse);
	$('#mfsee').html(fsee);

	$('#midfsor').html(idfsor);
	$('#midfse').html(idfse);
	$('#midfsee').html(idfsee);

	$("#segmentModal").modal('show');	
}


@if (!empty($location))
//	showTank_AccordingLocation({{$location->id}})
	location_id = {{$location->id}};
	$("#hrefBtn").attr("href_id", location_id);
	@endif

function l_events() {	
$('table').on('click', '.remove', function (e) {
	e.preventDefault ? e.preventDefault() : e.returnValue = false;
	systemid = $(this).attr('id');
	$('#showMsgModal').modal('show');
});

$('table').on('click','#tank_height_'.tankID,function(e){
	e.preventDefault();
	let inventoryCog= $("#tank_height_"+tankID).text();
		inventoryCog=inventoryCog.replace(/,/g,'');
		inventoryCog = Number(inventoryCog);

	$('#inventoryCogsInput').val(inventoryCog);

});

}
	$('#btnConfirm').on('click', function (e) {
		e.preventDefault();
		location_id = $("#hrefBtn").attr("href_id");
		commandDelete(systemid , location_id);
	});

	setInterval( function() {
		if ($('body').hasClass('modal-open') == false) {
			$(".modal-backdrop").remove();
		}	
	}, 2500);


</script>
@endsection
@endsection
