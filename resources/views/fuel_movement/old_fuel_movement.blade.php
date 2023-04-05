@extends('common.web')

@section('styles')

<style>
/* remove small icons from input number */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
-webkit-appearance: none;
margin: 0;
}
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_processing,
.dataTables_wrapper .dataTables_paginate{
    color: black !important;
    font-weight: normal !important;
}

/* Firefox */
input[type=number] {
	-moz-appearance:textfield;
}

.month_table > tr > th {
	font-size: 22px;
	color: white;
	background-color: rgba(255, 255, 255, 0.5);
}

.month_table  > tr > td {
	color: #fff;
	font-weight: 600;
	border: unset;
	font-size: 20px;
	cursor: pointer;
}


.date_table > tbody > tr > th {
	font-size: 22px;
	color: white;
	background-color: rgba(255, 255, 255, 0.5);
}

.date_table > tbody > tr > td {
	color: #fff;
	font-weight: 600;
	border: unset;
	font-size: 20px;
	cursor: pointer;
}

table.dataTable tbody td{
	border-left: 1px solid #dee2e6;
	border-right: 1px solid #dee2e6;
	border-top: none;
	/*border-bottom: none;*/
}

.btn-green {
	background-color: green !important;
	color: #fff !important;
	box-shadow: none !important;
	border: 0px !important;
}

.btn-green:focus {
	background-color: green !important;
	color: #fff !important;
	box-shadow: none !important;
	border: 0px !important;
}

.bg-blue {
	background-color: #007bff;
	color: #fff;
}

.date_table1 > tbody > tr > th ,
{
	font-size: 22px;
	color: white;
	background-color: rgba(255, 255, 255, 0.5);
}

.date_table1 > tbody > tr > td {
	color: #fff;
	font-weight: 600;
	border: unset;
	font-size: 20px;
	cursor: pointer;
}

.selected_date {
	color: #fff !important;
	background: #008000;
	font-weight: 600 !important;
}

.selected_date1 {
	color: #008000 !important;
	font-weight: 700 !important;
}

#Datepick .d-table {
	display: -webkit-flex !important;
	display: -ms-flexbox !important;
	display: flex !important;
}

.dataTables_filter input {
	width: 300px;
}

.greenshade {
	height: 30px;
	background-color: green; /* For browsers that do not support gradients */
	background-image: linear-gradient(-90deg, green, white); /* Standard syntax (must be last) */
}
.dt-button{
	display: none;
}

/* .bg-purplelobster{
	background-color: rgba(26, 188, 156, 0.7);
	border-color: rgba(26, 188, 156, 0.7);
} */

/*//for calender short day*/
.shortDay ul{
	llist-style: none;
	background-color: rgba(255, 255, 255, 0.5);
	position: relative;
	left: -75px;
	width: 124%;
	height: 55px;
	line-height: 42px;
 }

.shortDay ul > li{
	font-size: 22px;
	color: white;
	font-weight: 700 !important;
	/* background-color: #2b1f1f; */
	padding: 5px 24px;
	text-align: left !important;
}
.list-inline-item:not(:last-child){
	margin-right: 0 !important;
}
.modal-content{
	overflow: hidden;
}
.modal-inside .row {
	margin: 0px;
	color: #fff;
	margin-top: 15px;
	padding: 0px !important;
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

.modalBtns {
	margin-top: 5px
}
</style>
 <style>
	.butns{
		display: none
	}
</style>
@endsection
@include('common.header')
@include('common.menubuttons')
@section('content')
<div class="container-fluid">

    <div class="d-flex mt-0" style="width: 100%;">
        <div class="col-sm-3 align-self-center" style="padding:0px">
            <h2 class="mb-0">Fuel Movement</h2>
        </div>

        <div class="col-sm-1 d-flex align-self-center" id="fuelThumbnail" >
		</div>

        <div class="col-sm-2 d-flex"
			style="align-self:center;float:left;padding:0px;" >
            <a href="#" style="text-decoration: none;" id="selectFuelModal_btn"
				href_fuel_prod_name=""  href_fuel_prod_id="">
                <h4 style="margin-bottom:0px;padding-top:0;line-height:1.5;">
					Select Fuel
				</h4>
            </a>
        </div>

        <div class="col-sm-1" style="align-self:center;float:left;padding:0px;">
            <input type="hidden" id='startDate'>
            <h4 style="margin-bottom:0px;padding-top:0;line-height:1.5;">
                <a href="#" style="text-decoration: none; padding-top:10px;"
					onclick="show_month_modal()"
                id="month_from" name="froms">Month</a>
            </h4>
        </div>

        <div class="col-md-3" style="align-self:center;padding:0px;
			text-align:right;left:60px;z-index:100">

                <h5 style="margin-bottom:0">{{$location->name??''}} </h5>

        </div>

        <div class="col-sm-2 ">
            <div class="row mb-0" style="float:right;">
				<button onclick="window.open('{{ route("fuel.stockIn") }}')" class="btn btn-success bg-stockin sellerbutton m-0"
					style="padding:0px;float:right;border-radius:10px;"
					id="stockin_btn">Stock<br>In
				</button>
				<button onclick="window.open('{{ route("fuel.stockOut") }}')" class="btn btn-success bg-stockout sellerbutton
					mb-0 mr-0"
					style="padding:0px;float:right;margin-left:5px;
						border-radius:10px"
					id="stockout_btn">Stock<br>Out
				</button>
            </div>
        </div>
    </div>

    <div style="padding-left:0;padding-right:0; margin-top:5px;" class="col-sm-12">
        <table id="fuelMgt" class="table table-bordered">
            <thead class="thead-dark">
            <tr>
                <th style="width:10px">No</th>
                <th style="width:140px">Date</th>
                <th style="width:100px">C/Forward</th>
                <th style="width:100px">Sales&nbsp;(&ell;)</th>
                <th style="width: 100px">Receipt</th>
                <th style="width: 100px">Book</th>
                <th style="width: 100px">Tank Dip</th>
                <th style="width: 100px">Daily&nbsp;Variance</th>
                <th style="width: 150px">Cumulative</th>
                <th style="width: 50px">%</th>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center">1</td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center">
                        <a href="#" data-toggle="modal" data-target="#c_ForwardEditModal-" style="text-decoration: none;" onclick="">0.00
                        </a></td>
                    <td style="text-align: center"><a href="#" style="text-decoration: none;" onclick="window.open('{{ route("fuel_movement.showproductledgerSale") }}')">0.00
                    </a></td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center">
                        <a href="#" data-toggle="modal" data-target="#tank_dipEditModal-" style="text-decoration: none;" onclick="">0.00
                    </a></td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center"></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<!-- Start Edit Modal -->
<div class="modal fade" id="c_ForwardEditModal-"
    tabindex="-1" role="dialog" style="padding-right:0 !important"
    aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-75 w-25" role="document">
        <div class="modal-content bg-purplelobster">

            <div class="modal-body pb-0 text-center">
            <form method="post" action="#!"
                id="c_ForwardEditModal-">
                @csrf
                <input type="hidden" name="user_id"
                    value=""
                    id="userEditModalInput0-">
                <input type="hidden" name="chegeshappen"
                    value="0" id="chegeshappen-">
                <div class="mb-1 form-group">
                    <input type="number" style="margin-bottom:2px;text-align:right;"
                        id="c_ForwardEditModal-"
                        class="form-control chagehappen-"
                        name="fullname" placeholder="0.00"
                        value="" />
                </div>
            </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="tank_dipEditModal-"
    tabindex="-1" role="dialog" style="padding-right:0 !important"
    aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-75 w-25" role="document">
        <div class="modal-content bg-purplelobster">

            <div class="modal-body pb-0 text-center">
            <form method="post" action="#!"
                id="tank_dipEditModal-">
                @csrf
                <input type="hidden" name="user_id"
                    value=""
                    id="userEditModalInput0-">
                <input type="hidden" name="chegeshappen"
                    value="0" id="chegeshappen-">
                <div class="mb-1 form-group">
                    <input type="number" style="margin-bottom:2px;text-align:right;"
                        id="tank_dipEditModal-"
                        class="form-control chagehappen-"
                        name="fullname" placeholder="0.00"
                        value="" />
                </div>
            </form>
            </div>
        </div>
    </div>
</div>
    <!-- Start Location Modal -->
    <div id="locationModal" class="modal fade" tabindex="-1" role="dialog"
		aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md"
			role="document">
            <div class="modal-content bg-greenmidlobster"
				style="max-height: 100%;height: 100%;">
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


    <div class="modal fade" id="start" tabindex="-1" role="dialog"
		aria-labelledby="Member" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-75 w-50"
			role="document">
		<div class="modal-content modal-inside bg-purplelobster">
			<div class="modal-body">
			<div class="row" style="padding-bottom: 5px;padding-top: 5px;">
			<h5 style="text-align:center; line-height:1.5; font-weight:500px;">
			In order to start fuel management tracking,
			please fill in the carry forward tank value</h5>
			<div class="col-md-12">
				<div style="text-align:center">
				<input type="number" id="modal_Fuel_Prod_ID"
				class="pl-1" value="00.00"
				placeholder="0.00" step="0.00"
				style="margin-left: 43px; margin-right: 10px;
				width: 53%; padding: 0.375rem 0.75rem;
				height: calc(1.5em + 0.75rem + 2px);
				border: 1px solid #ced4da; border-radius: 0.25rem;
				text-align: right !important;">
				<label for="1">Litre</label>
				<span id="validation-error"
				style="display: block;color: #dc3545;font-weight: 600;">
				</span>
				</div>

				<div style="text-align: center">
					<button class="btn btn-primary modalBtns"
						id="start_confirm_btn" style="width: 100px">
						Confirm</button>
					<button class="btn btn-danger modalBtns"
						style="width: 100px" data-dismiss="modal">
						Cancel</button>
				</div>
			</div>
			</div>
			</div>
		</div>
        </div>
    </div>

    <!-- Popup for select product start -->
    <div id="productsModal" class="modal fade" tabindex="-1" role="dialog"
         aria-hidden="true">
        <div class="modal-dialog modal modal-dialog-centered"
             style="max-width:650px;margin: auto;">
            <div class="modal-content bg-purplelobster">
                <div class="modal-header">
                    <h3 style="margin-bottom:0">Select Fuel</h3>
                </div>
                <div class="modal-body" style="">
                    <div class="row" style="width:100%">
                        <div class="col-md-12" style="">
                            <div id="productList" class="creditmodelDV"
                                 style="display:flex; flex-wrap: wrap; justify-content: flex-start;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Popup for select product end -->


    <!-- Error Modal -->
    <div class="modal fade" id="error_modal" tabindex="-1"
		role="dialog" aria-labelledby="Member" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-75 w-50" role="document">
            <div class="modal-content modal-inside bg-purplelobster">
                <div class="modal-header">
                </div>
                <div class="modal-body" id="errorModal_body">

                    <br>
                </div>
            </div>
        </div>
    </div>

    <div id="productResponce"></div>
    <div id="showEditInventoryModal"></div>
    <div id="showEditInputInventoryModal"></div>
    <div id="response_dip_custom"></div>
    <script>
        $('#fuelMgt').DataTable();
        </script>
@endsection
@section('script')
    <script type="text/javascript">

        function select_month(month){
            var currYear=$("#main_year").find("h3").text();
            $('#month_from').html(month +' ' + currYear);
        }

        var location_id = "";
        var fuel_prod_id = "";

        var FuelTable = $('#fuelMgts').DataTable({
            "processing": false,
            "bDestroy": true,
            "serverSide": true,
            "bAutoWidth": false,
            "ajax": {
                "url": "{{route('fuel_movement.mainDatatable')}}",
                "type": "post",
                "data": {locID:"", min_date:$('#startDate').attr('min') },
                'headers': {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'date', name: 'date'},
                {data: 'cforward', name: 'cforward'},
                {data: 'sales', name: 'sales'},
                {data: 'receipt', name: 'receipt'},
                {data: 'book', name: 'book'},
                {data: 'tank_dip', name: 'tank_dip'},
                {data: 'daily_variance', name: 'daily_variance'},
                {data: 'cumulative', name: 'cumulative'},
                {data: 'percentage', name: 'percentage'},
            ],
            "columnDefs": [
                {"className": "dt-center", "targets": [7,8]},
            ],
            "order": [0, 'desc'],
            'aoColumnDefs': [{
                'bSortable': false,
                'aTargets': [-1] /* 1st one, start by the right */
            }]
        });
        FuelTable.ajax.reload();

        $("#hrefBtn").attr("href_id", "");
        location_id     = $("#hrefBtn").attr("href_id");
        fuel_prod_id    = $("#selectFuelModal_btn").attr("href_fuel_prod_id");

        //When Start button click
        $("#startModal_btn").on('click' , function(){
           $("#start").modal("show");
           $("#modal_location_ID").val(  $("#selectFuelModal_btn").attr("href_fuel_prod_id") );
           //$("#modal_Fuel_Prod_ID").val( $("#hrefBtn").attr("href_id") );
           $("#modal_Fuel_Prod_ID").val('');
           //console.log($("#hrefBtn").attr("href_id"));
           //console.log($("#selectFuelModal_btn").attr("href_fuel_prod_id"));
        });

        //When Select fuel button click
        $("#selectFuelModal_btn").on('click' , function(){
            var location_id = $("#hrefBtn").attr("href_id");
            $("#productsModal").modal('show');
            showOgFuelQualifiedProducts(location_id);
        });

        //When Confirm button click
        $("#start_confirm_btn").on('click' ,function () {
            var locID      = $("#hrefBtn").attr("href_id");
            var fuelprodID = $("#selectFuelModal_btn").attr("href_fuel_prod_id");
            var cForward   = $("#modal_Fuel_Prod_ID").val();
            console.log(locID);
            console.log(fuelprodID);

            if(cForward === '' || cForward === '0.00'){
                $("#validation-error").text("");
                $("#validation-error").text("Liter field is required");
                return false;
            }else{
                $("#validation-error").text("");
                console.log(cForward);
                addFuel(locID , fuelprodID , cForward);
                showFuel_Data_AccordingLocation(locID,fuelprodID);
            }
        });

        $("#start_confirm_dipping_btn").on('click' ,function () {
            var dip_tank   = $("#dippingInput").val();
            var cForward   = $("#fuel_cforward").text();
            addFuel(location_id , fuel_prod_id , cForward, dip_tank);
            showFuel_Data_AccordingLocation(location_id,fuel_prod_id);
        })


		var prd = false;
        function addFuel(location_id , fuel_prod_id , cForward,tankDip = 0) {
            if (prd == true) { return null; }

            prd = true;
            $.ajax({
                url: "'industryoilgas.storeFuelMovement'",
                type: "GET",
                enctype: 'multipart/form-data',
                // processData: false,
                // contentType: false,
                // cache: false,
                data: {
					location_id: location_id,
					fuel_prod_id: fuel_prod_id,
					cforward: cForward,
					tank_dip: tankDip
				},
                success: function (response) {
                    $("#start").modal('hide');
                    $("#dipping").modal('hide');
                    $("#showEditInventoryModal").html(response);

                    tables_Reload_After_DateInsert(location_id);
                    isFuelMovementExist(fuel_prod_id , location_id );
                    prd = false;
                }, error: function (e) {
                    console.log(e.message);
                }
            });
        }


        function tables_Reload_After_DateInsert(locID){

            var FuelTable = $('#fuelMgt').DataTable({
                "processing": false,
                "bDestroy": true,
                "serverSide": true,
                "bAutoWidth": false,
                "ajax": {
                    "url": "{{route('fuel_movement.mainDatatable')}}",
                    "type": "post",
                    "data": {locID:locID,min_date:$('#startDate').attr('min')},
                    'headers': {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'date', name: 'date'},
                    {data: 'cforward', name: 'cforward'},
                    {data: 'sales', name: 'sales'},
                    {data: 'receipt', name: 'receipt'},
                    {data: 'book', name: 'book'},
                    {data: 'tank_dip', name: 'tank_dip'},
                    {data: 'daily_variance', name: 'daily_variance'},
                    {data: 'cumulative', name: 'cumulative'},
                    {data: 'percentage', name: 'percentage'},
                ],
                // "columnDefs": [
                //     {"className": "dt-center", "targets": [7,8]},
                // ],
                "order": [0, 'desc'],
                'aoColumnDefs': [{
                    'bSortable': false,
                    'aTargets': [-1] /* 1st one, start by the right */
                }]
            });
            FuelTable.ajax.reload();

        }


        function showOgFuelQualifiedProducts(location_id){
            $.ajax({
                url: "{{route('fuel_movement.showOgFuelQualifiedProducts')}}", // industryoilgas.ajax.showOgFuelQualifiedProducts
                type:"GET",
                dataType:"JSON",
                success: function(response) {
                    // console.log('****** showProducts() success! *****');
                    console.log(response);
                    var productList = response.data;
                    var procutsModal = $("#productsModal");
                    var html = (productList.length) ? '' : 'No Products Available';

                    $(procutsModal).find('#productList').html(response.output);
                    var procutsModal = $("#productsModal");
                    $(procutsModal).modal('show')
                },
                error: function(response) {
                    // console.log('****** showProducts() ERROR! *****');
                    //console.log(JSON.stringify(response));
                },
            });
        }


        function showLocModal() {

	      if (fuel_prod_id == '') {
	      	return null;
	      }
              $.ajax({
                url: "'industryoilgas.customLocation'",
                type: 'POST',
                'headers': {
			'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                "data": {"loc_for_product":fuel_prod_id},
                success: function (response) {
                    //console.log(response);

                    let temp_id = parseFloat(0);
                    let text = "";

                    for (i = 0, len = response.length; i < len; i++) {
                        // text += href_s + response[i].branch + href_e + "<br>";
	 		if (response[i].warehouse == 1 || response[i].foodcourt == 1) {
				 continue;
			 }
                        text += "<h5 class='loc' id='" + response[i].id + "' style='font-weight:normal;cursor: pointer;text-transform: capitalize;'>" + response[i].branch + "</h5>";
                    }
                    $('.my-modal').html("" + text);
                    $("#locationModal").modal('show');
                },
                error: function (e) {
                    console.log('error', e);
                }
            });
        }

        deactivate_dipping_button();
        deactivate_start_button();

        function activate_start_button(){
            $("#startModal_btn").css({"background-color": "#982450", "border-color": "#982450", "cursor":"pointer"});
            $("#startModal_btn").attr("disabled", false);
        }
        function activate_dipping_button(){
            $("#dipping_btn").css({"background-color": "#982450", "border-color": "#982450", "cursor":"pointer"});
            $("#dipping_btn").attr("disabled", false);
        }

        function deactivate_dipping_button(){
            $("#dipping_btn").attr("disabled", true);
            $("#dipping_btn").css({"background-color": "#7c837e", "border-color": "#7c837e" , "cursor":"not-allowed"});
        }
        function deactivate_start_button(){
            $("#startModal_btn").attr("disabled", true);
            $("#startModal_btn").css({"background-color": "#7c837e", "border-color": "#7c837e" , "cursor":"not-allowed"});
        }

        $('body').on('click', '.loc', function () {
            let this_anchor = $(this);

            location_id = $(this).attr("id");
            $("#locationModal").modal('hide');
            $("#hrefBtn").html('<h5 style="margin-bottom:0">'+this_anchor.text()+'</h5>');
            $("#hrefBtn").attr("href_id",location_id);

            showFuel_Data_AccordingLocation(location_id,fuel_prod_id);

            isFuelMovementExist(fuel_prod_id , location_id );
        });

        $('body').on('click' , '.sellerbuttonwide' , function(){
            let this_anchor_fuel = $(this);

            fuel_prod_id     = this_anchor_fuel.attr("href_fuel_prod_id");
            var fuel_prod_name   = this_anchor_fuel.attr("href_fuel_prod_name");
            var fuel_prod_thumbnail   = this_anchor_fuel.attr("href_fuel_prod_thumbnail");
            var fuel_prod_systemid   = this_anchor_fuel.attr("href_fuel_prod_systemid");
            if (typeof fuel_prod_thumbnail !== typeof undefined && fuel_prod_systemid !== false) {
                var imagePath="/images/product/" + fuel_prod_systemid + '/thumb/' + fuel_prod_thumbnail;
                var thumbnailHtml="<img class='thumbnail align-self-center' href_fuel_thumbnail='' width='70px' height='70px' style='object-fit:contain;float:right;margin-left:0;margin-top:0;' src='"+imagePath+"'>";
                $("#fuelThumbnail").html(thumbnailHtml);
            }
            $("#selectFuelModal_btn").html('<h4 style="margin-bottom:0">'+fuel_prod_name+'</h4><p style="font-size:18px;margin-bottom:0;">'+fuel_prod_systemid+'</p>');
            $("#selectFuelModal_btn").attr("thumbnail",fuel_prod_thumbnail);
            $("#selectFuelModal_btn").attr("href_fuel_prod_name",fuel_prod_name);
            $("#selectFuelModal_btn").attr("href_fuel_prod_id",fuel_prod_id);

	    $("#hrefBtn").removeClass("disabled");

	    $("#productsModal").modal('hide');
            /*
            if (fuel_prod_id === '' || location_id === '') {
                deactivate_dipping_button();
                deactivate_start_button();
            } else {
                activate_start_button();
            }*/
            showFuel_Data_AccordingLocation(location_id,fuel_prod_id);
            isFuelMovementExist(fuel_prod_id , location_id );

        });

	    $("#hrefBtn").addClass("disabled");
        function isFuelMovementExist(ogfuel_id , location_id){
            if (fuel_prod_id === '' || location_id === '') {
                deactivate_dipping_button();
                deactivate_start_button();
            } else {
                $.ajax({
                    url: "'industryoilgas.checkFuelMovement'",
                    data : {ogfuel_id: ogfuel_id , location_id : location_id},
                    type: 'GET',
                    'headers': {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        var data = jQuery.parseJSON(response);
                        if(data.start) {
                            activate_start_button();
                        } else {
                            deactivate_start_button();
                        }
                        if(data.dipping) {
                            activate_dipping_button();
                        } else {
                            deactivate_dipping_button();
                        }
                    },
                    error: function (e) {
                        console.log('error', e);
                    }
                });
            }
            return false;
        }

        function showFuel_Data_AccordingLocation(location_ID,ogfuel_id) {

            var FuelTable = $('#fuelMgt').DataTable({
                "processing": false,
                "bDestroy": true,
                "serverSide": true,
                "bAutoWidth": false,
                "ajax": {
                    "url": "{{route('fuel_movement.mainDatatable')}}",
                    "type": "post",
                    "data": {locID:location_ID, ogfuel_id:ogfuel_id, min_date:$('#startDate').attr('min') },
                    'headers': {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'date', name: 'date'},
                    {data: 'cforward', name: 'cforward'},
                    {data: 'sales', name: 'sales'},
                    {data: 'receipt', name: 'receipt'},
                    {data: 'book', name: 'book'},
                    {data: 'tank_dip', name: 'tank_dip'},
                    {data: 'daily_variance', name: 'daily_variance'},
                    {data: 'cumulative', name: 'cumulative'},
                    {data: 'percentage', name: 'percentage'},
                ],
                // "columnDefs": [
                //     {"className": "dt-center", "targets": [7,8]},
                // ],
                "order": [0, 'desc'],
                'aoColumnDefs': [{
                    'bSortable': false,
                    'aTargets': [-1] /* 1st one, start by the right */
                }]
            });
            FuelTable.ajax.reload();
        }


        function atm_money(num) {
            if(num == 0){
                return '0.00';
            }
            if (num.toString().length == 1) {
                return '00.0' + num.toString()

            } else if (num.toString().length == 2) {
                return '00.' + num.toString()

            } else if (num.toString().length == 3) {
                return '0' + num.toString()[0] +'.'+ num.toString()[1] +
                    num.toString()[2];

            } else if (num.toString().length >= 4) {
                return num.toString().slice(0,(num.toString().length - 2)) +
                    '.'+ num.toString()[(num.toString().length - 2)] +
                    num.toString()[(num.toString().length - 1)];
            }
        }



        $("#inventoryCogsInput").on( "keydown", function( event ) {
            event.preventDefault()

            if (event.keyCode == 8) {
                $("#buffer_main_price").val('');
                $("#inventoryCogsInput").val('');
                return null
            }
            if (isNaN(event.key) || $.inArray( event.keyCode, [13,38,40,37,39] ) !== -1 || event.keyCode == 13  ) {
                if ($("#buffer_main_price").val() != '') {
                    $("#inventoryCogsInput").val(atm_money(parseInt($("#buffer_main_price").val())))
                } else {
                    $("#inventoryCogsInput").val('')
                }
                return null;
            }
            const input =  event.key;
            old_val = $("#buffer_main_price").val();

            if (old_val === '0.00') {
                $("#buffer_main_price").val('');
                $("#inventoryCogsInput").val('');
                old_val = '';
            }
            $("#buffer_main_price").val(''+old_val+input);
            $("#inventoryCogsInput").val(atm_money(parseInt($("#buffer_main_price").val())))
        });
//////////////////////////////////////////////////////////////////
    function show_month_modal() {
        jQuery('#showDateModalFrom').modal('show');
    }

    $('#showMonthModalFrom').on('hidden.bs.modal', function (e) {
        //
    });

    var dateToday=new Date();
    var dateAccountCreated='{{$date->month??""}} {{$date->year??""}}'; //format = MONTH(space)Year
    var currentYear=dateToday.getFullYear();
    var yearSelected=currentYear;
    var monthArray = 'Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec'.split(' ');

    $(document).ready(function () {
        $('#month_from').html(monthArray[dateToday.getMonth()] +' ' + currentYear);
        // Bind Events
        $('.prev-year').click(function () {
            yearNavigationHandler(-1);
        });
        $('.next-year').click(function () {
            yearNavigationHandler(1);
        });
        $(".year").html("<h3>"+currentYear+"</h3>");
        displayCalendar();
        shoot_event_for_month();
        //
    });

    function yearNavigationHandler(dir)
    {
        yearSelected=yearSelected+dir;
        $(".year").html("<h3>"+yearSelected+"</h3>");
        displayCalendar()
        shoot_event_for_month();
    }

    function displayCalendar()
    {
        clearMonths();
        var table=$("table.month_table");
        var monthNo=0;

        for(var i=0;i<3;i++)
        {
            var row=$("<tr/>");
            for(var j=0;j<4;j++)
            {
                var td=$("<td/>");
                $(td).html(monthArray[monthNo]);
                addClassToMonth(td,monthNo,yearSelected);
                monthNo++;
                $(row).append(td);
            }
            $(table).append(row);
        }
        shoot_event_for_month();
    }

    function addClassToMonth(td,month,year)
    {
        if(year===currentYear)
        {
            if(month>dateToday.getMonth())
            {
                $(td).css("cursor","not-allowed");
                $(td).addClass("disabled");
            }
        }
        else if(year>currentYear)
        {
            $(td).css("cursor","not-allowed");
            $(td).addClass("disabled");
        }

        if(year==dateAccountCreated.split(' ')[1]) //year = account creation year
        {
            if(month<dateAccountCreated.split(' ')[0]-1) //month <  account creation month
            {
                $(td).css("cursor","not-allowed");
                $(td).addClass("disabled");
            }
        }
        else if(year<dateAccountCreated.split(' ')[1]) //year < account creation year
        {
            $(td).css("cursor","not-allowed");
            $(td).addClass("disabled");
        }
    }

    function clearMonths()
    {
        var table=$("table.month_table");
        $(table).empty();
    }

    function shoot_event_for_month() {

        $('.month_table > tr > td').click(function (e) {
            console.log("Month clicked To");

            var target = e.target;
            if ($(target).hasClass('disabled')) {
                return false;
            } else {
                $('.month_table > tr > td').removeClass('selected_date1');
                $(target).addClass('selected_date1');
            }

            let month1 = $(target).html();
            let year1 = $('.year > h3').html();
            $('#startDate').attr('min',year1 + '-' + (monthArray.indexOf(month1)+1));
            $('#month_from').html($(target).html() +' ' + yearSelected);

			showFuel_Data_AccordingLocation(location_id,fuel_prod_id)

            jQuery('#showMonthModalFrom').modal('hide');
        });
    }


//////////////////////////////////////////////////////////////////
        $(function () {
            $(document).ready(function () {
                var todaysDate = new Date(); // Gets today's date
                // Max date attribute is in "YYYY-MM-DD".
                // Need to format today's date accordingly
                var year = todaysDate.getFullYear(); // YYYY
                var month = todaysDate.getMonth();  // MM
                var minDate = (year + "-" + (month+1));
                //  +"-"+ display Results in "YYYY-MM" for today's date
                // Now to set the max date value for the calendar to be today's date
                $('#startDate').attr('min', minDate);
            });
        });

    @yield('current_year')


/////////////////////////////////////////////////////////////////


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
            if (!regex.test(key)) {
                theEvent.returnValue = false;
                if (theEvent.preventDefault) theEvent.preventDefault();
            }
        }

        $('body').on('click', '.fuel_receipt', function() {
            var url = $(this).attr('url');
            var day = $(this).attr('day');

            window.open(url+'?&day='+day, '_blank');
        });

		////////////////////////////////////////////

		$("#dipping_btn").on('click', () => {
			$.post("'Fuel-Management.nodal'", {
				fuel_id: fuel_prod_id,
				location_id:location_id
			}).done(function(res) {
				$("#response_dip_custom").html(res);
				//$('#dippingModal').off();
				 $('#dippingModal').on('hidden.bs.modal', function (e){
					showFuel_Data_AccordingLocation(location_id,fuel_prod_id);
				 });
			});
		});


    </script>

    @include('fuel_movement.month_picker')

@endsection
@include('common.footer')

