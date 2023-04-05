@extends('common.web')
@section('styles')

<script type="text/javascript" src="{{ asset('js/console_logging.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/qz-tray.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/opossum_qz.js') }}"></script>
<script  type="text/javascript" src="{{asset('js/osmanli_calendar.js')}}?version={{date("hmis")}}"></script>


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
}

.bg-fuel-refund {
	color: white !important;
	border-color: #ff7e30 !important;
	background-color: #ff7e30 !important;
}


.modal-inside .row {
	padding: 0px;
	margin: 0px;
	color: #000;
}

.modal-body {
	position: relative;
	flex: 1 1 auto;
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
button:focus{
    outline:  none !important;
}
/* calander */
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
	border-bottom: none;
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

.data_table > tbody > tr > th {
	font-size: 22px;
	color: white;
	background-color: rgba(255, 255, 255, 0.5);
}

.data_table > tbody > tr > td {
	color: #fff;
	font-weight: 600;
	border: unset;
	font-size: 20px;
	cursor: pointer;
}

.selected_date {
	color: #008000 !important;
	font-weight: bold !important;
}

.selected_date1 {
	color: #008000 !important;
	font-weight: bold !important;
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

.bg-purplelobster{
	color:white;
	border-color:rgba(0,0,255,0.5);
	background-color:rgba(0,0,255,0.5)
}

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
}

.bg-fuel-refund {
	color: white !important;
	border-color: #ff7e30 !important;
	background-color: #ff7e30 !important;
}


#evReceiptDetailModalft .modal-inside .row {
	padding: 0px;
	margin: 0px;
	color: #000;
}

#evReceiptDetailModalft .modal-body {
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
button:focus{
    outline:  none !important;
}
/* calander */
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
	text-align:center;
}

table.dataTable tbody td{
	border-left: 1px solid #dee2e6;
	border-right: 1px solid #dee2e6;
	border-top: none;
	border-bottom: none;
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

.date_table1 > tbody > tr > th {
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
	color: #008000 !important;
	font-weight: bold !important;
}

.selected_date1 {
	color: #008000 !important;
	font-weight: bold !important;
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

.bg-purplelobster{
	color:white;
	border-color:rgba(0,0,255,0.5);
	background-color:rgba(0,0,255,0.5)
}

/*//for calender short day*/
.shortDay ul{
	/* list-style: none; */
	background-color: rgba(255, 255, 255, 0.5);
	position: relative;
	left: -75px;
	width: 124%;
	height: 55px;


 }
.shortDay ul > li{
    font-size: 22px;
    color: white;
    font-weight: 700 !important;
    /* background-color: #2b1f1f; */
    padding: 5px 24px;
    text-align: left !important;
    line-height: 42px;

 }
  .list-inline-item{
	display:inline-block !important;
    margin-right: 0% !important;
    margin-left:0.5% !important;
}
.modal-content{
	overflow: hidden;
}

/* EMMAN, DO NOT USE THIS! IT WILL SCREW UP THE FUEL RECEIPT!!!
.modal-inside .row {
	margin: 0px;
	color: #fff;
	margin-top: 15px;
	padding: 0px !important;
}
*/

.selected-button {
	background-color: green;;
	color: #fff;
}

.selected-button:hover {
	color: #fff !important;
}

.un-selected-button {
	background-color: #007bff;
	color: #fff;
}

.un-selected-button:hover {
	background: green;;
	color: white;
}

.disabled {
	color: gray!important;
   cursor: not-allowed !important;
}
.active {
	color:darkgreen;
	font-weight:700;
}
</style>
@endsection

@section('content')
@include('common.header')
@include('common.menubuttons')
<!-- <div id="loadOverlay" style="background-color:; position:absolute; top:0px; left:0px;
    width:100%; height:100%; z-index:2000;">
</div> -->


<div id="landing-view">
	<!--div id="landing-content" style="width: 100%"-->
	<div class="container-fluid">
		<div class="clearfix"></div>

		<div class="row align-items-center"
			style="height:70px;margin-bottom:5px">

			<div class="col-md-4" style="">
				<h2 style="margin-bottom:0;vertical-align:middle">
					Filled Amount Recovery
				</h2>
			</div>
		</div>

		<div class="modal fade" id="evReceiptDetailModal" tabindex="-1" role="dialog">
			<div class="modal-dialog  modal-dialog-centered"
				style="width: 366px; margin-top: 0!important;margin-bottom: 0!important;">

				<!-- Modal content-->
				<div class="modal-content  modal-inside detail_view">
				</div>
			</div>
		</div>

		<div id="response"></div>
		<div id="responseeod"></div>
		<table class="table table-bordered display" id="recallSummarylistd" style="width:100%;">
            <thead class="thead-dark">
                <tr>
                    <th class="text-center" style="">Pump</th>
                    <th class="text-center" style="">Date</th>
                    <th class="text-left"   style="">Receipt ID</th>
                    <th class="text-center" style="">Fuel</th>
                    <th class="text-center" style="">Filled</th>
                    <th class="text-center" style="">Refund</th>
                    <th class="text-center" style="">Recall</th>
                    <th class="text-center" style="">Refund</th>
                </tr>
            </thead>

            <tbody>
            </tbody>
        </table>
	</div>
</div>

@section('script')
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function getFuelReceiptlist(data) {
        $.ajax({
                method: "post",
                url: "{{ route('fuel.envReceipt') }}",
                data: {
                    id: data,
                    source: 'fuel'	// this is redundant!! it's trying to diff
                                    // creditact for fuel and fulltank
                }
            }).done((data) => {
                $(".detail_view").html(data);
                $("#evReceiptDetailModal").modal('show');
            })
            .fail((data) => {
                console.log("data", data)
            });
    }

	function number_format(value) {
		const valueFormated = (value).toLocaleString('en-US', {
			style: 'currency',
			currency: 'USD',
		});

		valueFormated.split('$');

		// here is the value formated
		var val = valueFormated.split('$')[1]

		return val;
	}

	function recallBtn(pump_no) {
		var url = "/pump-get-status/"+pump_no+"/{{ env('PTS_IPADDR') }}";
		// var url = "/pump-get-status-x";
		$.ajax({
                method: "get",
                url: url
            }).done((data) => {
				const response = data;
				const resp = response.data;
				if(resp.http_code == 200) { // 404
					const packets = resp.response.Packets;
					const pumpDetails = packets[0];
					refillTable.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
						var data = this.data();
						if(data.DT_RowIndex == pumpDetails.Data.Pump)
						{
							var filled = pumpDetails.Data.LastAmount || 0;
							var filled_amount = number_format(filled);
							data['filled'] = filled_amount;
						}
						this.data(data)
					});
					updateFillRecovery(pumpDetails.Data, pumpDetails.Type);
				}
            })
            .fail((data) => {});
	}

	function updateFillRecovery(data, type) {
		var pump = data.Pump;
		$.ajax({
                method: "post",
                url: "{{ route('recall-list.update') }}",
                data: {
					pump_no: pump,
                    filled: data.LastAmount,
                    nozzle: data.LastNozzle,
					price: data.LastPrice,
					trans_no: data.LastTransaction,
					volume: data.LastVolume,
					type: type
                }
            }).done((data) => {refillTable.ajax.reload();console.log(data);})
            .fail((data) => {});
	}

    var tbl_data = {
        'date': '{{ $date }}',
    }

    var tbl_url = "{{ route('recall-list-table') }}";

    var refillTable = $('#recallSummarylistd').DataTable({

        "processing": false,
        "serverSide": true,
        "autoWidth": false,
        // "paging": false,
        "lengthMenu": [
            [ 20, 50, -1 ],
            [ '20 rows', '50 rows', 'Show all' ]
        ],
        "ajax": {
            "url": tbl_url,
            "type": "POST",
            data: function(d) {
                return $.extend(d, tbl_data);
            },
            'headers': {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        },
        "initComplete": function() {
            //alert( 'DataTables has finished its initialisation.' );
        },
        columns: [{
            data: 'pump_no',
            name: 'pump_no',
            "orderable": false,
            "defaultContent": ""
        },{
            data: 'date',
            name: 'date',
            "orderable": false,
            "defaultContent": ""
        },{
            data: 'fuel_receipt_systemid',
            name: 'fuel_receipt_systemid',
            "orderable": false,
            "defaultContent": ""
        },{
            data: 'fuel',
            name: 'fuel',
            "orderable": false,
            "defaultContent": ""
        },{
            data: 'filled',
            name: 'filled',
            "orderable": false,
            "defaultContent": ""
        },{
            data: 'refund',
            name: 'refund',
            "orderable": false,
            "defaultContent": ""
        },{
            data: 'action',
            name: 'action',
            "orderable": false,
            "defaultContent": ""
        },{
            data: 'refund_bluecrab',
            name: 'refund_bluecrab',
            "orderable": false,
            "defaultContent": ""
        }],
        createdRow: (row, data, dataIndex, cells) => {
            $(cells[3]).css('background-color', data.status_color);
        },
		"columnDefs": [
		{ "width": "30px",  "targets": [0,6,7]},
		{ "width": "150px", "targets": [1]},
		{ "width": "auto", "targets" : [2]},
		{ "width": "100px", "targets": [3, 4, 5]},
		{ "className": "dt-left vt_middle", "targets": [2]},
		{ "className": "dt-center vt_middle", "targets": [0, 1, 3, 4, 5, 6, 7]},
		{ "className": "vt_middle","targets": [2]},
		{ orderable: false, targets: [-1]}
		]
    });

    </script>
@endsection

@extends('common.footer')
