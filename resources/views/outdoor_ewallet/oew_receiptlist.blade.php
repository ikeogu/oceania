@extends('common.web')
@section('styles')

<script type="text/javascript" src="{{ asset('js/console_logging.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/qz-tray.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/opossum_qz.js') }}"></script>

<style>
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_processing,
.dataTables_wrapper .dataTables_paginate {
    color: black !important;
    font-weight: normal !important;
}

#receipt-table_length,
#receipt-table_filter,
#receipt-table_info,
.paginate_button {
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

table.dataTable th.dt-right,
table.dataTable td.dt-right {
    text-align: right !important;
}

td {
    vertical-align: middle !important;
    text-align: center;
}

.bg-fuel-refund {
    color: white !important;
    border-color: #ff7e30 !important;
    background-color: #ff7e30 !important;
}
.boxhead a:hover {
    text-decoration: none;
}

.bg-total{
    background-color: rgb(255,126,48) !important;

}
.modal-inside .row {
    padding: 0px;
    margin: 0px;
    color: #000;
}

.modal-body {
    position: relative;
    flex: 1 1 auto;
    padding: 0px !important;
}

table.dataTable.display tbody tr.odd>.sorting_1 {
    background-color: unset !important;
}

tr:hover,
tr:hover>.sorting_1 {
    background: none !important;
}

table.dataTable.display tbody tr.odd>.sorting_1,
table.dataTable.order-column.stripe tbody tr.odd>.sorting_1 {
    background: none !important;
}

table.dataTable.order-column tbody tr>.sorting_1,
table.dataTable.order-column tbody tr>.sorting_2,
table.dataTable.order-column tbody tr>.sorting_3,
table.dataTable.display tbody tr>.sorting_1,
table.dataTable.display tbody tr>.sorting_2,
table.dataTable.display tbody tr>.sorting_3 {
    background-color: #fff !important;
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
    <!--div id="landing-content" style="width: 100%"-->
    <div class="container-fluid">
        <div class="clearfix"></div>
        <div class="row py-2 align-items-center" style="display:flex;height:75px">
            <div class="col" style="width:70%">
                <h2 style="margin-bottom: 0;">
                    Outdoor e-Wallet Receipt List
                </h2>

            </div>
            <div class="col-md-2">
                <h5 style="margin-bottom:0"></h5>
                <h5 style="margin-bottom:0"></h5>
            </div>
            <div class="middle;col-md-3">
                <h5 style="margin-bottom:0;"></h5>
            </div>
            <div class="col-md-2 text-right">
                <h5 style="margin-bottom:0;"></h5>
            </div>
        </div>

        <div id="response"></div>
        <div id="responseeod"></div>
        <table class="table table-bordered display boxhead" id="eodsummarylistd" style="width:100%;">
            <thead class="thead-dark">
                <tr>
                    <th class="text-center" style="width:30px;">No.</th>
                    <th class="text-center" style="width:120px;">Date</th>
                    <th class="text-center" style="width:auto;">Receipt ID</th>
                    <th class="text-center" style="width:100px;">Total</th>
                    <th class="text-center bg-fuel-refund" style="width:100px;">Fuel</th>
                    <th class="text-center bg-fuel-refund" style="width:100px">Filled</th>
                    <th class="text-center bg-fuel-refund" style="width:100px">Refund</th>
                    <th class="text-center bg-fuel-refund nosort" style="width:25px;"></th>
                </tr>
            </thead>

            <tbody>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="oewReceiptDetailModal" tabindex="-1" role="dialog">
	<div class="modal-dialog  modal-dialog-centered"
		style="width: 366px; margin-top: 0!important;margin-bottom: 0!important;">

		<!-- Modal content-->
		<div class="modal-content  modal-inside detail_view">
		</div>
	</div>
</div>

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
@section('script')
<script>
$.ajaxSetup({
	headers: {
		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	}
});

$(document).ready(function() {
    $("#loadOverlay").css("display","none");
    $('#oew_receiptlist').dataTable({
        "aLengthMenu": [[10, 50, 75, -1], [10, 25, 50, 100]],
        "iDisplayLength": 10,
        'aoColumnDefs': [{
        'bSortable': false,
        'aTargets': ['nosort']
    }]
    });
} );

function getOeWReceiptlist(data) {
	$.ajax({
			method: "post",
			url: "{{ route('oew.get_receiptlist') }}",
			data: {
				id: data,
				source: 'fuel'	// this is redundant!! it's trying to diff
								// creditact for fuel and fulltank
			}
		}).done((data) => {
			$(".detail_view").html(data);
			$("#oewReceiptDetailModal").modal('show');
		})
		.fail((data) => {
			console.log("data", data)
		});
}


var tableData = {
	'date': '{{ $date }}'
};


var table = $('#eodsummarylistd').DataTable({

	"processing": false,
	"serverSide": true,
	"autoWidth": false,
	"ajax": {
		"url": "{{ route('oew-list-table') }}",
		"type": "POST",
		data: {
			'date': '{{ $date }}',
		},
		'headers': {
			'X-CSRF-TOKEN': '{{ csrf_token() }}'
		}
	},
	"initComplete": function() {
		//alert( 'DataTables has finished its initialisation.' );
	},
	columns: [{
		data: 'DT_RowIndex',
		name: 'DT_RowIndex'
	}, {
		data: 'date',
		name: 'date'
	}, {
		data: 'systemid',
		name: 'systemid'
	}, {
		data: 'total',
		name: 'total'
	}, {
		data: 'fuel',
		name: 'fuel'
	}, {
		data: 'filled',
		name: 'filled'
	}, {
		data: 'refund',
		name: 'refund'
	}, {
		data: 'action',
		name: 'action'
	}, ],
	createdRow: (row, data, dataIndex, cells) => {
		$(cells[3]).css('background-color', data.status_color);
	},
	"columnDefs": [{
		"width": "3%",
		"targets": [0, 7]
	}, {
		"width": "12%",
		"targets": 1
	}, {
		"width": "100px",
		"targets": [3, 4, 5, 6, 7]
	}, {
		"className": "dt-center vt_middle",
		"targets": [0, 1, 2, 3, 4, 5, 6, 7]
	}, {
		"className": "vt_middle",
		"targets": [2]
	}, {
		orderable: false,
		targets: [-1]
	}, ],
});


function refundMe(id, fuel, filled) {
	$.ajax({
			method: "post",
			url: "{{ route('oew.refund') }}",
			data: {
				id: id,
				filled: filled,
				fuel: fuel
			}
		}).done((data) => {
      console.log("data", data)
			table.ajax.reload();
			localStorage.setItem('receipt_refunded', id);
			localStorage.setItem('receipt_refunded1', id);
			localStorage.removeItem('receipt_refunded')
			localStorage.removeItem('reload_for_fm_sales')
			localStorage.setItem("reload_for_fm_sales", "yes");

      var oew_refund_btn = document.getElementById('oew_refund_btn');
      $('#oew_refund_btn').removeClass('btn-warning');
      $('#oew_refund_btn').css("background-color", "#3a3535");
      $('#oew_refund_btn').attr('disabled', true);

		})
		.fail((data) => {
			console.log("data", data)
		});
}


function updateFilled(receipt_id, filled, refund) {
	log2laravel('info', 'updateFilled: receipt_id=' +
		receipt_id + ', filled=' + filled +
		', refund=' + refund);
	$.ajax({
		method: "post",
		url: "{{ route('oew.update.filled') }}",
		data: {
			id: receipt_id,
			filled: filled,
			refund: refund
		},
	}).done((data) => {
		table.ajax.reload();
	})
	.fail((data) => {
		console.log("data", data)
	});
}


var isVisible;
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
		if (document_hidden != document[hidden]) {
			if (document[hidden]) {
				// Document hidden
				console.log("close the tab");
			} else {

				//	window.location.reload();
				table.ajax.reload();

				update_filled_cell();

				// updateLocalStorageValues();
				log2laravel('info', 'tab eventlistner :back to tab reloaded');
			}

			document_hidden = document[hidden];
		}
	});
});

document.addEventListener("DOMContentLoaded", function(event) {
	window.onstorage = function(e) {
		switch (e.key) {
			case "reload_receipt_list":
				table.ajax.reload();
				update_filled_cell();
				log2laravel('info',
					'reload_receipt_list :back to tab reloaded');

				localStorage.removeItem('reload_receipt_list')
				break;
		}
	}
});


function update_filled_cell() {

	table.ajax.reload(function() {
		var trs = $("tr");

		for (var i = 0; i < trs.length; i++) {
			if (trs[i].id) {

				//split row id to fetch receipt_id
				var fields = (trs[i].id).split('-');
				//take fuel_receipt_data of pump
				var pump_data = JSON.parse(localStorage.getItem(fields[0]));

				//check if data exist in local storage and is type of object to fetch
				if (pump_data && pump_data.receipt_id) {

					//check the filled_column value
					var filled = $('#' + trs[i].id).find("td").eq(5).text();
					//check if filled column is set to 0.00
					not_filled = parseFloat(filled) === 0.00;

					log2laravel('info', 'update_filled_cell: fields[1]=' +
						JSON.stringify(fields[1]));
					log2laravel('info', 'update_filled_cell: pump_data =' +
						JSON.stringify(pump_data));
					log2laravel('info', 'update_filled_cell: filled =' +
						JSON.stringify(filled));
					log2laravel('info', 'update_filled_cell: not_filled =' +
						not_filled);

					// check if its active receipt match from local_storage
					is_active_receipt = parseInt(fields[1]) === parseInt(pump_data.receipt_id);

					log2laravel('info', 'update_filled_cell: is_active_receipt =' +
						is_active_receipt);

					//if not filled and receipt is active then update record
					if (not_filled && is_active_receipt) {

						log2laravel('info', 'update_filled_cell: Just before CELL update!');

						$('#' + trs[i].id).find("td").eq(5).text(pump_data.amount);
						$('#' + trs[i].id).find("td").eq(6).text((pump_data.dose - pump_data.amount)
							.toFixed(2));

						log2laravel('info', 'update_filled_cell: Just before updateFilled:' +
							' pump_data=' + JSON.stringify(pump_data));

						updateFilled(pump_data.receipt_id,
							pump_data.amount,
							pump_data.dose - pump_data.amount);
					}
				}
			}
		}
	})
}
</script>
@endsection
@extends('common.footer')
