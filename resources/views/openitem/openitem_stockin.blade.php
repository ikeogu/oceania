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

.active_button:hover, .active_button:active, .active_button_activated {
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

tr {
	height: 40px
}

.num_td{
	text-align: left;
}
.value-button {
	display: inline-block;
	font-size: 24px;
	line-height: 21px;
	text-align: center;
	vertical-align: middle;
	background: #fff;
	-webkit-touch-callout: none;
	-webkit-user-select: none;
	-khtml-user-select: none;
	-moz-user-select: none;
	-ms-user-select: none;
	user-select: none;
	text-align: center;
	text-align: center;
}
input.number {
	text-align: center;
	border: none;
	border: 1px solid #e2dddd;
	margin: 0px;
	width: 90px;
	border-radius: 5px;
	height: 38px;
	border-radius: 5px;
	background-color: #d4d3d36b !important;
	vertical-align: text-bottom;
}
.value-button {
	cursor:pointer;
}
</style>
@endsection

@include('common.menubuttons')

@section('content')
<div class="container-fluid">
	<div class="d-flex mt-0 p-0"
		 style="width:100%; margin-top:5px !important;margin-bottom:5px !important">
		<div style="padding:0" class="align-self-center col-sm-8">
			<h2 class="mb-0">Open Item: Stock In</h2>
		</div>

		<div class="col-sm-4 pl-0">
			<div class="row mb-0 align-items-center" style="float:right;">
				<div class="col-auto mt-0" style="">
					<h5 class="mb-0">{{$location->name}}</h5>
				</div>
				<button onclick="update_quantity()"
					class="btn sellerbutton btn-success bg-confirm-button mr-0"
					style="padding:0px;float:right;margin-left:5px;background:gray;
					border-radius:10px;margin-bottom:0 !important; cursor: not-allowed;"
					id="confirm_update">Confirm
				</button>
			</div>
		</div>
	</div>

	<div class="col-sm-12 pl-0 pr-0" style="">
		<table id="tableFLocaltionProduct" class="table table-bordered">
			<thead class="thead-dark">
			<tr style="">
				<th class="text-center" style="text-align: center;">No</th>
				<th class="text-center" style="text-align: center;">Product ID</th>
				<th class="" style="">Product Name</th>
				<th class="text-center" style="" nowrap>Qty</th>
				<th class="text-center" style="width: auto;">Qty in</th>
			</tr>
			</thead>
			<tbody id="shows">
			</tbody>
		</table>
	</div>
</div>
@endsection

@section('script')
<script>

	var stockin_updates = [];

	$.ajaxSetup({
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
	});

	var location_id = "{{$location->id}}";
	var tablestockin ={};
	var isConfirmEnabled = 0;

	var tableData = {};
	var tablestockin = $('#tableFLocaltionProduct').DataTable({
		"processing": true,
		"serverSide": true,
		"autoWidth": false,
		"ajax": {
			/* This is just a sample route */
			"url": "{{route('openitem.openitem_stockinlist')}}",
			"type": "POST",
			data: function (d) {
                d.search = $('input[type=search]').val();
				return $.extend(d, tableData);
			},
			'headers': {
				'X-CSRF-TOKEN': '{{ csrf_token() }}'
			},
		},
		columns: [
			{data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{data: 'product_systemid', name: 'product_systemid'},
			{data: 'product_name', name: 'product_name'},
			{data: 'product_qty', name: 'product_qty'},
			{
				data: 'action',
				name: 'action',
				render: function (data) {return data}
			},
		],

		"columnDefs": [
			{"width": "30px", "targets": [0]},
			{"width": "160px", "targets": [1]},
			{"width": "60px", "targets": [3]},
			{"width": "100px", "targets": [4]},
			{
				"targets": 2, // your case first column
				"className": "text-left",
			},
			{"className": "dt-left vt_middle", "targets": [2]},
			{"className": "dt-right vt_middle slim-cell", "targets": [4]},
			{"className": "dt-center vt_middle slim-cell", "targets": [0, 1, 3]},
			{"className": "vt_middle slim-cell", "targets": [2]},
			//{"className": "vt_middle slim-cell", "targets": [6]},
			{orderable: false, targets: [-1]},
            {
                "searchable": true,
                "caseInsensitive": false
            }
		],
		"drawCallback": function( settings ) {

		    if (stockin_updates.length > 0) {
		    	stockin_updates.forEach((prd) => {
		    		let input_id = 'number_'+prd.product_id
		    		$('#'+input_id).val(prd.qty)
				});
		    }

		    // Output the data for the visible rows to the browser's console
		    // console.log( api.rows( {page:'--current--'} ).data() );
		}
	});

	function increaseValue(id) {
		var num_element = document.getElementById('number_'+id);
		var value = parseFloat(num_element.value);
		value = isNaN(value) ? 0 : value;
		value++;
		num_element.value = value;

		isConfirmEnabled++;

		if(value > 0 ) {

			// Gat values/objects that do not include current product
			stockin_updates = stockin_updates.filter(function(me) {
				if(!(me.product_id == id)) {
					return me;
				}
			});

			stockin_updates.push({
				'product_id': id,
				'qty': value
			});

		}
	}


	function decreaseValue(id) {
		var num_element = document.getElementById('number_'+id);
		var value = parseFloat(num_element.value);
			value = isNaN(value) ? 0 : value;
			value < 1 ? value = 1 : '';
			value--;
		num_element.value = value;

		isConfirmEnabled--;

		if(value > 0 ) {

			// Gat values/objects that do not include current product
			stockin_updates = stockin_updates.filter(function(me) {
				if(!(me.product_id == id)) {
					return me;
				}
			});

			stockin_updates.push({
				'product_id': id,
				'qty': value
			});

		}
	}

	function changeValueOnBlur(id) {
		x = 0;
		ele = document.querySelectorAll('.number');
		ele.forEach( (e) => x += parseInt(e.value));

		isConfirmEnabled = x;

		ele.forEach( (e) => {

			if(e.value > 0 ) {

				var main_id = e.id.replace('number_','');

				// Gat values/objects that do not include current product
				stockin_updates = stockin_updates.filter(function(me) {
					if(!(me.product_id == main_id)) {
						return me;
					}
				});

				stockin_updates.push({
					'product_id': main_id,
					'qty': e.value
				});
			}

		});
	}

	setInterval(() => {
		if (isConfirmEnabled > 0) {
			$("#confirm_update").removeAttr('disabled');
			$("#confirm_update").css('background','linear-gradient(#0447af,#3682f8)');
			$("#confirm_update").css('cursor','pointer');
		} else {
			$("#confirm_update").attr('disabled', true);
			$("#confirm_update").css('background','gray');
			$("#confirm_update").css('cursor','not-allowed');
		}
	}, 1500);

	function update_quantity() {
		console.log("update qty");

        $("#confirm_update").attr('disabled', true);
		$("#confirm_update").css('background','gray');
		$("#confirm_update").css('cursor','not-allowed');

		var table_data = [];
		total_qty = 0;
		tablestockin.rows().eq(0).each( function ( index ) {
			let row = tablestockin.row( index );
			let data = row.data();
			qty = $('#number_'+data.id).val();
			console.log("qty", qty);
			if (qty > 0)
				table_data.push({'product_id': data.id,'location_id': location_id,'qty': qty});
		});

		/*
		if (table_data.length < 1)
			return;
		*/

		if (stockin_updates.length > 0) {

			$.ajax({
				  url: "{{route('openitem.openitem_stockin_update')}}",
				  type: "POST",
				  'headers': {
						'X-CSRF-TOKEN': '{{ csrf_token() }}'
					},
				  data: {table_data : stockin_updates, stock_type:"IN"},
				  cache: false,
				  success: function(dataResult){
					  //$("#productResponse").html(dataResult);
					messageModal(`Stock in successful`);
					tablestockin.ajax.reload();
				    isConfirmEnabled = 0;

				    // Clear the fields
					if (stockin_updates.length > 0) {
						stockin_updates.forEach((prd) => {
							let input_id = 'number_'+prd.product_id
							$('#'+input_id).val('')
						});
					}
				    stockin_updates = [];
				  }
			});
		}

	}

    $(document).ready(function() {
        stockin_updates = [];
    })

    $(document).ready(function() {
		var hidden, visibilityState, visibilityChange;

		if (typeof document.hidden !== "undefined") {
			hidden = "hidden";
			visibilityChange = "visibilitychange";
			ovisibilityState = "visibilityState";

		} else if (typeof document.msHidden !== "undefined") {
			hidden = "msHidden";
			visibilityChange = "msvisibilitychange";
			visibilityState = "msVisibilityState";
		}

		var document_hidden = document[hidden];

		document.addEventListener(visibilityChange, function() {
			if(document_hidden != document[hidden]) {
				if(document[hidden]) {

				} else {
					tablestockin.ajax.reload();
				}

				document_hidden = document[hidden];
			}
		});
	});
</script>
@endsection
@include('common.footer')
