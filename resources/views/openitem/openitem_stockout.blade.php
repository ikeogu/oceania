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
                <h2 class="mb-0">Open Item: Stock Out</h2>
            </div>

            <div class="col-sm-4 pl-0">
                <div class="row mb-0 align-items-center" style="float:right;">
					<div class="col-auto " style="margin-top:0">
						<h5 class="mb-0">
							{{$location->name}}
						</h5>
					</div>
                    <button onclick="update_quantity()"
						class="btn btn-success bg-confirm-button sellerbutton mr-0"
						style="padding:0px;float:right;margin-left:5px; cursor: default;
						margin-bottom:0 !important; border-radius:10px;background: gray;"
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
                    <th class="text-center" style="text-align: center;">Product&nbsp;ID</th>
                    <th class="" style="">Product&nbsp;Name</th>
                    <th class="text-center" style="" nowrap>Qty</th>
                    <th class="text-center" style="width: auto;">Qty&nbsp;Out</th>
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

    var stockout_updates = [];

        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

		var tablestockin ={};
		var location_id = "{{$location->id}}";
		var	isConfirmEnabled = 0;
        var tableData = {};

        var tablestockin = $('#tableFLocaltionProduct').DataTable({
            "processing": true,
            "serverSide": true,
            "autoWidth": false,
            "ajax": {
                /* This is just a sample route */
                "url": "{{route('openitem.openitem_stockoutlist')}}",
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
                {data: 'action', name: 'action'},
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
                {"className": "dt-right vt_middle slim-cell", "targets": [4,]},
                {"className": "dt-center vt_middle slim-cell", "targets": [0, 1, 3]},
                {"className": "vt_middle slim-cell", "targets": [2]},
               // {"className": "vt_middle slim-cell", "targets": [6]},
                {orderable: false, targets: [-1]},
            ],
			"drawCallback": function( settings ) {

				if (stockout_updates.length > 0) {
					stockout_updates.forEach((prd) => {
						let input_id = 'number_'+prd.product_id
						$('#'+input_id).val(prd.qty)
					});
				}
			}
        });

		function increaseValue(id) {
			var num_element = document.getElementById('number_'+id);
			var existing_qty = parseInt($(`#qty_${id}`).text())
			var value = parseFloat(num_element.value);
			value = isNaN(value) ? 0 : value;
			value++;
			if (existing_qty >= value)	{
				num_element.value = value;
				isConfirmEnabled++;

				if(value > 0 ) {

					// Gat values/objects that do not include current product
					stockout_updates = stockout_updates.filter(function(me) {
						if(!(me.product_id == id)) {
							return me;
						}
					});

					stockout_updates.push({
						'product_id': id,
						'qty': value
					});

				}
			}
		}

		function decreaseValue(id) {
			var num_element = document.getElementById('number_'+id);
			var existing_qty = parseInt($(`#qty_${id}`).text())
			var value = parseFloat(num_element.value);
				value = isNaN(value) ? 0 : value;
				value < 1 ? value = 1 : '';
				value--;

			if (existing_qty >= value) {
				num_element.value = value;
				isConfirmEnabled--;
				if (isConfirmEnabled < 0)
					isConfirmEnabled = 0;

				if(value > 0 ) {

					// Gat values/objects that do not include current product
					stockout_updates = stockout_updates.filter(function(me) {
						if(!(me.product_id == id)) {
							return me;
						}
					});

					stockout_updates.push({
						'product_id': id,
						'qty': value
					});

				}
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
					stockout_updates = stockout_updates.filter(function(me) {
						if(!(me.product_id == main_id)) {
							return me;
						}
					});

					stockout_updates.push({
						'product_id': main_id,
						'qty': e.value
					});
				}

			});
		}

		function update_quantity() {
			var table_data = [];
			total_qty = 0;

		  	if (stockout_updates.length > 0) {

		  		$.ajax({
				  url: "{{route('openitem.openitem_stockout_update')}}",
				  type: "POST",
				  'headers': {
					'X-CSRF-TOKEN': '{{ csrf_token() }}'
					},
				  data: {table_data : stockout_updates, stock_type:"OUT"},
				  cache: false,
				  success: function(dataResult){
				    isConfirmEnabled = 0
					$("#confirm_update").attr('disabled', true);
					$("#confirm_update").css('background','gray');
					$("#confirm_update").css('cursor','not-allowed');

					messageModal(`Stock out successful`);
					tablestockin.ajax.reload();

					// Clear the fields
					if (stockout_updates.length > 0) {
						stockout_updates.forEach((prd) => {
							let input_id = 'number_'+prd.product_id
							$('#'+input_id).val('')
						});
					}
				    stockout_updates = [];
				  }
				});

		  	}

	}

	function messageModal(msg) {
		$('#modalMessage').modal('show');
		$('#statusModalLabelMsg').html(msg);
		setTimeout(function(){
			$('#modalMessage').modal('hide');
		}, 3500);
	}

	setInterval(() => {
		if (isConfirmEnabled > 0) {
			$("#confirm_update").removeAttr('disabled');
			//$("#confirm_update").addClass('poa-bluecrab-button','');
			$("#confirm_update").css('background','linear-gradient(#0447af,#3682f8)');
			$("#confirm_update").css('cursor','pointer');
		} else {
			$("#confirm_update").attr('disabled', true);
			$("#confirm_update").css('background','gray');
			$("#confirm_update").removeClass('poa-bluecrab-button','');
			$("#confirm_update").css('cursor','not-allowed');
		//	$("#confirm_update").css('margin-bottom', '5px !important');
		}
	}, 1500);

    $(document).ready(function() {
        stockout_updates = [];
    });

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
