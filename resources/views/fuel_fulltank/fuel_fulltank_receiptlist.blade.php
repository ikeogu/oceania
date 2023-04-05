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
.modal-inside .row {
	margin: 0px;
	color: #fff;
	margin-top: 15px;
	padding: 0px !important;
}
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
<div id="loadOverlay" style="background-color: white; position:absolute; top:0px; left:0px;
    width:100%; height:100%; z-index:2000;">
</div>

<div id="landing-view">
	<!--div id="landing-content" style="width: 100%"-->
	<div class="container-fluid">
		<div class="clearfix"></div>
		<div class="row align-items-center"
			style="display:flex;margin-top:5px;margin-bottom:5px">
			<div class="col-8 d-flex" style="width:70%">
				<h2 style="margin-bottom: 0;">
					Full Tank Receipt List
				</h2>
			</div>

			<div class="col-md-4 d-flex mb-0"
				style="justify-content:flex-end ">
             <form action="{{route('export_excel_fuel_fulltank_receiptlist')}}" method="POST" style="margin-right: 0.5%;">

                    <div style="display:inline;padding-left:0;margin-bottom:20px">
                        <input class="to_date form-control btnremove"
                        style="display:inline;margin-top:10px;padding-top:0px !important;
                        position:relative;top:2px;
                        padding-bottom: 0px; width:110px;padding-right:0;padding-left:0px;
                        text-align: center;"
                        value="Start Date"
                        onclick="ev_start_dialog()"
                        id="ev_start_date" name="fulltank_start_date" placeholder="Select" />
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
                        id="ev_end_date" name="fulltank_end_date" placeholder="Select" />
                    </div>
                    <div style="right:200px;display:inline;
                        padding-left:30px;margin-bottom:20px">

                         <button class="btn btn-success "
					        style="height:70px;width: 70px; border-radius:10px !important;"
					        id="fulltank-rl" style="color:white;">
                            <span style="font-size:13px">
                            Excel
                            </span>
                        </button>
                    </div>

                </form>

			</div>
		</div>

		<div id="response"></div>
		<div id="responseeod"></div>
		<table class="table table-bordered display" id="eodsummarylistd1" style="width:100%;">
			<thead class="thead-dark">
				<tr>
					<th class="text-center" style="width:30px;">No.</th>
					<th class="text-center" style="width:100px;">Date</th>
					<th class="text-center" style="width:auto;">Receipt ID</th>
					<th class="text-center" style="width:100px;">Total</th>
				</tr>
			</thead>

			<tbody>
			</tbody>
		</table>
	</div>
</div>

<div class="modal fade" id="evReceiptDetailModalft" tabindex="-1" role="dialog">
	<div class="modal-dialog  modal-dialog-centered"
		style="width: 366px; margin-top: 0!important;margin-bottom: 0!important;">

		<!-- Modal content-->
		<div class="modal-content  modal-inside detail_view">
		</div>
	</div>
</div>
<div class="modal fade" id="showDateModalFromS" tabindex="-1" role="dialog" aria-labelledby="staffNameLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered  mw-75 w-50" role="document">
    <div class="modal-content modal-inside bg-purplelobster">
      <div class="modal-body text-center" style="min-height: 485px;max-height:485px">
        <div class="row">
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
        <table class="table date_table" >
          <tr style="display: none; ">
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

@section('script')
<script>
$.ajaxSetup({
	headers: {
		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	}
});


function getFuelReceiptlist(data) {
	$.ajax({
			method: "post",
			url: "{{ route('fulltank.envReceipt') }}",
			data: {
				id: data
			}
		}).done((data) => {
			$(".detail_view").html(data);
			$("#evReceiptDetailModalft").modal('show');
		})
		.fail((data) => {
			console.log("data", data)
		});
}

var ft_tbl_url = "{{ route('ft-fuel-list-table') }}";

var ft_tbl_data = {
	'date': '{{ $date }}',
}



var table = $('#eodsummarylistd1').DataTable({

	"processing": false,
	"serverSide": true,
	"autoWidth": false,
	"ajax": {
		"url": ft_tbl_url,
		"type": "POST",
		data: function() {
			 return ft_tbl_data
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
	}],
	"columnDefs": [{
		"width": "3%",
		"targets": [0, 3]
	}, {
		"width": "12%",
		"targets": 1
	}, {
		"className": "dt-center vt_middle",
		"targets": [0, 1, 2, 3]
	}, {
		"className": "vt_middle",
		"targets": [2]
	}, {
		orderable: false,
		targets: [-1]
	}, ],
});


var isVisible;
$(document).ready(function() {
    $("#loadOverlay").css("display","none");

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
				// updateLocalStorageValues();
				log2laravel('info', 'tab eventlistner :back to tab reloaded');
			}

			document_hidden = document[hidden];
		}
	});

	$('#eodsummarylistd1_filter input').attr ('id', 'ft_fuel_search');

	$('#ft_fuel_search').on( 'keyup', function () {
		let sanitized_search = this.value.replace(/[^a-zA-Z0-9]/g, '')
		sanitized_search.toLowerCase
		if (sanitized_search.charAt(0) == 'p') {
			pump_search(sanitized_search)
		} else {
			ptype_search(sanitized_search)
		}

		// console.log(regex.test(str));
	// expected output: true

	} );

});

document.addEventListener("DOMContentLoaded", function(event) {
	window.onstorage = function(e) {
		switch (e.key) {
			case "reload_receipt_list":
				table.ajax.reload();
				log2laravel('info',
					'reload_receipt_list :back to tab reloaded');

				localStorage.removeItem('reload_receipt_list')
				break;
		}
	}
});


function pump_search(search) {

	if (search.length > 4) {
		let pump_no = search.substring(4)

		tbl_url = "{{ route('ft.fuel.search.pump') }}";
		tbl_data = {
			'date': '{{ $date }}',
			'pump_no': pump_no,
		}
		table.ajax.url( tbl_url ).load();
	} else {
		ft_tbl_url = "{{ route('ft-fuel-list-table') }}";
		ft_tbl_data = {
			'date': '{{ $date }}',
		}
		table.ajax.url( ft_tbl_url ).load();
	}
}

function ptype_search(search) {
	if (search.length > 0) {
		let pump_no = search.substring(4)

		ft_tbl_url = "{{ route('ft.fuel.search.ptype') }}";
		ft_tbl_data = {
			'date': '{{ $date }}',
			'ptype': search,
		}
		table.ajax.url( ft_tbl_url ).load();
	} else {
		tbl_url = "{{ route('ft-fuel-list-table') }}";
		tbl_data = {
			'date': '{{ $date }}',
		}
		table.ajax.url( ft_tbl_url ).load();
	}
}
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
