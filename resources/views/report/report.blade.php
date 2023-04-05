@extends('common.web')
@section('styles')
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

#receipt-table_length, #receipt-table_filter,
#receipt-table_info, .paginate_button {
	color: white !important;
}

#eodSummaryListModal-table_paginate, #eodSummaryListModal-table_previous,
#eodSummaryListModal-table_next, #eodSummaryListModal-table_length,
#eodSummaryListModal-table_filter, #eodSummaryListModal-table_info {
	color: white !important;
}

.paging_full_numbers a.paginate_button {
	color: #fff !important;
}

.paging_full_numbers a.paginate_active {
	color: #fff !important;
}

table.dataTable th.dt-right, table.dataTable td.dt-right {
	text-align: right !important;
}
  .btn-custom{
        background-color: #e2e2e2 !important;
        color: #676565 !important;
    }
    .btn-primary{
    background-color: #10094b  !important;
        color: #fff !important;
    }

    .btn-primary:hover{
        background-color: green !important;
        color: #fff !important;
    }
    .btn-custom:hover{
        background-color: green !important;
        color: #fff !important;
    }
    .next-year{
        color: #727272 !important;
    }
    .prev-year{
        color: #727272 !important;
    }
    .noClick {
        pointer-events: none;
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


.btn-custom-enable {
	background-color: #000 !important;
	color: #fff !important;
}

.btn-custom-enable:hover {
	background-color: green !important;
	color: #fff !important;
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
/* .spinner-button {
    border: 2px solid #000;
    display: inline-block;
    padding: 8px 20px 9px;
    font-size: 12px;
    color: #000;
    background-color: transparent
}


.spinner-button:hover {
    background-color: #000;
    border: 2px solid #000;
    color: #fff
}
*/

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
<div id="landing-view">
	<!--div id="landing-content" style="width: 100%"-->
	<div class="container-fluid">
		<div class="clearfix"></div>
		<div class="row py-2 align-items-center" style="display:flex;height:75px">
			<div class="col" style="width:70%">
				<h2 style="margin-bottom: 0;">Report</h2>
			</div>

			<div class="col-md-2 text-right">
				<h5 style="margin-bottom:0;"></h5>
			</div>
		</div>

        <div style="padding-left:2px">
            <form id="form2">

                <h5 class="mb-0">Cost Value Report</h5>
                <hr class="mt-0 mb-2" style="border-color:#c0c0c0">
				<div class=""
					style="
						display: flex;
						align-items: center;
						justify-content: space-between;
						width: 100%;">
					<div class="col-md-2" style="">
	                    <a href="#" onclick='show_dialog15()' id="link"

							style="text-decoration:none">
							Mon Year
						</a>
	                    <input type="hidden" name="month" id="mon">
	                    <input type="hidden" name="year" id="yr">
	                </div>

	                <div class="col-md-10" style="" id="btnFetch">
	                    <button class="btn btn-success bg-download spinner-button"
	                        style="height:70px;width:70px;border-radius:10px;
	                        outline:none;font-size: 14px" onclick="downloadCVPDF()">
                             <span class="d-none spinner-border spinner-border-sm" role="status" aria-hidden="true"  style="z-index:2; position: fixed; margin-top: 3px;
                            margin-left:7px"></span>
                            PDF
	                    </button>
	                </div>
				</div>
            </form>
		</div>

		<div style="padding-left:2px">
			<form id="form1">
				<h5 class="mb-0">C-Store Profit & Loss</h5>
				<hr class="mt-0 mb-2" style="border-color:#c0c0c0">
				<div class=""
					style=" display: flex; align-items: center;
						justify-content: space-between;width: 100%;">

				<div style="" class="pl-0 pr-0 col-md-2">
					<div style="right:200px;display:inline;padding-left:0;
						margin-bottom:20px">
						<input class="to_date form-control btnremove"
						style="display:inline;margin-top:10px;padding-top:0px !important;
						position:relative;top:2px;
						padding-bottom: 0px; width:110px;padding-right:0;padding-left:0px;
						text-align: center;"
						value="Start Date"
						onclick="ev_start_dialog()"
						id="ev_start_date" name="ev_start_date" placeholder="Select" />
					</div>

					To
					<div style="right:200px;display:inline;padding-left:0;
						margin-bottom:20px">
						<input class="to_date form-control btnremove"
						style="display:inline;margin-top:10px;padding-top:0px !important;
						position:relative;top:2px;
						padding-bottom: 0px; width:110px;padding-right:0;
						padding-left:0px; text-align: center;"
						value="End Date"
						onclick="ev_end_dialog()"
						id="ev_end_date" name="ev_end_date" placeholder="Select" />
					</div>
				</div>

				<div class="col-md-10">
					<div style="right:200px;display:inline; margin-bottom:20px" id="btnFetch1">
						<button class="btn btn-success bg-download spinner-button"
							style="height:70px;width:70px;border-radius:10px;
							outline:none;font-size: 14px" onclick="pdfPrintPDF()">
                             <span class="d-none spinner-border spinner-border-sm" role="status" aria-hidden="true"  style="z-index:2; position: fixed; margin-top: 3px;
                            margin-left:7px"></span>
                            PDF
						</button>
					</div>
				</div>
				</div>
			</form>
		</div>


		<div style="padding-left:2px">
			<form id="form3">
				<h5 class="mb-0">C-Store Stock Ledger</h5>
				<hr class="mt-0 mb-2" style="border-color:#c0c0c0">
				<div class=""
					style=" display: flex; align-items: center;
						justify-content: space-between;width: 100%;">

				<div style="" class="pl-0 pr-0 col-md-2">
					<div style="right:200px;display:inline;padding-left:0;
						margin-bottom:20px">
						<input class="to_date form-control btnremove"
						style="display:inline;margin-top:10px;padding-top:0px !important;
						position:relative;top:2px;
						padding-bottom: 0px; width:110px;padding-right:0;padding-left:0px;
						text-align: center;"
						value="Start Date"
						onclick="sl_start_dialog()"
						id="sl_start_date" name="sl_start_date" placeholder="Select" />
					</div>

					To
					<div style="right:200px;display:inline;padding-left:0;
						margin-bottom:20px">
						<input class="to_date form-control btnremove"
						style="display:inline;margin-top:10px;padding-top:0px !important;
						position:relative;top:2px;
						padding-bottom: 0px; width:110px;padding-right:0;
						padding-left:0px; text-align: center;"
						value="End Date"
						onclick="sl_end_dialog()"
						id="sl_end_date" name="sl_end_date" placeholder="Select" />
					</div>
				</div>

				<div class="col-md-10">
					<div style="right:200px;display:inline; margin-bottom:20px " id="btnFetch2">

                        <button class="btn btn-success screend-button spinner-button"
                            style="font-size: 14px"  onclick="downloadExcelSL()">
                            <span class="d-none spinner-border spinner-border-sm" role="status" aria-hidden="true" style="z-index:2; position: fixed; margin-top: 12px;
                            margin-left:10px"></span>
                            Stock<br>Ledger

                        </button>

					</div>
				</div>
				</div>
				</div>
			</form>
		</div>

	</div>
</div>

{{-- <div class="modal " ><!-- Place at bottom  of page --></div> --}}

    <div class="clearfix"></div>
    <br><br>

    <div class="modal fade" id="showDateModalFrom" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document"
            style="min-width: 700px;">
              <?php
                $months=\App\Classes\Helper::monthList();
                    $nowM = date("M");
                    $year = date('Y');
                    $now = date("m")-1;
                    $b = `document.getElementById('year_cal').textContent`;

            ?>
            <div class="modal-content bg-purplelobster">
                <div class="modal-header align-items-center">
                <h3 class="mb-0 modal-title">Month</h3>

                <div class="row" style="margin-right: 15px;align-items: center;">
                    <div class="col-md-2">
                        <i class="prev-year fa fa-chevron-left fa-3x"
                        style="cursor:pointer;" onclick="prev_month()"></i>
                    </div>
                    <div class="col-md-8" style="transform: translateX(9%);">
                        <div class="year text-center text-white" id="main_year">
                            <h3 class="mb-0" id="year_cal">2020</h3>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <i style="cursor:pointer"
                            class="fa fa-chevron-right fa-3x "
                            onclick="nxt_month()">
                        </i>
                    </div>
                </div>
                </div>
                <div style="padding:20px !important">

                <div class="row" style="justify-content:center">

                    <ul name="discountItemtLevel" id="discountItemtLevelId"
                        style="margin-bottom:0;padding-left:40px;padding-right:35px"
                        class="discountItemtLevel">

                @foreach ($months as $key=>$mm)

                     @if($now > $key && $now != $key)
                    <li class="btn  btn-log btn-primary discountbutton yearBtn {{ $mm['sort_name'] }}"
                        style= "padding-left:0;padding-right:0;padding-top:20px; cursor:pointer"
                        onclick="select_month('{{ $mm["sort_name"] }}')" value="{{ $mm['sort_name'] }}" id="past">

                        <span>{{ $mm['sort_name'] }}</span>

                    </li>
                    @elseif($mm["sort_name"]== $nowM && $key == $now)
                     <li class="btn  btn-log btn-success  discountbutton yearBtn {{ $mm['sort_name'] }}"
                        style= "padding-left:0;padding-right:0;padding-top:20px; cursor:pointer"
                        onclick="select_month('{{ $mm["sort_name"] }}')" value="{{ $mm['sort_name'] }}" id="present">

                        <span>{{ $mm['sort_name'] }}</span>

                    </li>
                    @else
                         <li class="btn  btn-log btn-custom discountbutton yearBtn {{ $mm['sort_name'] }}"
                        style= "padding-left:0;padding-right:0;padding-top:20px; pointer-events: none;color:#5f5656"
                        onclick="select_month('{{ $mm["sort_name"] }}')" value="{{ $mm['sort_name'] }}" id="future">

                        <span>{{ $mm['sort_name'] }}</span>

                    </li>
                    @endif


                @if ($key== 5)
                    </ul>
                </div>

                <div class="row" style="justify-content:center">
                    <ul name="discountItemtLevel" id="discountItemtLevelId"
                        style="padding-left:40px;padding-right:35px"
                        class="discountItemtLevel">
                @endif

                @endforeach
                    </ul>
                </div>
                <input type="hidden" id='product_id' value=""/>
            </div>
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

<script type="text/javascript">
    $(function() {
        $(document).ready(function () {
            dateLoader();

            $('#btnFetch').on('click', f => {
                let spinner = $(f.currentTarget).find('span')
                spinner.removeClass('d-none')
                setTimeout(_ => spinner.addClass('d-none'), 12000)
            })



            var todaysDate = new Date(); // Gets today's date
            // Max date attribute is in "YYYY-MM-DD".
            // Need to format today's date accordingly
            var year = todaysDate.getFullYear(); // YYYY
            var month = ("01");  // MM
            var day = ("01");           // DD
            var minDate = (year +"-"+ month );
            //  +"-"+ display Results in "YYYY-MM" for today's date
            // Now to set the max date value for the calendar to be today's date
            $('#startDate').attr('min',minDate);

			var curent = $('#link').html();

			$('#mon').val(curent.split(' ')[0]);
			$('#yr').val(curent.split(' ')[1]);
        });
    });

    @yield('current_year')

    function show_dialog2(ogFuelId) {
        jQuery('#showDateModal').modal('show');
        $("#ogFuelPriceId").val(ogFuelId);
    }

    var start_date_dialog = osmanli_calendar;
    var completion_date_dialog = osmanli_calendar;
    var terminal_date;

    store_date = dateToYMDEmpty(new Date());
    $("#ev_start_date").val(store_date);
    $("#ev_end_date").val(store_date);
    $("#cs_start_date").val(store_date);
    $("#cs_end_date").val(store_date);
    $("#sl_start_date").val(store_date);
    $("#sl_end_date").val(store_date);



    localStorage.removeItem("showEVStartDate")
    localStorage.removeItem("showEndEVStartDate")
    localStorage.removeItem("showCSSStartDate")
    localStorage.removeItem("showCSSEndDate")
    localStorage.removeItem("showSLStartDate")
    localStorage.removeItem("showSLEndDate");


    function dateLoader(){
        date = new Date();
        start_date_dialog.MAX_DATE = date;
        start_date_dialog.DAYS_DISABLE_MIN = "ON";
        start_date_dialog.DAYS_DISABLE_MAX = "ON";
        start_date_dialog.MIN_DATE = new Date('{{$approved_at}}');
        start_date_dialog.CURRENT_DATE = new Date();
        if(localStorage.getItem("showCSSStartDate")===null) {
            start_date_dialog.SELECT_DATE = new Date()
        } else{
            var loclaaa=  localStorage.getItem("showCSSStartDate");
            // console.log( loclaaa)
            // start_date_dialog.CURRENT_DATE =new Date(localStorage.getItem("showH2StartDate"));
            start_date_dialog.SELECT_DATE = new Date(localStorage.getItem("showCSSStartDate"))
            start_date_dialog.CURRENT_DATE = new Date(localStorage.getItem("showCSSStartDate"))
        }


            var date =   start_date_dialog.SELECT_DATE.getDate();
            const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
            "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
            ];
            var month  =  monthNames[start_date_dialog.SELECT_DATE .getMonth()];
            let year = start_date_dialog.SELECT_DATE.getFullYear();
            var select_moth_year  =  month+" "+year
            var date =   start_date_dialog.SELECT_DATE.getDate();
            sessionStorage.setItem("date_check",date);
            sessionStorage.setItem("select_moth_year",select_moth_year);
            let dy = document.getElementById("year_cal");
            let link =document.getElementById("link");
            dy.textContent = year;
            link.textContent = select_moth_year;
    }

    function sum(input) {
        var total = 0;
        for (var i = 0; i < input.length; i++) {
            total += Number(input[i]);
        }
        return total;
    }


    function show_dialog15(e){
        sessionStorage.removeItem("modalTrue");
        sessionStorage.setItem("modalTrue",'showTransStartDate');

        date = new Date();
        start_date_dialog.MAX_DATE = date;
        start_date_dialog.DAYS_DISABLE_MIN = "ON";
        start_date_dialog.DAYS_DISABLE_MAX = "ON";
        start_date_dialog.MIN_DATE = new Date('{{ $approved_at }}');

        $('.next-year').click(function () {start_date_dialog.next_year()});
        $('.prev-year').click(function () {start_date_dialog.pre_year()});

        start_date_dialog.CURRENT_DATE = new Date();

        if(localStorage.getItem("showCSSStartDate")===null)
        {
            start_date_dialog.SELECT_DATE = new Date()
        } else{
            var loclaaa=  localStorage.getItem("showCSSStartDate");

            start_date_dialog.SELECT_DATE = new Date(localStorage.getItem("showCSSStartDate"))
            start_date_dialog.CURRENT_DATE = new Date(localStorage.getItem("showCSSStartDate"))

        }

        var date =   start_date_dialog.SELECT_DATE.getDate();
        const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
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
                localStorage.setItem("showCSSStartDate",date)
                var start_date = dateToYMDEmpty(date);
                // console.log(start_date)

                // localStorage.setItem("sTransDate",start_date)

                $("#cs_start_date").val(start_date);
                jQuery('#showDateModalFrom').modal('hide');
            }

        }else{
            start_date_dialog.ON_SELECT_FUNC = function(){
                var date = osmanli_calendar.SELECT_DATE;
                localStorage.setItem("showCSSStartDate",date)
                var start_date = dateToYMDEmpty(date);
                console.log(start_date)

                // localStorage.setItem("sTransDate",start_date)

                $("#cs_start_date").val(start_date);
                jQuery('#showDateModalFrom').modal('hide');
            }
        }

        // console.log(start_date_dialog)
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
        jQuery('#showDateModalFrom').modal('show');

        //end showTransStartDate
        var EndDate = new Date();
    }

    // Ev Dialog
    function sl_start_dialog(e) {
        // alert("yessss")
        sessionStorage.removeItem("modalTrue");
        sessionStorage.setItem("modalTrue",'showSLStartDate');

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

        if(localStorage.getItem("showSLStartDate")===null)
        {
            start_date_dialog.SELECT_DATE = new Date()
        } else{
            var loclaaa=  localStorage.getItem("showSLStartDate");
            // console.log( loclaaa)
            // start_date_dialog.CURRENT_DATE =new Date(localStorage.getItem("showH2StartDate"));
            start_date_dialog.SELECT_DATE = new Date(localStorage.getItem("showSLStartDate"))
            start_date_dialog.CURRENT_DATE = new Date(localStorage.getItem("showSLStartDate"))


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
                localStorage.setItem("showSLStartDate",date)
                var start_date = dateToYMDEmpty(date);
                // console.log(start_date)

                // localStorage.setItem("sTransDate",start_date)

                $("#sl_start_date").val(start_date);
                jQuery('#showDateModalFromS').modal('hide');
            }

        }else{
            start_date_dialog.ON_SELECT_FUNC = function(){
                var date = osmanli_calendar.SELECT_DATE;
                localStorage.setItem("showSLStartDate",date)
                var start_date = dateToYMDEmpty(date);
                console.log(start_date)

                // localStorage.setItem("sTransDate",start_date)

                $("#sl_start_date").val(start_date);
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
    function sl_end_dialog(e) {
        // alert("yessss")
        sessionStorage.removeItem("modalTrue");
        sessionStorage.setItem("modalTrue",'showTransStartDate');

        date = new Date();
        start_date_dialog.MAX_DATE = date;
        start_date_dialog.DAYS_DISABLE_MIN = "ON";
        start_date_dialog.DAYS_DISABLE_MAX = "ON";
        //start_date_dialog.MIN_DATE = new Date();

        if(localStorage.getItem("showSLStartDate") == null){
            start_date_dialog.MIN_DATE = new Date ()
        }else{
            start_date_dialog.MIN_DATE = new Date (localStorage.getItem("showSLStartDate"))
            start_date_dialog.MIN_DATE.setDate(start_date_dialog.MIN_DATE.getDate())

        }
        $('.next-month').off();
        $('.prev-month').off();

        $('.prev-month').click(function () {start_date_dialog.pre_month()});
        $('.next-month').click(function () {start_date_dialog.next_month()});

        start_date_dialog.CURRENT_DATE = new Date();

        if(localStorage.getItem("showSLEndDate")===null)
        {
            start_date_dialog.SELECT_DATE = new Date()
        } else{
            var loclaaa=  localStorage.getItem("showSLEndDate");
            // console.log( loclaaa)
            // start_date_dialog.CURRENT_DATE =new Date(localStorage.getItem("showH2StartDate"));
            start_date_dialog.SELECT_DATE = new Date(localStorage.getItem("showSLEndDate"))
            start_date_dialog.CURRENT_DATE = new Date(localStorage.getItem("showSLEndDate"))

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
                localStorage.setItem("showSLEndDate",date)
                var start_date = dateToYMDEmpty(date);
                // console.log(start_date)

                // localStorage.setItem("sTransDate",start_date)

                $("#sl_end_date").val(start_date);
                jQuery('#showDateModalFromS').modal('hide');
            }

        }else{
            start_date_dialog.ON_SELECT_FUNC = function(){
                var date = osmanli_calendar.SELECT_DATE;
                localStorage.setItem("showSLEndDate",date)
                var start_date = dateToYMDEmpty(date);
                console.log(start_date)

                // localStorage.setItem("sTransDate",start_date)

                $("#sl_end_date").val(start_date);
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



    function dateToYMDEmpty(date) {
        var strArray=['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        var d = date.getDate();
        var m = strArray[date.getMonth()];
        var y = date.getFullYear().toString().substr(-2);
        var currentHours = date.getHours();
        return '' + (d <= 9 ? '0' + d : d) + '' + m + '' + y ;
    }

    var end_date_dialog = osmanli_calendar;

    function show_dialog5() {

        // alert("yessss")
        sessionStorage.removeItem("modalTrue");
        sessionStorage.setItem("modalTrue",'showTransStartDate');

        date = new Date();
        start_date_dialog.MAX_DATE = date;
        start_date_dialog.DAYS_DISABLE_MIN = "ON";
        start_date_dialog.DAYS_DISABLE_MAX = "ON";
        //start_date_dialog.MIN_DATE = new Date();


        if(localStorage.getItem("showH2StartDate") == null){
            start_date_dialog.MIN_DATE = new Date ()
        }else{
            start_date_dialog.MIN_DATE = new Date (localStorage.getItem("showH2StartDate"))
            start_date_dialog.MIN_DATE.setDate(start_date_dialog.MIN_DATE.getDate())

        }
        $('.next-month').off();
        $('.prev-month').off();

        $('.prev-month').click(function () {start_date_dialog.pre_month()});
        $('.next-month').click(function () {start_date_dialog.next_month()});

        start_date_dialog.CURRENT_DATE = new Date();

        if(localStorage.getItem("endH2Date")===null)
        {


            start_date_dialog.SELECT_DATE = new Date()
        } else{
            var loclaaa=  localStorage.getItem("endH2Date");
            // console.log( loclaaa)
            // start_date_dialog.CURRENT_DATE =new Date(localStorage.getItem("showH2StartDate"));
            start_date_dialog.SELECT_DATE = new Date(localStorage.getItem("endH2Date"))
            start_date_dialog.CURRENT_DATE = new Date(localStorage.getItem("endH2Date"))


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
                localStorage.setItem("endH2Date",date)
                var start_date = dateToYMDEmpty(date);
                // console.log(start_date)

                // localStorage.setItem("sTransDate",start_date)

                $("#h2_end_date").val(start_date);
                jQuery('#showDateModalFromS').modal('hide');
            }

        }else{
            start_date_dialog.ON_SELECT_FUNC = function(){
                var date = osmanli_calendar.SELECT_DATE;
                localStorage.setItem("endH2Date",date)
                var start_date = dateToYMDEmpty(date);
                console.log(start_date)

                // localStorage.setItem("sTransDate",start_date)

                $("#h2_end_date").val(start_date);
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


        function show_dialog5_oew() {

            // alert("yessss")
            sessionStorage.removeItem("modalTrue");
            sessionStorage.setItem("modalTrue",'showTransStartDate');

            date = new Date();
            start_date_dialog.MAX_DATE = date;
            start_date_dialog.DAYS_DISABLE_MIN = "ON";
            start_date_dialog.DAYS_DISABLE_MAX = "ON";
            //start_date_dialog.MIN_DATE = new Date();


            if(localStorage.getItem("showOewStartDate") == null){
                start_date_dialog.MIN_DATE = new Date ()
            }else{
                start_date_dialog.MIN_DATE = new Date (localStorage.getItem("showOewStartDate"))
                start_date_dialog.MIN_DATE.setDate(start_date_dialog.MIN_DATE.getDate())

            }
            $('.next-month').off();
            $('.prev-month').off();

            $('.prev-month').click(function () {start_date_dialog.pre_month()});
            $('.next-month').click(function () {start_date_dialog.next_month()});

            start_date_dialog.CURRENT_DATE = new Date();

            if(localStorage.getItem("endOewDate")===null)
            {


                start_date_dialog.SELECT_DATE = new Date()
            } else{
                var loclaaa=  localStorage.getItem("endOewDate");
                // console.log( loclaaa)
                // start_date_dialog.CURRENT_DATE =new Date(localStorage.getItem("showH2StartDate"));
                start_date_dialog.SELECT_DATE = new Date(localStorage.getItem("endOewDate"))
                start_date_dialog.CURRENT_DATE = new Date(localStorage.getItem("endOewDate"))


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
                    localStorage.setItem("endOewDate",date)
                    var start_date = dateToYMDEmpty(date);
                    // console.log(start_date)

                    // localStorage.setItem("sTransDate",start_date)

                    $("#oew_end_date").val(start_date);
                    jQuery('#showDateModalFromS').modal('hide');
                }

            }else{
                start_date_dialog.ON_SELECT_FUNC = function(){
                    var date = osmanli_calendar.SELECT_DATE;
                    localStorage.setItem("endOewDate",date)
                    var start_date = dateToYMDEmpty(date);
                    console.log(start_date)

                    // localStorage.setItem("sTransDate",start_date)

                    $("#oew_end_date").val(start_date);
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


    function onDateSelect_to(selectedDate) {
        if (selectedDate == null) {
            return false;
        }

        const todaysDate = new Date();
        var selectedFinalDate = (selectedDate.getDate() < 10 ? '0' : '') + selectedDate.getDate();
        var selectedFullYear = selectedDate.getFullYear().toString();
        selectedFullYear = selectedFullYear.match(/\d{2}$/);
        $('#date_to').val(selectedFinalDate + selectedDate.toLocaleString('en-us',
        {month: 'short'}) + selectedFullYear);
        jQuery('#showDateModalFromS').modal('hide');
        date_filter();
    }

    function dateLoader(){
        date = new Date();
        start_date_dialog.MAX_DATE = date;
        var date =   start_date_dialog.SELECT_DATE.getDate();
        const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
            "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
            ];
            var month  =  monthNames[start_date_dialog.SELECT_DATE .getMonth()];
            var year = start_date_dialog.SELECT_DATE.getFullYear();
            var select_moth_year  =  month+" "+year
            var date =   start_date_dialog.SELECT_DATE.getDate();
            let dy = document.getElementById("year_cal");
            let link =document.getElementById("link");
            dy.textContent = year;
            link.textContent = select_moth_year;
    }
    // pass info
    function select_month(month){
        // document.getElementById('mon').value = month;
        // document.getElementById('yr').value = document.getElementById("year_cal").textContent;
        // document.getElementById('link').textContent = document.getElementById('mon').value + " "+document.getElementById('yr').value;

        var cdate = $('#link').html();


        $("." + cdate.split(' ')[0]).addClass("btn-custom-enable");
        $("." + cdate.split(' ')[0]).addClass("btn-primary");
        $("." + cdate.split(' ')[0]).removeClass("btn-success");
        $("." + cdate.split(' ')[0]).css("font-weight", " normal");

        var currYear = $("#main_year").find("h3").text();
        $('#link').html(month + ' ' + currYear);
        var cdate = $('#link').html();
        $("." + cdate.split(' ')[0]).removeClass("btn-custom-enable");
        $("." + cdate.split(' ')[0]).addClass("btn-success");
        $("." + cdate.split(' ')[0]).removeClass("btn-primary");
        $("." + cdate.split(' ')[0]).css("font-weight", " bold");

        $('#mon').val(cdate.split(' ')[0]);
        $('#yr').val(cdate.split(' ')[1]);
        // alert( dt.rows().count());
        // rowcheck()
        // if (fuel_prod_id) {
        //  getMainTable()
        // }
    }



    function rowcheck() {
        var cdate = $('#link').html();
        var fields = cdate.split(' ');
        var month = fields[0];
        var currYear = fields[1];
        var monthArray = 'Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec'.split(' ');

        month = monthArray.indexOf(month);
        var month = month + 1;
        var year = currYear;
        for (var i = 31; i >= 0; i--) {
            dt.row(i).remove().draw();
        }

        for (var i = dt.rows().count() + 1; i <= daysInMonth(month, year); i++) {
            if (i < 10) {
                day_custom = `0${i}`;
            } else {
                day_custom = i;
            }
            dt.row.add([i, day_custom + "" + monthArray[month - 1] + "" + year.slice(-2), "", "", "", "", "", "", "", ""
            ]).draw();
        }

        for (var i = dt.rows().count(); i >= daysInMonth(month, year); i--) {
            dt.row(i).remove().draw();
        }
    }

    function prev_month(){
    let  min_date = new Date('{{ $approved_at }}');
    if(min_date.getFullYear() == document.getElementById("year_cal").textContent)
        document.getElementById("year_cal").textContent ;
        else{
            document.getElementById("year_cal").textContent -= 1;

        }
        $('#present').removeClass("btn-success");
        $('#present').addClass("btn-primary");
        $('#past').removeClass("btn-primary");
        $('#past').addClass("btn-custom");
        $('#future').removeClass("btn-custom");
        $('#future').addClass("btn-primary");
        console.log( document.getElementById("present").textContent)
    }

    function nxt_month(){

        let  bn= document.getElementById("year_cal").textContent  ;
        document.getElementById("year_cal").textContent =parseInt(bn) +  1;
        $('#present').removeClass("btn-success");
        $('#present').addClass("btn-primary");
        $('#past').removeClass("btn-primary");
        $('#past').addClass("btn-custom");
        $('#future').removeClass("btn-custom");
        $('#future').addClass("btn-primary");

    }

    function createCookie(name, value, days) {
    var expires;
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
    }
    else {
        expires = "";
    }
    document.cookie = escape(name) + "=" + escape(value) + expires + "; path=/";
    }

    var CURRENT_DATE = new Date();
    var d = new Date();

    var content = 'January February March April May June July August September October November December'.split(' ');
    var contentMonth = '1 2 3 4 5 6 7 8 9 10 11 12'.split(' ');
    var weekDayName = 'SUN MON TUES WED THURS FRI'.split(' ');
    var daysOfMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

    // Returns the day of week which month starts (eg 0 for Sunday, 1 for Monday, etc.)
    function getCalendarStart(dayOfWeek, currentDate) {
        var date = currentDate - 1;
        var startOffset = (date % 7) - dayOfWeek;
        if (startOffset > 0) {
            startOffset -= 7;
        }
        return Math.abs(startOffset);
    }

    // Render Calendar
    function renderCalendar(startDay, totalDays, currentDate) {
        var currentRow = 1;
        var currentDay = startDay;
        var $table = $('table');
        var $week = getCalendarRow();
        var $day;
        var i = 1;

        for (; i <= totalDays; i++) {
            $day = $week.find('td').eq(currentDay);
            $day.text(i);
            if (i === currentDate) {
                $day.addClass('today');
            }

            // +1 next day until Saturday (6), then reset to Sunday (0)
            currentDay = ++currentDay % 7;

            // Generate new row when day is Saturday, but only if there are
            // additional days to render
            if (currentDay === 0 && (i + 1 <= totalDays)) {
                $week = getCalendarRow();
                currentRow++;
            }
        }
    }

    // Clear generated calendar
    var ACTIVE_DATE  = [];

    function clearCalendar() {
        if($('td.selected_date').length){
             ACTIVE_DATE  = [];
            ACTIVE_DATE.push($('td.selected_date').text());
            ACTIVE_DATE.push($('#currMonth').val());
            // console.log(ACTIVE_DATE);
        }
        var $trs = $('.picker tr').not(':eq(0)');
        $trs.remove();
        $('.month-year').empty();
    }
    // Generates table row used when rendering Calendar
    function getCalendarRow() {
        var $table = $('table.date_table');
        var $tr = $('<tr/>');
        for (var i = 0, len = 7; i < len; i++) {
            $tr.append($('<td/>'));
        }
        $table.append($tr);
        return $tr;
    }

    function myCalendar() {
        var month = d.getUTCMonth();
        var day = d.getUTCDay();
        var year = d.getUTCFullYear();
        var date = d.getUTCDate();
        var totalDaysOfMonth = daysOfMonth[month];
        var counter = 1;

        var $h3 = $('<h3>');

        $h3.html(content[month] + ' ' + year );
        $h3.appendTo('.month-year');
        var $div = $('<div>');
        $div.html('<input type="hidden" id="currMonth" name="currMonth" value="'+contentMonth[month]+'">');
        $div.appendTo('.month-year');

        var dateToHighlight = 0;

        // Determine if Month && Year are current for Date Highlight
        if (CURRENT_DATE.getUTCMonth() === month &&
            CURRENT_DATE.getUTCFullYear() === year) {

            dateToHighlight = date;
        }

        //Getting February Days Including The Leap Year
        if (month === 1) {
            if ((year % 100 !== 0) && (year % 4 === 0) || (year % 400 === 0)) {
                totalDaysOfMonth = 29;
            }
        }

        // Get Start Day
        renderCalendar(getCalendarStart(day, date), totalDaysOfMonth,
            dateToHighlight);
    };

    // download files
    function pdfPrintPDF(){


        let spinner = $('#btnFetch1').find('span')
            spinner.removeClass('d-none')

        $("#form1").submit(e =>{
            e.preventDefault();
        })
        var startDate = $('#ev_start_date').val();
        var endDate = $('#ev_end_date').val();

        data = {
			"ev_start_date":startDate,
			"ev_end_date":endDate
        };
		console.log('****-----the print pdf')
        console.log(data);

        $.ajax({
			url: "{{route('c-storePL')}}",
            type: "POST",
            'headers': {
			  'X-CSRF-TOKEN': '{{ csrf_token() }}'
			},
            data:data,
                xhrFields: {
                responseType: 'blob'
            },
            success: function(response){
                var blob = new Blob([response]);
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = "Cstore-Profit&Loss.pdf";
                link.click();
                spinner.addClass('d-none')
            },
            error: function(blob){
                console.log(blob);
            }
		})



    }

    function downloadExcelSL(){

        let spinner = $('#btnFetch2').find('span')
            spinner.removeClass('d-none')

        $("#form3").submit(e =>{
            e.preventDefault();
        })
        var startDate = $('#sl_start_date').val();
        var endDate = $('#sl_end_date').val();

        data = {
			"sl_start_date":startDate,
			"sl_end_date":endDate
        };
		console.log('****-----the print pdf')
        console.log(data);

        $.ajax({
			url: "{{route('export_stock_ledger_excel')}}",
            type: "POST",
            'headers': {
			  'X-CSRF-TOKEN': '{{ csrf_token() }}'
			},
            data:data,
                xhrFields: {
                responseType: 'blob'
            },
            success: function(response){
                var blob = new Blob([response]);
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = "StockLedger.xlsx";
                link.click();
                spinner.addClass('d-none')
            },
            error: function(blob){
                console.log(blob);
            }
		})



    }

    function downloadCVPDF(){

        let spinner = $('#btnFetch').find('span')
            spinner.removeClass('d-none')

        $("#form2").submit(e =>{
            e.preventDefault();
        })
        var mon = $('#mon').val();
        var yr = $('#yr').val();

        data = {
			"mon":mon,
			"yr":yr
        };
		console.log('****-----the print pdf')
        console.log(data);

        $.ajax({
			url: "{{route('cost_value_report')}}",
            type: "POST",
            'headers': {
			  'X-CSRF-TOKEN': '{{ csrf_token() }}'
			},
            data:data,
                xhrFields: {
                responseType: 'blob'
            },
            success: function(response){
                var blob = new Blob([response]);
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = "CostValueReport.pdf";
                link.click();
                spinner.addClass('d-none')
            },
            error: function(blob){
                console.log(blob);
            }
		})



    }

    function navigationHandler(dir) {
        d.setUTCMonth(d.getUTCMonth() + dir);
        clearCalendar();
        myCalendar();
        shoot_event();
    }


    $(document).ready(function() {
        // Bind Events
        $('.prev-month').click(function() {
            if($(this).hasClass('disabled')) return false;
            navigationHandler(-1);
        });
        $('.next-month').click(function() {
            navigationHandler(1);
        });
        // Generate Calendar
        myCalendar();
        shoot_event();
    });

    var CURRENT_DATE = new Date();
    function shoot_event () {
        var year = d.getUTCFullYear();
        var currentMOnth = parseInt($('#currMonth').val()) -1;

        // if(currentMOnth ==  CURRENT_DATE.getMonth()  && year <=CURRENT_DATE.getFullYear()){
        //     $('.prev-month').addClass('disabled');
        //     $('.prev-month').css("cursor", "not-allowed");
        // } else{
        //     $('.prev-month').removeClass('disabled');
        //     $('.prev-month').css("cursor", "default");
        // }

        $('.date_table > tbody > tr > td').click(function(e) {
            console.log("Date clicked");

            var target = e.target;
            $('.date_table > tbody > tr > td').removeClass('selected_date');

            $(target).addClass('selected_date');

            var act = {"day" : $(this).text(), "month" : $('#currMonth').val() , "year" : year};
            sessionStorage.setItem('activeDate', JSON.stringify(act));

            let day = $(target).html();
            let month  = $('.month-year > h3').html();
            $('#startDate').val(day+' '+month);
            jQuery('#showDateModal').modal('hide');
        });

        $('.date_table tbody tr td').each(function () {
            if(ACTIVE_DATE.length){
                if($(this).text() == ACTIVE_DATE[0] &&
                    $('#currMonth').val() == ACTIVE_DATE[1] &&
                    year <=CURRENT_DATE.getFullYear()){

                    $(this).addClass('selected_date');
                }
            }
            var s = sessionStorage.getItem('activeDate');
            if(s != null || s != undefined){
                s = JSON.parse(s);
                //console.log(s);
                if(s.day == $(this).text() &&
                    s.month == $('#currMonth').val() && s.year == year) {
                    $(this).addClass('selected_date');
                }
            }

            // var currentMOnth = parseInt($('#currMonth').val()) -1;

            if ((parseInt($(this).text()) < CURRENT_DATE.getDate() &&
                currentMOnth <=  CURRENT_DATE.getMonth() &&
                year <=CURRENT_DATE.getFullYear())){

                // $(this).closest('td').addClass('disabled');
                // // $(this).closest('tr').css("pointer-events", "none");
                // $(this).closest('td').css("cursor", "not-allowed");
                // $(this).closest('td').unbind('click');
            }
        });
    }


    function overideFY() {
        $('#overide').val('true');
        //onDateSelect();
    }


    function reset_dialog() {
        $('#confirmation').val('false');
        $('#overide').val('false');
    }


    function onDateSelect() {
		const val = $('#startDate').val();
		const selectedDate = new Date(val);

		if (selectedDate == 'Invalid Date') {
			return false;
		}

		const todaysDate = new Date();
		$('#date_from').val(selectedDate.getDate()+selectedDate.toLocaleString('en-us',
			{ month: 'short' })+selectedDate.getFullYear().toString().substr(2,2));

		//  $('#from_year').html(selectedDate.getFullYear()+ ' from');

		if (todaysDate.getFullYear() > selectedDate.getFullYear()) {
			alert('Error: You can only select from this year!');
			$('#startDate').val('');
			return false;

		} else {
			console.log("date")
		}
	}

    $(".modal-body div:first").on("click" , function(){
		var change_month_year = $(".modal-body div:first .col-md-8 .month-year h3").html()
		var select_moth_year = sessionStorage.getItem("select_moth_year");
		var date = sessionStorage.getItem("date_check");

		if(date == 1){
			if(change_month_year  == select_moth_year ){
			var table_data =  $(".date_table tbody tr").eq(1)
			table_data.children('td').each(function(){
				var data = $(this).html();
					if(data== 1){
						$(this).addClass("selected_date")

					}
				})

			}else{
				$(".selected_date").removeClass("selected_date")

			}
		}
	})

    /* function spinner() {
        $("body").css("background-color","#e1e1e1");
        setInterval("$('body').toggleClass('loading')", 4000);
    } */

</script>

@endsection
@extends('common.footer')
