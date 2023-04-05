@extends('common.web')
@section('styles')


<script type="text/javascript" src="{{ asset('js/console_logging.js') }}"></script>
<script type="text/javascript" src="{{asset('js/qz-tray.js')}}"></script>
<script type="text/javascript" src="{{asset('js/opossum_qz.js')}}"></script>
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


#eodModal_1 .modal-inside .row {
	padding: 0px;
	margin: 0px;
	color: #000;
}

#eodModal_1.modal-body {
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

/* EMMAN, THIS IS YOUR BUG!! DON'T MESS WITH THIS STYLE AS IT WILL SCREW
   UP THE RECEIPT
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

.spinner-button i {
    color: #fff
}

.spinner-button:hover i {
    color: #fff
}

.fa{
    color:#fff;
}

.fa:hover{
    color:#fff;
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

		<div class="row  align-items-center"
			 style="height:70px; margin-bottom:5px;">

			<div class="col-md-4" style="">
				<h2 style="margin-bottom: 0;">
					C-Store Receipt List
				</h2>
			</div>
			<div class="col-md-2">
				<h5 style="margin-bottom:0">{{ $location->name??"" }}</h5>
				<h5 style="margin-bottom:0">{{ $location->systemid??"" }}</h5>
			</div>
			<div class="col-md-3">
				<h5 style="margin-bottom:0;">Terminal ID: {{ $terminal->systemid??"" }}</h5>

			</div>
			<div class="col-md-3 d-flex justify-content-end">
				{{-- <h5 style="margin-bottom:0;"></h5> --}}
				<form action="{{route('export_cstore_excel')}}"
					style="margin-bottom:0!important"
				 	method="POST">

                    <div style="display:inline;padding-left:0;margin-bottom:20px">
                        <input class="to_date form-control btnremove"
                        style="display:inline;margin-top:10px;padding-top:0px !important;
                        position:relative;top:2px;
                        padding-bottom: 0px; width:110px;padding-right:0;padding-left:0px;
                        text-align: center;"
                        value="Start Date"
                        onclick="ev_start_dialog()"
                        id="ev_start_date" name="cstore_start_date" placeholder="Select" />
                    </div>
                    {{ csrf_field() }}
                    To
                    <div style="display:inline;padding-left:0;
                        margin-bottom:20px">
                        <input class="to_date form-control btnremove"
                        style="display:inline;margin-top:10px;padding-top:0px !important;
                        position:relative;top:2px;
                        padding-bottom: 0px; width:110px;padding-right:0;
                        padding-left:0px; text-align: center;"
                        value="End Date"
                        onclick="ev_end_dialog()"
                        id="ev_end_date" name="cstore_end_date" placeholder="Select" />
                    </div>
                    <div style="display:inline; margin-right:1px;">

                         <button class="btn btn-success "
					        style="height:70px;width: 70px; border-radius:10px !important;"
					        id="fulltank-rl" style="color:white;" >
                            <span class="d-none spinner-border spinner-border-sm" role="status" aria-hidden="true" style="z-index:2; position: fixed; margin-top: 3px;
                        margin-left:14px"></span>
                            Excel
                            </span>
                        </button>
                    </div>
                </form>

			</div>
		</div>

		<div id="response"></div>
		<div id="responseeod"></div>
		<table class="table table-bordered display"
			   id="cstoreReceiptlist" style="width:100%;">
			<thead class="thead-dark">
			<tr>
				<th style="text-align:center;width:30px;" >No</th>
				<th style="text-align:center;width:150px">Date</th>
				<th style="text-align:center;width:auto">Receipt&nbsp;ID</th>
				<th style="text-align:center;width:100px;">Total</th>
			</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>

<div class="modal fade" id="eodModal_1" tabindex="-1" role="dialog"
	 style="overflow:auto;" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered mw-75"
		 style="width:370px" role="document">
		<div id="receipt-model-div" class="modal-content modal-inside">
		</div>
	</div>
</div>
<div class="modal fade" id="voidreceiptmodal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-dialog-centered  mw-75 w-50" role="document">
		<div class="modal-content modal-inside bg-purplelobster">
			<div style="border:0" class="modal-header"></div>
			<div class="modal-body text-center">
				<h5 class="modal-title text-white" id="logoutModalLabel">
					Do you want to void the receipt?
				</h5>
				<br/><input type="hidden" id="receiptid" name="receiptid">
				<textarea placeholder="Reason for void receipt" rows='4'
						  id="reason_void" class="form-control"></textarea>
			</div>
			<div class="modal-footer"
				 style="border-top:0 none; padding:0;padding-bottom: 15px;">
				<div class="row" style="width: 100%; padding:0">
					<div class="col col-m-12 text-center">
						<a class="btn btn-primary"
						   href="javascript:void(0)" style="width:100px"
						   data-dismiss="modal"
						   onclick="onConfirmReceiptVoid()">
							Confirm
						</a>
						<button type="button" class="btn btn-danger"
								data-dismiss="modal" style="width:100px">
							Cancel
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="refundreceiptmodal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-dialog-centered  mw-75 w-50" role="document">
		<div class="modal-content modal-inside bg-purplelobster">
			<div style="border:0" class="modal-header"></div>
			<div class="modal-body text-center">
				<h5 class="modal-title text-white" id="logoutModalLabel">
					Please fill in the amount to be refunded.
				</h5><br/>
				<input type="hidden" id="refund_receiptid" name="receiptid">
				<div class="row align-items-center">
					<div class="col-6">
						<span>Refund Amount</span>
					</div>
					<div class="col-6">
						<input class="form-control text-right"
							id="receipt_refund_amount" type="number"
							placeholder="0.00"
							onkeyup='enforceMinMax(this)'/>
						<input id="receipt_refund_amount_buffer" type="hidden"/>
					</div>
				</div>
				<textarea placeholder="Reason for refund receipt" rows='4'
						  id="reason_refund" class="form-control">
			</textarea>
			</div>
			<div class="modal-footer"
				 style="border-top:0 none; padding:0;padding-bottom: 15px;">
				<div class="row" style="width: 100%; padding:0">
					<div class="col col-m-12 text-center">
						<a class="btn btn-primary"
						   href="javascript:void(0)" style="width:100px"
						   data-dismiss="modal"
						   onclick="onConfirmReceiptRefund()">
							Confirm
						</a>
						<button type="button" class="btn btn-danger"
								data-dismiss="modal" style="width:100px">
							Cancel
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalMessage" tabindex="-1" role="dialog"
	 aria-hidden="true" style="text-align: center;">
	<div class="modal-dialog modal-dialog-centered  mw-75 w-50" role="document"
		 style="display: inline-flex;">
		<div class="modal-content modal-inside bg-purplelobster"
			 style="width: 100%;  background-color: {{@$color}} !important">
			<div class="modal-header" style="border:0">&nbsp;</div>
			<div class="modal-body text-center">
				<h5 class="modal-title text-white" id="statusModalLabelMsg"></h5>
			</div>
			<div class="modal-footer" style="border-top:0 none;">&nbsp;</div>
		</div>
	</div>
</div>


<div id="res"></div>
<div class="modal fade" id="showDateModalFromS" tabindex="-1" role="dialog" aria-labelledby="staffNameLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered  mw-75 w-50" role="document">
    <div class="modal-content modal-inside bg-purplelobster">
      <div class="modal-body text-center" style="min-height: 485px;max-height:485px">
        <div class="row" style="margin-top:15px">
          <div class="col-md-2">
            <i class="prev-month fa fa-chevron-left fa-3x" style="cursor:pointer;display: inline-flex;"></i>
          </div>
          <div class=" col-md-8">
            <div class="month-year text-center text-white"></div>
          </div>
          <div class="col-md-2">
            <i style="cursor:pointer" class="next-month fa fa-chevron-right fa-3x"></i>
          </div>
        </div>
        <div class="row">
          <div class="shortDay">
            <ul>
              <li class="list-inline-item">S</li>
              <li class="list-inline-item">M</li>
              <li class="list-inline-item">T</li>
              <li class="list-inline-item">W</li>
              <li class="list-inline-item">T</li>
              <li class="list-inline-item">F</li>
              <li class="list-inline-item">S</li>
            </ul>
          </div>

        </div>
        <table class="table date_table">
          <tr style="display: none;">
            <th>S</th>
            <th>M</th>
            <th>T</th>
            <th>W</th>
            <th>T</th>
            <th>F</th>
            <th>S</th>
          </tr>
        </table>
      </div>
    </div>
    <form id="status-form" action="{{ route('logout') }}" method="POST" style="display: none;">
      @csrf
    </form>
  </div>
</div>


@section ('script')
<script>
    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });

     $('#fulltank-rl').on('click', es => {
        let spinner = $(es.currentTarget).find('span')
        spinner.removeClass('d-none')
        setTimeout(_ => spinner.addClass('d-none'), 9800)
    })
    function getOptlist(id) {
        $.ajax({
            method: "post",
            url: "{{route('local_cabinet.optList')}}",
            data: {id: id}
        }).done((data) => {
            $("#optlistModal-table").html(data);
            $("#optlistModal").modal('show');
        })
            .fail((data) => {
                console.log("data", data)
            });
    }

    function getEvReceiptlist(id) {
        $.ajax({
            method: "post",
            url: "{{route('ev_receipt.evList')}}",
            data: {id: id}
        }).done((data) => {
            $("#evlistModal-table").html(data);
            $("#evlistModal").modal('show');
        })
        .fail((data) => {
            console.log("data", data)
        });
    }

    function showPSSReceipt(date) {
        $('#eodSummaryListModal').modal('hide').html();
        $('#optlistModal').modal('hide').html();
        $('#receiptoposModal').modal('hide');
        $.ajax({
            url: "{{route('pshift.list',[$date])}}",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'post',
            data: {date},
            success: function (response) {
                // console.log(response);
                //res
                $('#eod-model-div').html(response);
                $('#eodpssModal_1').modal('show');
            },
            error: function (e) {
                $('#responseeod').html(e);
                $("#msgModal").modal('show');
            }
        });
    }

    function onConfirmReceiptVoid() {
        var receiptid = $('#receiptid').val();
        var reason_void = $('#reason_void').val();
        var dt = time_void = new Date();
        var months = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL",
            "AUG", "SEP", "OCT", "NOV", "DEC"];

        time_void = time_void.getDate() + " " + months[time_void.getMonth()] +
            " " + time_void.getFullYear().toString().substr(-2) + " " +
            time_void.getHours() + ":" + time_void.getMinutes() + ":" +
            time_void.getSeconds();

        var dtstring = dt.getFullYear() + "-" + (dt.getMonth() + 1) + "-" +
            dt.getDate() + " " + dt.getHours() + ":" + dt.getMinutes() + ":" +
            dt.getSeconds();

        $("#void-stamp" + receiptid).show();
        $("#void-div" + receiptid).show();
        $("#void-time" + receiptid).html(time_void);
        $("#void-reason" + receiptid).html(reason_void);
        $.ajax({
            url: "{{route('local_cabinet.cstore.voidReceipt')}}",
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                "receiptid": receiptid,
                "reason_void": reason_void,
                "voitdatetime": dtstring
            },
            dataType: 'json',
            success: function (response) {
                table.ajax.reload();
                $.ajax({
                    url: "/local_cabinet/cstore/eodReceiptPopup/" + receiptid,
                    // headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    type: 'get',
                    success: function (response) {
                        // console.log(response);
                        $('#receipt-model-div').html(response);
                    },
                    error: function (e) {
                        $('#responseeod').html(e);
                        $("#msgModal").modal('show');
                    }
                });
            }
        });
        generate_cstorevoidPdf(receiptid);
    }

    function generate_cstorevoidPdf(receipt_id){
        // Generate the void PDF

        console.log("from Void: ", receipt_id)

        log2laravel('Void PDF : ' + receipt_id);

        $.ajax({
            url: "{{ route('cstore.generatevoidpdf') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'post',
            data: {
                receipt_id: receipt_id,
            },
            success: function(response) {
                var error1 = false,
                    error2 = false;
                console.log('PR ' + JSON.stringify(response));

                try {
                    eval(response);
                    console.log('eval working');
                } catch (exc) {
                    console.error('ERROR eval(): ' + exc);
                }

            },
            error: function(e) {
                console.log('PR ' + JSON.stringify(e));
            }
        });
    }

    function showReceipt(id) {
        $('#eodSummaryListModal').modal('hide').html();
        $('#optlistModal').modal('hide').html();
        $('#receiptoposModal').modal('hide');
        $.ajax({
            url: "/local_cabinet/cstore/eodReceiptPopup/" + id,
            // headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'get',
            success: function (response) {
                // console.log(response);
                $('#receipt-model-div').html(response);
                $('#eodModal_1').modal('show');
            },
            error: function (e) {
                $('#responseeod').html(e);
                $("#msgModal").modal('show');
            }
        });
    }


    function eod_summarylist(eod_date) {
        $.ajax({
            url: "{{route('local_cabinet.eodsummary.popup')}}/" + eod_date,
            // headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'get',
            success: function (response) {
                // console.log(response);
                $('#eodSummaryListModal-table-div').html(response);
                $('#eodSummaryListModal').modal('show').html();
            },
            error: function (e) {
                $('#responseeod').html(e);
                $("#msgModal").modal('show');
            }
        });
    }


    function receipt_list(date) {
        $.ajax({
            url: "{{route('local_cabinet.receipt.list')}}",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'post',
            data: {
                date
            },
            success: function (response) {
                //console.log(response);
                $('#receiptoposModal-table').html(response);
                $('#receiptoposModal').modal('show').html();

                $('#receipt-table').DataTable({
                    "order": [],
                    "columnDefs": [
                        {"targets": -1, 'orderable': false}
                    ],
                    "autoWidth": false,
                })
            },
            error: function (e) {
                $('#responseeod').html(e);
                $("#msgModal").modal('show');
            }
        });
    }

    function void_receipt(id) {
        $('#receiptid').val(id);
        $('#voidreceiptmodal').modal('show');
    }

    function refund_receipt(id) {
        $('#refund_receiptid').val(id);
        max = $("span#total_amount_unq").text().trim()
        $("#receipt_refund_amount").attr('min', 0);
        $("#receipt_refund_amount").attr('max', max);
        $("#receipt_refund_amount").val('');
        $("#receipt_refund_amount_buffer").val('');
        $("#refundreceiptmodal").modal('show');
    }

    function onConfirmReceiptRefund() {

        receipt_id = $('#refund_receiptid').val();
        $('#refund_receiptid').val('');
        amount = $("#receipt_refund_amount").val();
        $("#receipt_refund_amount").val('');
        $("#receipt_refund_amount_buffer").val('');
        description	= $("#reason_refund").val();
        $("#reason_refund").val('');


        $.ajax({
            url: "{{route('local_cabinet.cstore.refund')}}",
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {receipt_id, amount,description},
            dataType: 'json',
            success: function (response) {
                console.log('***cstore recipt refunded***', response)
                table.ajax.reload();
                $('#eodSummaryListModal').modal('hide').html();
                $('#optlistModal').modal('hide').html();
                $('#receiptoposModal').modal('hide');
                $('#eodpssModal_1').modal('hide');
                $("#refundreceiptmodal").modal('hide');
                $("#eodModal_1").modal('hide');
                $("#refund_cstore_receipt").css({
                    "background": "#3a3535cc",
                    "cursor":"not-allowed",
                });
                messageModal('Refund is successful');
            }
        });

        $("#void_cstore_receipt, #refund_cstore_receipt").css({
            "background": "#3a3535cc",
            "cursor":"not-allowed",
        });
        $("#void_cstore_receipt, #refund_cstore_receipt").attr('disabled', 'disabled');
        generate_cstorerefundPdf(receipt_id);
        console.log('***cstore recipt refunded***')
        table.ajax.reload()
    }

    function generate_cstorerefundPdf(receipt_id){
        // Generate the void PDF

        console.log("from Void: ", receipt_id)

        log2laravel('Void PDF : ' + receipt_id);

        $.ajax({
            url: "{{ route('cstore.generaterefundpdf') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'post',
            data: {
                receipt_id: receipt_id,
            },
            success: function(response) {
                var error1 = false,
                    error2 = false;
                console.log('PR ' + JSON.stringify(response));

                try {
                    eval(response);
                    console.log('eval working');
                } catch (exc) {
                    console.error('ERROR eval(): ' + exc);
                }

            },
            error: function(e) {
                console.log('PR ' + JSON.stringify(e));
            }
        });
    }


    function pssReceiptPopup(login_time, logout_time, user_systemid) {
        $('#eodSummaryListModal').modal('hide').html();
        $('#optlistModal').modal('hide').html();
        $('#receiptoposModal').modal('hide');
        $('#eodpssModal_1').modal('hide');

        $.ajax({
            url: "{{route('pshift.details')}}",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'post',
            data: {login_time, logout_time, user_systemid},
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


    function generate_refund(r_id, filled, e) {
        var data = {
            'receipt_id': r_id,
            'filled': filled
        };

        log2laravel('debug', 'generate_refund: data=' +
            JSON.stringify(data));


        crab = '#crab_' + r_id;
        $(crab).attr('style', 'width:30px;filter:grayscale(1) brightness(1.5);');
        $(crab).attr('disabled', true);

        $.ajax({
            url: "{{route('local_cabinet.nozzle.down.refund')}}",
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: data,
            dataType: 'json',
            success: function (response) {

                log2laravel('info', 'generate_refund: ' +
                    'local_cabinet.nozzle.down.refund SUCCESS: ' +
                    JSON.stringify(response));

                if (response.responseText == "VOID") {
                    messageModal('The receipt is void')
                }

            },
            error: function (response) {
                console.error(JSON.stringify(response));

                log2laravel('error', 'generate_refund: ' +
                    'local_cabinet.nozzle.down.refund ERROR: ' +
                    JSON.stringify(response));

                if (response.responseText == "VOID") {
                    messageModal('The receipt is void')
                }

            }
        });
    }


    function messageModal(msg) {
        $('#modalMessage').modal('show');
        $('#statusModalLabelMsg').html(msg);
        setTimeout(function () {
            $('#modalMessage').modal('hide');
        }, 2500);
    }

    function enforceMinMax(el){
        if(el.value != ""){
            if(parseFloat(el.value) < parseFloat(el.min)){
                el.value = el.min;
            }
            if(parseFloat(el.value) > parseFloat(el.max)){
                el.value = el.max;
                $("#receipt_refund_amount_buffer").val(el.value.replace('.',''));
            }
        }
    }

    //--------------------
    filter_price("#receipt_refund_amount", "#receipt_refund_amount_buffer");
    function filter_price(target_field,buffer_in) {
        $(target_field).off();
        $(target_field).on( "keydown", function( event ) {
            event.preventDefault()
            if (event.keyCode == 8) {
                $(buffer_in).val('')
                $(target_field).val('')
                return null
            }
            if (isNaN(event.key) ||
                $.inArray( event.keyCode, [13,38,40,37,39] ) !== -1 ||
                event.keyCode == 13) {
                if ($(buffer_in).val() != '') {
                    $(target_field).val(atm_money(parseInt($(buffer_in).val())))
                } else {
                    $(target_field).val('')
                }
                return null;
            }

            const input =  event.key;
            old_val = $(buffer_in).val()
            if (old_val === '0.00') {
                $(buffer_in).val('')
                $(target_field).val('')
                old_val = ''
            }
            $(buffer_in).val(''+old_val+input)
            $(target_field).val(atm_money(parseInt($(buffer_in).val())))
        });
    }


    function atm_money(num) {
        if (num.toString().length == 1) {
            return '0.0' + num.toString()
        } else if (num.toString().length == 2) {
            return '0.' + num.toString()
        } else if (num.toString().length == 3) {
            return  num.toString()[0] + '.' + num.toString()[1] +
                num.toString()[2];
        } else if (num.toString().length >= 4) {
            return num.toString().slice(0, (num.toString().length - 2)) +
                '.' + num.toString()[(num.toString().length - 2)] +
                num.toString()[(num.toString().length - 1)];
        }
    }


    function ptype_search(search) {
        if (search.length > 0) {

            cstore_tbl_url = "{{ route('cstore.search.ptype',[$date]) }}";
            cstore_tbl_data = {
                'date': '{{ $date }}',
                'ptype': search,
            }
            table.ajax.url( cstore_tbl_url ).load();
        } else {
            cstore_tbl_url = "{{route('local_cabinet.cstore-list-table',[$date])}}";
            cstore_tbl_data = {'date': '{{ $date }}'}
            table.ajax.url( cstore_tbl_url ).load();
        }
    }

    var cstore_tbl_data = {'date': '{{ $date }}'}

    var table = $('#cstoreReceiptlist').DataTable({
            "processing": true,
            "serverSide": true,
            "autoWidth": false,
            "ajax": {
                /* This is just a sample route */
                "url": "{{route('local_cabinet.cstore-list-table',[$date])}}",
                "type": "POST",
                data: function (d) {
                    return $.extend(d, cstore_tbl_data);
                },
                'headers': {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                error: function (xhr, error, code)
                {
                    console.log(xhr);
                    console.log(code);
                },
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'created_at', name: 'created_at'},
                {data: 'systemid', name: 'systemid', render: function (data) {
                        let all_data = data;
                        return "<a href='javascript:void(0)' style='text-decoration: none;' onclick='showReceipt(" +all_data['id'] + ")'>" + all_data['systemid'] + "</a>"
                    }
                },
                {data: 'total', name: 'total'},
            ],
            createdRow: (row, data, dataIndex, cells) => {
                $(cells[3]).css('background-color', data.status_color);
            },
            columnDefs: [
                {"className":"dt-center", "targets":[0,1,2,3]},
                // Squidster: How to center th, but text-right body?
                //{"className":"dt-right", "targets":3},
                {
                    orderable: false,
                    targets: [-1]
                },
            ]
        });

    $(document).ready(function() {
        $("#loadOverlay").css("display","none");

        table.ajax.reload();

        $('#cstoreReceiptlist_filter input').attr ('id', 'cstore_search');

        $('#cstore_search').on( 'keyup', function () {
            let sanitized_search = this.value.replace(/[^a-zA-Z0-9]/g, '')
            sanitized_search.toLowerCase
            ptype_search(sanitized_search)
        } );
    });

     // calender
    var start_date_dialog = osmanli_calendar;
    var completion_date_dialog = osmanli_calendar;
    var terminal_date;

    store_date = dateToYMDEmpty(new Date());
    $("#ev_start_date").val(store_date);
    $("#ev_end_date").val(store_date);

    localStorage.removeItem("showEVStartDate")
    localStorage.removeItem("showEndEVStartDate")


    function dateToYMDEmpty(date) {
        var strArray=['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        var d = date.getDate();
        var m = strArray[date.getMonth()];
        var y = date.getFullYear().toString().substr(-2);
        var currentHours = date.getHours();
        return '' + (d <= 9 ? '0' + d : d) + '' + m + '' + y ;
    }

    var end_date_dialog = osmanli_calendar;

        // Ev Dialog
    function ev_start_dialog(e) {
        // alert("yessss")
        sessionStorage.removeItem("modalTrue");
        sessionStorage.setItem("modalTrue",'showTransStartDate');

        date = new Date();
        start_date_dialog.MAX_DATE = date;
        start_date_dialog.DAYS_DISABLE_MIN = "ON";
        start_date_dialog.DAYS_DISABLE_MAX = "ON";
        start_date_dialog.MIN_DATE = new Date('{{$approved_at}}');
        $('.next-month').off();
        $('.prev-month').off();

        $('.prev-month').click(function () {start_date_dialog.pre_month()});
        $('.next-month').click(function () {start_date_dialog.next_month()});

        start_date_dialog.CURRENT_DATE = new Date();

        if(localStorage.getItem("showEVStartDate")===null)
        {


            start_date_dialog.SELECT_DATE = new Date()
        } else{
            var loclaaa=  localStorage.getItem("showEVStartDate");
            // console.log( loclaaa)
            // start_date_dialog.CURRENT_DATE =new Date(localStorage.getItem("showH2StartDate"));
            start_date_dialog.SELECT_DATE = new Date(localStorage.getItem("showEVStartDate"))
            start_date_dialog.CURRENT_DATE = new Date(localStorage.getItem("showEVStartDate"))


            // console.log(start_date_dialog.SELECT_DATE)
        }
            var date =   start_date_dialog.SELECT_DATE.getDate();
            const monthNames = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
            ];
            var month  =  monthNames[start_date_dialog.SELECT_DATE .getMonth()];
            var year = start_date_dialog.SELECT_DATE.getFullYear();
            var select_moth_year  =  month+" "+year
            var date =   start_date_dialog.SELECT_DATE.getDate();
            sessionStorage.setItem("date_check",date);
            sessionStorage.setItem("select_moth_year",select_moth_year);

        if(date == 1){
            start_date_dialog.CURRENT_DATE.setDate(4)
            start_date_dialog.ON_SELECT_FUNC = function(){
                var date = osmanli_calendar.SELECT_DATE;
                localStorage.setItem("showEVStartDate",date)
                var start_date = dateToYMDEmpty(date);
                // console.log(start_date)

                // localStorage.setItem("sTransDate",start_date)

                $("#ev_start_date").val(start_date);
                jQuery('#showDateModalFromS').modal('hide');
            }

        }else{
            start_date_dialog.ON_SELECT_FUNC = function(){
                var date = osmanli_calendar.SELECT_DATE;
                localStorage.setItem("showEVStartDate",date)
                var start_date = dateToYMDEmpty(date);
                console.log(start_date)

                // localStorage.setItem("sTransDate",start_date)

                $("#ev_start_date").val(start_date);
                jQuery('#showDateModalFromS').modal('hide');
            }
        }


        start_date_dialog.init()
        if(date == 1){
            var table_data =  $(".date_table tbody tr").eq(1)
            table_data.children('td').each(function(){
            var data = $(this).html();
                if(data== 1){
                    $(this).addClass("selected_date")

                }
            })
        }
        jQuery('#showDateModalFromS').modal('show');
        //end showTransStartDate
        var EndDate = new Date();
    }
    function ev_end_dialog(e) {
        // alert("yessss")
        sessionStorage.removeItem("modalTrue");
        sessionStorage.setItem("modalTrue",'showTransStartDate');

        date = new Date();
        start_date_dialog.MAX_DATE = date;
        start_date_dialog.DAYS_DISABLE_MIN = "ON";
        start_date_dialog.DAYS_DISABLE_MAX = "ON";
        //start_date_dialog.MIN_DATE = new Date();

        if(localStorage.getItem("showEVStartDate") == null){
            start_date_dialog.MIN_DATE = new Date ()
        }else{
            start_date_dialog.MIN_DATE = new Date (localStorage.getItem("showEVStartDate"))
            start_date_dialog.MIN_DATE.setDate(start_date_dialog.MIN_DATE.getDate())

        }
        $('.next-month').off();
        $('.prev-month').off();

        $('.prev-month').click(function () {start_date_dialog.pre_month()});
        $('.next-month').click(function () {start_date_dialog.next_month()});

        start_date_dialog.CURRENT_DATE = new Date();

        if(localStorage.getItem("showEndEVStartDate")===null)
        {


            start_date_dialog.SELECT_DATE = new Date()
        } else{
            var loclaaa=  localStorage.getItem("showEndEVStartDate");
            // console.log( loclaaa)
            // start_date_dialog.CURRENT_DATE =new Date(localStorage.getItem("showH2StartDate"));
            start_date_dialog.SELECT_DATE = new Date(localStorage.getItem("showEndEVStartDate"))
            start_date_dialog.CURRENT_DATE = new Date(localStorage.getItem("showEndEVStartDate"))


            // console.log(start_date_dialog.SELECT_DATE)
        }
            var date =   start_date_dialog.SELECT_DATE.getDate();
            const monthNames = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
            ];
            var month  =  monthNames[start_date_dialog.SELECT_DATE .getMonth()];
            var year = start_date_dialog.SELECT_DATE.getFullYear();
            var select_moth_year  =  month+" "+year
            var date =   start_date_dialog.SELECT_DATE.getDate();
            sessionStorage.setItem("date_check",date);
            sessionStorage.setItem("select_moth_year",select_moth_year);

        if(date == 1){
            start_date_dialog.CURRENT_DATE.setDate(4)
            start_date_dialog.ON_SELECT_FUNC = function(){
                var date = osmanli_calendar.SELECT_DATE;
                localStorage.setItem("showEndEVStartDate",date)
                var start_date = dateToYMDEmpty(date);
                // console.log(start_date)

                // localStorage.setItem("sTransDate",start_date)

                $("#ev_end_date").val(start_date);
                jQuery('#showDateModalFromS').modal('hide');
            }

        }else{
            start_date_dialog.ON_SELECT_FUNC = function(){
                var date = osmanli_calendar.SELECT_DATE;
                localStorage.setItem("showEndEVStartDate",date)
                var start_date = dateToYMDEmpty(date);
                console.log(start_date)

                // localStorage.setItem("sTransDate",start_date)

                $("#ev_end_date").val(start_date);
                jQuery('#showDateModalFromS').modal('hide');
            }
        }


        start_date_dialog.init()
        if(date == 1){
            var table_data =  $(".date_table tbody tr").eq(1)
            table_data.children('td').each(function(){
            var data = $(this).html();
                if(data== 1){
                    $(this).addClass("selected_date")

                }
            })
        }
        jQuery('#showDateModalFromS').modal('show');


        //end showTransStartDate
        var EndDate = new Date();
    }

</script>
@endsection
@extends('common.footer')
