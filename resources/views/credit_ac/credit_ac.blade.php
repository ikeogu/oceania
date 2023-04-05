@extends('common.web')
@include('common.header')

@section('styles')
<style>
button:focus{
	outline:none !important;
}

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
 a:hover, a:visited, a:link, a:active{
     text-decoration: none;
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


.typewriter {
    text-align: right;
    border-radius: 3px;
    overflow: hidden; /* Ensures the content is not revealed until the animation */
    /* border-right: .15em solid black;*/ /* The typwriter cursor */
    white-space: nowrap; /* Keeps the content on a single line */
    margin: 0 auto; /* Gives that scrolling effect as the typing happens */
    letter-spacing: .0em; /* Adjust as needed */
    /* animation:
      typing 3.5s steps(40, end),
      blink-caret .75s step-end infinite;*/
}

#showDateModalFrom .date_table > tbody > tr > th {
	font-size: 22px;
	color: white;
	background-color: rgba(255, 255, 255, 0.5);
}

#showDateModalFrom .date_table > tbody > tr > td {
	color: #fff;
	font-weight: 600;
	border: unset;
	font-size: 20px;
	cursor: pointer;
}

#showDateModalFrom table.dataTable tbody td{
	border-left: 1px solid #dee2e6;
	border-right: 1px solid #dee2e6;
	border-top: none;
	border-bottom: none;
}

#showDateModalFrom .btn-green {
	background-color: green !important;
	color: #fff !important;
	box-shadow: none !important;
	border: 0px !important;
}

#showDateModalFrom .btn-green:focus {
	background-color: green !important;
	color: #fff !important;
	box-shadow: none !important;
	border: 0px !important;
}

#showDateModalFrom .bg-blue {
	background-color: #007bff;
	color: #fff;
}

#showDateModalFrom .date_table1 > tbody > tr > th {
	font-size: 22px;
	color: white;
	background-color: rgba(255, 255, 255, 0.5);
}

#showDateModalFrom .date_table1 > tbody > tr > td {
	color: #fff;
	font-weight: 600;
	border: unset;
	font-size: 20px;
	cursor: pointer;
}

#showDateModalFrom .selected_date {
	color: #008000 !important;
	font-weight: bold !important;
}

#showDateModalFrom .selected_date1 {
	color: #008000 !important;
	font-weight: bold !important;
}

#showDateModalFrom #Datepick .d-table {
	display: -webkit-flex !important;
	display: -ms-flexbox !important;
	display: flex !important;
}

#showDateModalFrom .dataTables_filter input {
	width: 300px;
}

#showDateModalFrom .greenshade {
	height: 30px;
	background-color: green; /* For browsers that do not support gradients */
	background-image: linear-gradient(-90deg, green, white); /* Standard syntax (must be last) */
}
#showDateModalFrom .dt-button{
	display: none;
}

#showDateModalFrom .bg-purplelobster{
	color:white;
	border-color:rgba(0,0,255,0.5);
	background-color:rgba(0,0,255,0.5)
}

/*//for calender short day*/
#showDateModalFrom .shortDay ul{
	llist-style: none;
	background-color: rgba(255, 255, 255, 0.5);
	position: relative;
	left: -75px;
	width: 124%;
	height: 55px;
	line-height: 42px;

 }
#showDateModalFrom .shortDay ul > li{
  font-size: 22px;
  color: white;
  font-weight: 700 !important;
  /* background-color: #2b1f1f; */
  padding: 5px 24px;
  text-align: left !important;
 }
 #showDateModalFrom  .list-inline-item:not(:last-child){
	margin-right: 0 !important;
}
#showDateModalFrom .modal-content{
	overflow: hidden;
}
#showDateModalFrom .modal-inside .row {
	margin: 0px;
	color: #fff;
	margin-top: 15px;
	padding: 0px !important;
}
#showDateModalFrom .selected-button {
	background-color: green;;
	color: #fff;
}

#showDateModalFrom .selected-button:hover {
	color: #fff !important;
}

#showDateModalFrom .un-selected-button {
	background-color: #007bff;
	color: #fff;
}

#showDateModalFrom .un-selected-button:hover {
	background: green;;
	color: white;
}

#showDateModalFrom .disabled {
	color: gray!important;
   cursor: not-allowed !important;
}
#showDateModalFrom .active {
	color:darkgreen;
	font-weight:700;
}

</style>
@endsection

@include('common.menubuttons')

@section('content')
<div class="container-fluid">

	<div class="d-flex mt-0 mb-0 p-0"
		 style="height:75px;margin-bottom:5px !important">
		<div style="" class="p-0 align-self-center col-sm-5">
			<h2 class="mb-0">Credit Account</h2>
		</div>
		<div style="" class="col-sm-7 p-0 align-self-center text-right">
			<div class="row">
				<div class="col-sm-10 text-right">
					<div style="right:200px;display:inline;padding-left:0;margin-bottom:20px">
						<input class="to_date form-control btnremove"
						style="display:inline;margin-top:10px;padding-top:0px !important;
						position:relative;top:2px;
						padding-bottom: 0px; width:110px;padding-right:0;padding-left:0px;
						text-align: center;"
						value="End Date"
						onclick="show_dialog15()"
						id="stmt_start_date" name="start_date" placeholder="Select" />
					</div>
					{{ csrf_field() }}
					To
					<div style="right:200px;display:inline;padding-left:0;
						margin-bottom:20px">
						<input class="to_date form-control btnremove"
						style="display:inline;margin-top:10px;padding-top:0px !important;
						position:relative;top:2px;
						padding-bottom: 0px; width:110px;padding-right:0;
						padding-left:0px; text-align: center;"
						value="End Date"
						onclick="show_dialog20()"
						id="stmt_end_date" name="end_date" placeholder="Select" />
					</div>
				</div>
				<div class="col-sm-2">
					<button onclick="open_add_merchant_modal()"
							class="btn btn-success sellerbuttonwide mb-0 mr-0"
							style="padding:0px;float:right;margin-left:5px;
							border-radius:10px" id="stockin_btn">

							+Company
					</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="add_merchant_modal" tabindex="-1"
		 role="dialog" aria-labelledby="staffNameLabel" aria-hidden="true">
		 <div class="modal-dialog modal-dialog-centered"
			 role="document" style="width: 600px">
			<div class="modal-content bg-modal"  style=""	>

				<div class="modal-body bg-purplelobster" style="padding: 1rem;">
					<div class='' style="margin:auto">

						<input class="form-control"
							id="merchant_name" type="text"
							placeholder="Company Name"/>

					</div>
				</div>
			</div>
		</div>
	</div>


	<div class="modal fade" id="credit_limit_modal" tabindex="-1"
		 role="dialog" aria-labelledby="staffNameLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered"
			 role="document" style="width: 300px">
			<div class="modal-content ">

	            <div class="modal-body bg-purplelobster">
					<div class='text-center ' style="margin:auto">

						<div class="typewriter"
							id="credit_limit_fk_text"
							style="padding: 6px 12px 6px 12px;
							background-color: white; color: #0c0c0c; ">0.00
						</div>

						<input type="text" id="credit_limit_fk"
							style="text-align:right; margin-top: -14%; opacity: 0"
							class="form-control"
							placeholder='0.00' value="0"/>

						<input type="hidden" id="credit_limit_fk_buffer"
							style="text-align:right; " class="form-control"
							placeholder='' value="0"/>

						<input type="hidden" id='credit_limit_normal'/>
						<input type="hidden" id="element_credit_limit"
							style="text-align:right" value=""
							class="form-control" placeholder='0'/>
	                    <input type="hidden" id="credit_limit_pro_id"
	                        style="text-align:right" value=""
	                        class="form-control" placeholder='0'/>
					</div>
				</div>
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

					</h5>
				</div>
				<div class="modal-footer" style="border: none;">&nbsp;</div>
			</div>

		</div>
	</div>

	<div class="modal fade" id="confirm_delete_company" tabindex="-1" role="dialog" aria-labelledby="showMsgModal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered  mw-75 w-50" role="document">
			<div class="modal-content modal-inside bg-purplelobster">
				<div style="border-width:0" class="modal-header text-center"></div>
				<div class="modal-body text-center">
					<h5 class="modal-title text-white"
					id="statusModalLabel">Do you want to permanently delete
					this company?</h5>
				</div>
				<div class="modal-footer"
					style="border-top:0 none; padding-left: 0px; padding-right: 0px;">
					<div class="row"
						style="width: 100%; padding-left: 0px; padding-right: 0px;">
						<div class="col col-m-12 text-center">
							<input type="hidden" id="my_code"/>
							<button type="button"
							id="delete_company_confirm"
							class="btn bg-primary primary-button"
							onclick="delete_company(this.id)"
							data-dismiss="modal" style="color: white">Yes</button>
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

	<div class="col-sm-12 pl-0 pr-0" style="">
		<table class="table table-bordered boxhead" id="creditact_table">
			<thead class="thead-dark">
			<tr style="">
				<th class="text-center" style="width:30px;">No</th>
				<th class="text-left"   style="">Company&nbsp;Name</th>
				<th class="text-center" style="width:100px;">Credit&nbsp;Limit</th>
				<th class="text-center" style="width:100px;">Status</th>
				<th class="text-center" style="width:100px;">Amount</th>
				<th class="text-center" style="width:100px;">PDF</th>
				<th class="text-center" style="width:100px;"></th>
			</tr>
			</thead>
			<tbody id="">

			</tbody>
		</table>
	</div>
</div>

<div class="modal fade" id="showDateModalFrom" tabindex="-1"
  role="dialog" aria-labelledby="staffNameLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered  mw-75 w-50" role="document">
    <div class="modal-content modal-inside bg-purplelobster">
      <div class="modal-body text-center" style="min-height: 485px;max-height:485px">
        <div class="row">
          <div class="col-md-2">
            <i class="prev-month fa fa-chevron-left fa-3x"
            style="cursor:pointer;display: inline-flex;"></i>
          </div>
          <div class=" col-md-8">
            <div class="month-year text-center text-white"></div>
          </div>
          <div class="col-md-2">
            <i style="cursor:pointer"
            class="next-month fa fa-chevron-right fa-3x"></i>
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
    <form id="status-form" action="{{ route('logout') }}"
      method="POST" style="display: none;">
      @csrf
    </form>
  </div>
</div>


@endsection
@section('script')
<script src="{{asset('/js/osmanli_calendar.js')}}?version={{date("hmis")}}"></script>
<script>

store_date = dateToYMDEmpty(new Date());
$("#stmt_start_date").val(store_date);
$("#stmt_end_date").val(store_date);

function dateToYMDEmpty(date) {
	var strArray=['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
	var d = date.getDate();
	var m = strArray[date.getMonth()];
	var y = date.getFullYear().toString().substr(-2);
	var currentHours = date.getHours();
	return '' + (d <= 9 ? '0' + d : d) + '' + m + '' + y ;
}

var start_date_dialog = osmanli_calendar;
var completion_date_dialog = osmanli_calendar;

function show_dialog15(e){

    sessionStorage.removeItem("modalTrue");
    sessionStorage.setItem("modalTrue",'showTransStartDate');

	let min_date = '{{$first_approved}}' * 1000
    date = new Date();
    start_date_dialog.MAX_DATE = date;
    start_date_dialog.DAYS_DISABLE_MIN = "ON";
    start_date_dialog.DAYS_DISABLE_MAX = "ON";
    start_date_dialog.MIN_DATE = new Date(min_date);


    $('.next-month').off();
    $('.prev-month').off();

    $('.prev-month').click(function () {start_date_dialog.pre_month()});
    $('.next-month').click(function () {start_date_dialog.next_month()});



    start_date_dialog.CURRENT_DATE = new Date();

    if(localStorage.getItem("showCSSStartDate")===null)
    {


         start_date_dialog.SELECT_DATE = new Date()
    } else{
        var loclaaa=  localStorage.getItem("showCSSStartDate");
        // console.log( loclaaa)
        // start_date_dialog.CURRENT_DATE =new Date(localStorage.getItem("showH2StartDate"));
        start_date_dialog.SELECT_DATE = new Date(localStorage.getItem("showCSSStartDate"))
        start_date_dialog.CURRENT_DATE = new Date(localStorage.getItem("showCSSStartDate"))


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
            localStorage.setItem("showCSSStartDate",date)
            var start_date = dateToYMDEmpty(date);
            // console.log(start_date)

            // localStorage.setItem("sTransDate",start_date)

            $("#stmt_start_date").val(start_date);
			$('input[name="ca_start_date"]').val(start_date);
            jQuery('#showDateModalFrom').modal('hide');
        }

    }else{
        start_date_dialog.ON_SELECT_FUNC = function(){
            var date = osmanli_calendar.SELECT_DATE;
            localStorage.setItem("showCSSStartDate",date)
            var start_date = dateToYMDEmpty(date);
            console.log(start_date)

            // localStorage.setItem("sTransDate",start_date)

            $("#stmt_start_date").val(start_date);
			$('input[name="ca_start_date"]').val(start_date);
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


function show_dialog20(e){

        sessionStorage.removeItem("modalTrue");
        sessionStorage.setItem("modalTrue",'showTransStartDate');

        date = new Date();
        start_date_dialog.MAX_DATE = date;
        start_date_dialog.DAYS_DISABLE_MIN = "ON";
        start_date_dialog.DAYS_DISABLE_MAX = "ON";

        if(localStorage.getItem("showCSSStartDate") == null){
            start_date_dialog.MIN_DATE = new Date ()
        }else{
            start_date_dialog.MIN_DATE = new Date (localStorage.getItem("showCSSStartDate"))
            start_date_dialog.MIN_DATE.setDate(start_date_dialog.MIN_DATE.getDate())

        }

        $('.next-month').off();
        $('.prev-month').off();

        $('.prev-month').click(function () {start_date_dialog.pre_month()});
        $('.next-month').click(function () {start_date_dialog.next_month()});

        start_date_dialog.CURRENT_DATE = new Date();

        if(localStorage.getItem("showCSSEndDate")===null) {
            start_date_dialog.SELECT_DATE = new Date()
        } else{
            var loclaaa=  localStorage.getItem("showCSSEndDate");
            // console.log( loclaaa)
            // start_date_dialog.CURRENT_DATE =new Date(localStorage.getItem("showH2StartDate"));
            start_date_dialog.SELECT_DATE = new Date(localStorage.getItem("showCSSEndDate"))
            start_date_dialog.CURRENT_DATE = new Date(localStorage.getItem("showCSSEndDate"))


            // console.log(start_date_dialog.SELECT_DATE)
        }

        // console.log()

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
                localStorage.setItem("showCSSEndDate",date)
                var start_date = dateToYMDEmpty(date);
                // console.log(start_date)

                // localStorage.setItem("sTransDate",start_date)

                $("#stmt_end_date").val(start_date);
				$('input[name="ca_end_date"]').val(start_date);
                jQuery('#showDateModalFrom').modal('hide');
            }

        }else{
            start_date_dialog.ON_SELECT_FUNC = function(){
                var date = osmanli_calendar.SELECT_DATE;
                localStorage.setItem("showCSSEndDate",date)
                var start_date = dateToYMDEmpty(date);
                console.log(start_date)

                // localStorage.setItem("sTransDate",start_date)

                $("#stmt_end_date").val(start_date);
				$('input[name="ca_end_date"]').val(start_date);
                jQuery('#showDateModalFrom').modal('hide');
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
        jQuery('#showDateModalFrom').modal('show');

//end showTransStartDate
var EndDate = new Date();

}

function atm_money(num) {
    if (num.toString().length == 1) {
        return '0.0' + num.toString()
    } else if (num.toString().length == 2) {
        return '0.' + num.toString()
    } else if (num.toString().length == 3) {
        return num.toString()[0] + '.' + num.toString()[1] +
            num.toString()[2];
    } else if (num.toString().length >= 4) {
        return num.toString().slice(0, (num.toString().length - 2)) +
            '.' + num.toString()[(num.toString().length - 2)] +
            num.toString()[(num.toString().length - 1)];
    }
}

function open_add_merchant_modal(id) {
	$("#add_merchant_modal").modal("show");

	$('#add_merchant_modal').on('show.bs.modal', function (e) {
		document.getElementById('merchant_name').value = ''
	})

	$('#add_merchant_modal').on('hide.bs.modal', function (e) {
		let merchant_name = $("#merchant_name").val();
		if (merchant_name !== '') {
			$.ajax({
				url: "{{ route('creditact.save_merchant') }}",
				type: "POST",
				data: {
					merchant_name: merchant_name,
				},
				'headers': {
					'X-CSRF-TOKEN': '{{ csrf_token() }}'
				},
				success: function(response) {
					console.log(response);
					$("#return_data2").html("Company created successfully");
					$("#fillFields2").modal('show');
					table.ajax.reload();
				},
				error: function(resp) {
				  console.log(response);
				}
			})
			.done(() => {})
			.fail(() => {});
		}
		$(this).off('hide.bs.modal');
	})
}


function credit_limit_modal(old_value, company) {
	let merchant = company.replace("cl_", "");


	if (parseInt(old_value) > 0) {
	  $("#credit_limit_fk").val(atm_money(old_value));
	  $("#credit_limit_fk_text").text(atm_money(old_value));
	} else {
	  $("#credit_limit_fk").val('');
	  $("#credit_limit_fk_text").text("0.00");
	}
	$("#credit_limit_normal").val(old_value);
	// $("#element_credit_limit").attr("value", data);
	//$("#credit_limit_pro_id").attr("value", pro);

	$("#credit_limit_modal").modal("show");

	$('#credit_limit_modal').on('show.bs.modal', function (e) {
		$("#credit_limit_record").val(merchant);
	})

	$('#credit_limit_modal').on('hidden.bs.modal', function (e) {
		let credit_limit = $("#credit_limit_normal").val()
		let is_merchant = "true"

		// if ($("#"+company).attr("data-merchant") == undefined) {
		// 	is_merchant = "false"
		// }

		if (credit_limit != old_value) {
			$.ajax({
				url: "{{ route('creditact.save_credit_limit') }}",
				type: "POST",
				data: {
					credit_limit: credit_limit,
					merchant: merchant,
					is_merchant: is_merchant
				},
				'headers': {
					'X-CSRF-TOKEN': '{{ csrf_token() }}'
				},
				success: function(response) {
					table.ajax.reload();
					 console.log(response);
				},
				error: function(resp) {
				  console.log(response);
				}
			})
			.done(() => {
				$("#return_data2").html("Credit limit saved successfully");
				$("#fillFields2").modal('show');
			})
			.fail(() => {});
		}

		$(this).off('hidden.bs.modal');
        location.reload();
	});
}


$("#credit_limit_fk").on("keyup keypress", function (evt) {
	let old_value = "";
	let type_evt_not_use = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];

	if (evt.type === "keypress") {
		let value = $("#credit_limit_fk").val();
		console.log("value", value);
		old_value = parseInt(value.replace('.', ''));
		$("#credit_limit_normal").val(old_value == '' ? 0 : old_value);
	} else {
		if (evt.key === "Backspace") {
			let value = $("#credit_limit_fk").val();
			console.log("value-bp", value);
			old_value = parseInt(value.replace('.', ''));
			$("#credit_limit_normal").val(old_value);
		}

		let use_key = "";
		if (type_evt_not_use.includes(evt.key)) {
			use_key = evt.key;
			console.log(evt.key);
		}

		old_value = parseInt((isNaN($("#credit_limit_normal").val()) == false ?
			$("#credit_limit_normal").val() : 0) + "" + use_key);

		let nan = isNaN(old_value);
		console.log("up", old_value);

		if (old_value !== "" && nan == false) {
			$("#credit_limit_fk").val(atm_money(parseInt(old_value)));
			$("#credit_limit_fk_text").text(atm_money(parseInt(old_value)));
			$("#credit_limit_normal").val(parseInt(old_value));
		} else {
			$("#credit_limit_fk").val("0.00");
			$("#credit_limit_fk_text").text("0.00");
			$("#credit_limit_normal").val(0);
		}
	}
});

function show_delete_merchant_modal(tag_id){

	let is_merch = $('#'+tag_id).data('is_merchant');
	let merchant = tag_id.replace("del_", "");
	$.ajax({
		url: "{{ route('creditact.check_merchant_account') }}",
		type: "POST",
		data: {
			merchant: merchant,
			is_merchant: is_merch ? true : false
		},
		'headers': {
			'X-CSRF-TOKEN': '{{ csrf_token() }}'
		},
		success: function(response) {
			if (response.account_exists) {
				$("#return_data2").html("Company has transactions. Delete is prohibited.");
				$("#fillFields2").modal('show');
			} else {
				$("#delete_company_confirm").data('merchant', merchant.replace("_", " "));
				$("#confirm_delete_company").modal("show");
			}
			table.ajax.reload();
		},
		error: function(resp) {
		  console.log(response);
		}
	})
	.done(() => {})
	.fail(() => {});
	// $("#delete_company_confirm").data('merchant',merc);
	// $("#confirm_delete_company").modal("show");

}

function delete_company(){
	let merch = $('#delete_company_confirm').data('merchant');
	$.ajax({
		url: "{{ route('creditact.delete_merchant') }}",
		type: "POST",
		data: {
			merchant: merch
		},
		'headers': {
			'X-CSRF-TOKEN': '{{ csrf_token() }}'
		},
		success: function(response) {
			table.ajax.reload();
		},
		error: function(resp) {
		  console.log(response);
		}
	})
	.done(() => {
		$("#return_data2").html("Company deleted successfully");
		$("#fillFields2").modal('show');
	})
	.fail(() => {});
}

function download_ca_stmt(tag_id) {
	var args = {
		'start_date': $('#stmt_start_date').val(),
		'end_date': $('#stmt_end_date').val(),
		'company': $('#'+tag_id).data('co'),
		'type': $('#'+tag_id).data('co-type'),
	}

	console.log('****thema RGAS ', args)

	$.ajax({
		url: "{{ route('creditact.download_ca_pdf_stmt') }}",
		type: "POST",
		data: {
			'start_date': $('#stmt_start_date').val(),
			'end_date': $('#stmt_end_date').val(),
			'company': $('#'+tag_id).data('co'),
			'type': $('#'+tag_id).data('co-type'),
			'company_id': $('#'+tag_id).data('co-id'),
		},
		'headers': {
			'X-CSRF-TOKEN': '{{ csrf_token() }}'
		},
        contentType: false,
        processData: false,
        //dataType : 'text',
        //contentType : 'application/pdf',
		success: function(response) {
			console.log(response);
			$("#return_data2").html("Company created successfully");
			$("#fillFields2").modal('show');
		},
		error: function(resp) {
		  	console.log(response);
		}
	})
}
var tableData = {};

tableData ={
    'date':'{{ 'TODAY' }}'
};

var table = $('#creditact_table').DataTable({
 	"processing": true,
  	"serverSide": true,
  	"autoWidth": false,
  	"ajax": {
  		"url": "{{route('creditaccount.list')}}",
  		"type": "POST",
  		data: function (d) {
            return $.extend(d, tableData);
        },
  		'headers': {
  			'X-CSRF-TOKEN': '{{ csrf_token() }}'
  		},
  	},

	columns: [{
		data: 'DT_RowIndex',
		name: 'DT_RowIndex'
		/*
	},{
		data: 'sysid',
		name: 'sysid'
		*/
	},{
		data: 'name_company',
		name: 'name_company'
	},{
		data: 'credit_limit',
		name: 'credit_limit',
		render: function (data, type, row, meta) {

			let style = 'text-decoration: none;'

			let lim = (row['credit_limit'] != null ? row['credit_limit'] : 0) / 100
			let limit = lim.toFixed(2)

			if (row['sysid']) {
				company = row['name_company'].replace(/ /g,"_");
				return "<a href='#' id='cl_" + company +
					"' style='" + style + "' data-merchant=' " + company + " ' onclick='credit_limit_modal(" +
					(lim*100) + ", this.id)'>" + limit + "</a>"
			} else {
				company = row['sysid']
				return "<a href='#' id='cl_" + company +
					"' style='" + style + "' data-company=' " + company + " ' onclick='credit_limit_modal(" +
					(lim*100) + ", this.id)'>" + limit + "</a>"
			}
		}
	}, {
  	data: 'status',
	name: 'status',
	render: function (data) {
  		let status = data==null?"Pending":data.charAt(0).toUpperCase() + data.slice(1);
  			return "" + status + ""
  		}
  	}, {
  	data: 'amount',
	name: 'amount',
	render: function (data, type, row, meta) {
		var url = '{{ route("creditacountledger.get", ":id") }}';

		if (row['sysid'] == '-') {
			id = row['name_company']
		} else {
			id = row['sysid']
		}

		url = url.replace(':id', id);

		return "<a target='_blank' href='"+
			url+"' style='text-decoration: none;float: right;'>"+
			row['amount']+"</a>"
		}
	},{
		data: 'statement',
		name: 'statement',
		render: function (data, type, row, meta) {
			let co =''
			if (row['sysid'] == '-') {
				//oneway
				co = row['name_company'].replace(/ /g,"_");
			} else {
				//company
				co = row['sysid']
			}

			let co_type = row['type']
			let co_id = row['company_id']

			return "<form action='/credit/acount/merchant/download_ca_pdf_stmt' method='POST' style='padding:0; margin:0;'>" +
						"<input type='hidden' name='_token' value='{{ csrf_token() }}' />" +
					    "<input type='hidden' name='ca_start_date' value='"+$('#stmt_start_date').val()+"'>" +
					    "<input type='hidden' name='ca_end_date' value='"+$('#stmt_start_date').val()+"'>" +
					    "<input type='hidden' name='company' value='"+co+"'>" +
					    "<input type='hidden' name='company_id' value='"+co_id+"'>" +
					    "<input type='hidden' name='type' value='"+co_type+"'>" +
					    "<button style='padding: 0;background: none;border: none;' type='submit'>" +
						"<img width='25px' src='images/pinkcrab_50x50.png' alt=''> " +
					    "</button>" +
					"</form>"
			// return "<a onclick='download_ca_stmt(this.id)' id='stmt_" + co_id + "' data-co='" + co + "' data-co-id='" + co_id + "' data-co-type='" + co_type + "' > " +
			// 	"<img width='25px' src='images/pinkcrab_50x50.png' alt=''> </a>";

		}
	},{
		data: 'action',
		name: 'action',
		render: function (data, type, row, meta) {
			var url = '{{ route("creditacountledger.get", ":id") }}';

			let style = ''
			if (row.has_legder_account == "false") {
				style = 'text-decoration: none;'
			} else if (row.has_legder_account == "true"){
				style = 'text-decoration: none;  filter: grayscale(100) brightness(1.5); pointer-events: none;cursor: default;'
			}

			if (row['sysid'] == '-') {
				company = row['name_company'].replace(/ /g,"_");
			} else {
				company = row['sysid']
			}

			if (row['sysid'] == '-') {
				company = row['name_company'].replace(/ /g,"_");
				return "<a  href='javascript:void(0)' id='del_" + company +
						"' data-is_merchant='true' style='" + style +
						"' onclick='show_delete_merchant_modal(this.id)' class='delete text-right'> "+
						"<img width='25px' src='images/redcrab_50x50.png' alt=''> </a>"
			} else {
				company = row['sysid']
				return "<a  href='javascript:void(0)' id='" + company +
					"' data-is_merchant='false' style='" + style +
					"' onclick='show_delete_merchant_modal(this.id)' class='delete text-right'> "+
					"<img width='25px' src='images/redcrab_50x50.png' alt=''> </a>"
			}
		}
	}],

  	"columnDefs": [
  		{"width": "30px", "targets": [0]},
  		{"width": "auto", "targets": [1]},
  		{"width": "100px", "targets": [2, 3, 4]},
  		{"width": "30px", "targets": [5,6]},
  		{"className": "dt-left vt_middle", "targets": [1]},
  		{"className": "dt-right vt_middle", "targets": [2]},
  		{
			orderable: false,
			targets: [-1, -2]
		},
  	],
});

$.ajaxSetup({
 	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
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
                openitemtable.ajax.reload();
            }

            document_hidden = document[hidden];
        }
    });

    table.ajax.reload();
});



</script>
@endsection
@include('common.footer')
