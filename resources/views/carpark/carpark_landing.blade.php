@extends('landing.web')
@section('subheader')
@endsection

@section('content')
<script type="text/javascript"
		src="{{asset('js/qz-tray.js')}}"></script>
<script type="text/javascript"
		src="{{asset('js/opossum_qz.js')}}"></script>
<script type="text/javascript"
		src="{{asset('js/JsBarcode.all.min.js')}}"></script>
<script type="text/javascript"
		src="{{asset('js/console_logging.js')}}"></script>
<style>
::placeholder {
	text-align:center;
}
.keydigit {
	font-size: 20px;
	width: 40px !important;
	height: 40px !important;
	padding: 6px !important;
	text-align: center;
	color: black;
	margin-right: 5px;
	background-color: #ffffff00 !important;
	color: #fff !important;
	border: 1px solid #fff;
}

.pre_setup_field {
	width: 100%;
	height: 45px;
	margin: auto;
	font-size: 20px;
	background: transparent;
	border: 1px solid #fff;
	color: white !important;
	border-radius: 10px;
	text-align: left !important;
}

.pre_setup_field:focus {
	background: transparent;
	border: 1px solid #fff;
	color: white !important;
	border-radius: 10px;
	outline-width: 0;
}

.pre_setup_label {
	margin: 10px 0px;
}

.custom_activate_btn {
	border-radius: 10px;
	padding-left: 0;
	padding-right: 0;
	margin: auto;
	width: 70px;
	height: 70px;
	font-size: 16px;
	border-color: black;
	background-image: linear-gradient(#b4dd9f, #0be020);
}

.login_field {
	height: 40px;
	font-size: 17px;
	border-width: 0;
	margin-left: 0;
	margin-right: 0;
	margin-bottom: 5px;
	background: transparent;
	border: 1px solid #fff;
	color: white !important;
	border-radius: 10px;
	text-align: left !important;
}

.login_field:focus {
	background: transparent;
	border: 1px solid #fff;
	color: #fff !important;
	border-radius: 10px;
	outline-width: 0;
}

.custom_login_btn {
	width: 65%;
	height: 45px;
	font-size: 20px;
	margin-top: 10px;
	margin-left: auto;
	margin-right: auto;
	border-radius: 10px;
	color: white;
	border: 1px solid white;
	background-color: transparent !important;
}

.login_error {
	color: #fff;
}

/* Styles for Pay buttons */
.btn-pay-prawn-inactive {
	color: #cccccc;
	border-color: #cccccc;
	background-color: transparent;
}

.btn-pay-prawn-inactive:hover {
	color: #cccccc;
	border-color: #cccccc;
}

.btn-pay-prawn-custom {
	font-weight: normal;
	color: #f265b7;
	border-color: #f265b7;
	background-color: transparent;
}

.btn-pay-prawn-custom:hover {
	color: #f265b7;
	font-weight: bold;
}


/* Styles for Active buttons */
.btn-prawn-inactive {
	color: #cccccc;
	border-color: #cccccc;
	background-color: transparent;
}

.btn-prawn-inactive:hover {
	color: #cccccc;
	border-color: #cccccc;
}

.btn-prawn-custom {
	font-weight: normal;
	color: #34dabb;
	border-color: #34dabb;
	background-color: transparent;
}

.btn-prawn-custom:hover {
	color: #34dabb;
	font-weight: bold;
}

.kwh-hour-button-disabled {
	pointer-events: none;
	margin-bottom: 5px !important;
	color: #ffffff;
	background-color: rgb(146, 146, 146);
	background-image: none;
}

.poa-button-number-payment-enter-cp-disabled {
	pointer-events: none;
	margin-bottom: 5px !important;
	margin-left: 0 !important;
	margin-right: 0 !important;
	margin-top: 0 !important;
	/* font-size: 20px; */
	border-radius: 10px;
	color: #ffffff;
	border: 0;
	border-width: 0;
	padding-left: 0;
	padding-right: 0;
	width: 145px !important;
	height: 70px !important;
	background-color: rgb(146, 146, 146);
}

.poa-button-number-payment-enter-cp-active {
	margin-bottom: 5px !important;
	margin-left: 0 !important;
	margin-right: 0 !important;
	margin-top: 0 !important;
	/* font-size: 20px; */
	border-radius: 10px;
	color: #ffffff;
	border: 0;
	border-width: 0;
	padding-left: 0;
	padding-right: 0;
	width: 145px !important;
	height: 70px !important;
	background-image: linear-gradient(rgb(246, 25, 78), rgb(231, 115, 156));
}

.table > tbody > tr > th {
     vertical-align: middle;
}
</style>

@auth
@include("carpark.header")
<div class="container-fluid"
	id="container-blur">
<div class="row pt-2 pb-0 m-0 pl-0 pr-0"
	 style="height: 91.5%">
	<div class="col-md-4 pt-1 pb-0 m-0 pr-0"
		 style="display:flex;align-items:flex-end">

	<div class="row m-0  pb-0 pl-0 pr-0 col-12"
		id="pump-main-block-0"
		style="display:flex;align-items:flex-end;
		padding-left: 5px !important;">
		<div class="row col-md-12 pb-0 pl-0 pr-2 pr-0 mb-1"
			 style="margin-bottom:5px !important; color: white; "
			 id="detailOpera">
			<div style="padding-right:20px"
				 class="col-md-12 pl-0 mt-1">
				<div class="row "
					 style="font-size: 17px; font-weight:bold;">
					<div class="col-md-5 text-left">
						Description
					</div>
					<div class="col-md-2 text-center">
						@if( $current_setting_mode == 'hour' )
							Hour
						@else
							kWh
						@endif
					</div>
					<div class="col-md-2 text-center">
						Rate
					</div>
					<div class="col-md-3 text-right">
						{{empty($terminal->currency) ? 'MYR': $terminal->currency }}
					</div>
				</div>
				<div class="row"
					 style="opacity: 0; "
					 id="row_detail">
					<div class="col-md-5 text-left"
						 id="description"
						 >
						Value
					</div>
					<div class="col-md-2 p-1 text-center"
						 id="hours">
						Value
					</div>
					<div class="col-md-2  p-1 text-center"
						 id="rate">
						Value
					</div>
					<div class="col-md-2  p-1 text-right"
						 id="amount"
						 style="margin-left:6%;">
						Value
					</div>
				</div>

				<div class="col-12" 
					 style="padding: 0.05%; height: 1px; background-color: white">
				</div>
				<div class="row mt-1">
					<div class="col-md-8 text-left">
						Item
						Amount
					</div>
					<div class="col-md-4 text-right"
						 id="itemAmount">
						0.00
					</div>
				</div>
				<div class="row mt-1">
					<div class="col-md-8 text-left"  id="sst">
						{{!empty($terminal_all_value->taxtype)?strtoupper($terminal_all_value->taxtype):"SST"}}
						{{empty($terminal_all_value->tax_percent) ? '0.00': $terminal_all_value->tax_percent }}%
						
					</div>
					<div class="col-md-4 text-right"
						 id="tax">
						0.00
					</div>
				</div>
				<div class="row mt-1 mb-1" >
					<div class="col-md-8 text-left">
						Rounding
					</div>
					<div class="col-md-4 text-right"
						 id="rounding">
						0.00
					</div>
				</div>
				<div class="col-12"
				style="padding: 0.05%; height: 1px; background-color: white">
				</div>
				<div class="row mt-1">
					<div class="col-md-8 text-left">
						Total
					</div>
					<div class="col-md-4 text-right"
						 id="total">
						0.00
					</div>
				</div>
				<div class="row mt-2">
					<div class="col-md-8 text-left">
						Change
					</div>
					<div class="col-md-4 text-right"
						 id="change">
						0.00
					</div>
				</div>
			</div>
		</div>

		<div class="row col-md-12 pb-0 pl-0 pr-0 pr-0"
			 style="margin-bottom:5px !important">
			<div style="padding-right:15px"
				 class="col-md-12 pl-0 mt-1">

				<!-- <div id="payment-div"
					class="justify-content-center align-items-center"
					style="display:flex; background-color:#f0f0f0;
					border-radius:10px;height:50px !important;
					width:295px !important; ">

					<div id="payment-value"
						class="pr-2"
						style="color:#a0a0a0;font-weight:500;
						font-size: 30px; width: 100%;
						text-align: center;">
						Cash Received
					</div>

				</div> -->
				<input type="text"
					id="cash_received_input"
					style="display: flex;
				    background-color: #f0f0f0;
				    width: 295px !important;
				    border-radius: 10px;
				    height: 50px !important;
				    font-weight: 500;
				    font-size: 30px;
				    text-align: right;"
					class="form-control"
					placeholder='Cash Received'/>
				<input type="hidden"
					id='cash_received_input_normal'/>
			</div>
		</div>
		<div class="row col-md-12 pl-0 pr-0">
			<div class="col-md-12 row pr-0 pl-0"
				style="margin-left: -2px; ">

				<button class="btn btn-success btn-sq-lg screend-button
					bg-virtualcabinet"
					onclick="window.open('{{route('ev-receipt-list',[date('Y-m-d')])}}','_blank')"
					style="margin-left:0 !important;outline:none;font-size: 14px;">
					<span style="">Today Cabinet</span>
				</button>

				<button class="btn btn-sq-lg
					poa-button-number-payment-zero"
					onclick="set_cash('zero')"
					style="outline:none;font-size: 14px; margin-left: 5px !important;">
					Zero
				</button>

				<button id="enter-btn"
					class="btn poa-button-cash mb-1 poa-button-cash-disabled"
					onclick="process_finish()"
					style="width:145px !important;margin-left:5px !important">
					Enter
				</button>
			</div>
		</div>

		<hr class="pl-0"
			style="margin-top:0px !important;margin-left: -13px;
			margin-bottom:5px !important;width: 285px;border: 0.5px solid #a0a0a0;">
		<div class="row col-md-12 pl-0 pr-0">
			<div class="col-md-12 row pr-0 pl-0"
				 style="margin-left: -2px; ">

				<a href="{{route('screen.d')}}"
				   target="_blank"
				   rel="noopener noreferrer">
					<button class="btn ev-bluecrab-button mb-1"
						style="float: left !important;margin-right:0!important "
						id="bluecrab_btn"
						onclick="">
						<i style="top:2px;margin-left:0; margin-right:0;
							padding-left:-2px;padding-right:0;font-size:48px; "
							class="far fa-circle"></i>
					</button>
				</a>

				<button class="btn ml-0 opos-ev-button"
					style="margin-left: 5px !important;"
					id="ev_btn"
					onclick="window.close()">
					<img src="{{asset('images/ev_exit_button.png')}}"
						 style="margin-left:0;margin-top:1px;width:70px;
						height:auto;object-fit:contain; "/>
				</button>

				<button class="btn poa-button-cash mb-1 poa-button-cash-disabled"
					id="button-cash-payment0"
					onclick="select_cash()"
					style=" margin-left: 5px !important;">
					Cash
				</button>

			</div>
		</div>

		<div class="row col-md-12 pl-0 pr-0">
			<div class="col-md-12 row pr-0 pl-0"
				style="margin-left: -2px; ">
				<button class="btn btn-success btn-sq-lg
					ev-perhour-button @if( $current_setting_mode == 'hour' ) kwh-hour-button-disabled @endif "
					style="font-size:15px;
					margin-left: 0px !important; "
					onclick="hour_mode()">
					/Hour<br>Mode
				</button>

				<button id="kwh-mode" class="btn btn-success btn-sq-lg
					ev-kwh-button @if( $current_setting_mode == 'kwh' ) kwh-hour-button-disabled @endif"
					style="font-size:15px; width:70px !important;
					margin-left: 5px !important; "
					onclick="kwh_mode()">
					kWh<br>Mode
				</button>

				<button class="btn btn-success poa-button-credit-card poa-button-credit-card-disabled"
					id="button-credit-card"
					style=""
					onclick="select_credit_card()">
					Credit
					Card
				</button>
			</div>
		</div>

		<div class="row col-md-12 pl-0 pr-0">
			<div class="col-md-12 row pr-0 pl-0"
				 style="margin-left: -2px; ">

				<button class="btn ml-0 "
					style="width: 145px !important;
					color:#a0a0a0;
					background-color:#f0f0f0;
					border-width:0;font-size:20px;
					border-radius:10px;height: 70px !important;
					outline: none;text-align:center;"
					onclick="">
					Search
				</button>

				<button class="btn btn-success btn-sq-lg
					ev-button-drawer"
					style="font-size:15px; width:70px !important;
					margin-right:0 !important;
					margin-left: 5px !important;"
					onclick="open_cashdrawer()">
					Drawer
				</button>

				<button class="btn btn-success btn-sq-lg
					opos-button-wallet opos-button-wallet-disabled"
					id="button-wallet"
					style="border-radius:10px;width:70px !important"
					onclick="select_wallet()">
					Wallet
				</button>
			</div>
		</div>
	</div>
	<div class="container-fluid row"
		 style="position:absolute;bottom:60px">
		<div style="color:white;position:absolute;right:30px">
			<img style="width:80px"
				 src="{{asset('images/ev_transparent.png')}}">
		</div>
	</div>
</div>

<div class="col-md-8 pb-0 m-0 pr-0"
	style="border-left: 2px #a0a0a0 solid;
	color:white;">
	<table class="w-100" id="tableCarParkOpera">
		<tbody id="table_content">
		@include("carpark.carparklot_table")
		</tbody>
	</table>

	
	</div>
</div>
</div>
<div class="modal fade"  id="modalMessage"  tabindex="-1" role="dialog"
 	aria-hidden="true" style="text-align: center;">
    <div class="modal-dialog modal-dialog-centered  mw-75 w-50" role="document"
     style="display: inline-flex;">
        <div class="modal-content modal-inside bg-purplelobster"
        style="width: 100%;  background-color: {{@$color}} !important" >
            <div class="modal-header" style="border:0">&nbsp;</div>
            <div class="modal-body text-center">
                <h5 class="modal-title text-white" id="statusModalLabelMsg"></h5>
            </div>
            <div class="modal-footer" style="border-top:0 none;">&nbsp;</div>
        </div>
    </div>
</div>
<!-- Srawak modal start -->
<div class="modal fade" id="wallet_instruction_modal" tabindex="-1" role="dialog"
   style="padding-right:0 !important" aria-labelledby="" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered  mw-75 w-50" role="document">
      <div class="modal-content modal-inside bg-purplelobster">
         <div style="border:0" class="modal-header">&nbsp;
            <button class="close " type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true"></span>
            </button>
         </div>
         <div class="modal-body text-center" id="start-wallet">
            <h5 class="modal-title text-white">
               Please scan QR code to begin the process of wallet usage.
               </h5>
         </div>
         <div style="border:0" class="modal-footer">&nbsp;</div>
      </div>
   </div>
</div>

<div class="modal fade" id="wrong_key_modal" tabindex="-1" role="dialog"
   style="padding-right:0 !important" aria-labelledby="" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered  mw-75 w-50" role="document">
      <div class="modal-content modal-inside bg-purplelobster">
         <div style="border:0" class="modal-header">&nbsp;
            <button class="close clw" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true"></span>
            </button>
         </div>
         <div class="modal-body text-center" id="not-wallet-provider">
            <h5 class="modal-title text-white">
               The electronic wallet is not a panel provider.
            </h5>
         </div>
         <div style="border:0" class="modal-footer">&nbsp;</div>
		</div>
	</div>
</div>

<div class="modal fade" id="sarawak_pay" tabindex="-1" role="dialog"
	style="padding-right:0 !important" aria-labelledby="" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content modal-inside bg-purplelobster">
         <div style="border:0" class="modal-header">
         	 <button class="close " type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true"></span>
            </button>
         </div>
         <div class="modal-body text-center p-0" id="wallet-spay">
            <img class="center" 
				style="width:auto;height:90px;"
				src="{{ asset('images/sarawakpay_orange.png') }}"/>
                <div class="pt-2 d-none" id="inquirebtn">
                <button class="btn btn-success opos-topup-button"
                    id="query_order_btn"
                    style="height:35px !important; width:80px !important; border-radius:5px"
				        onclick="query_order();">Inquire
                    </button>
                    </div> 				
			<div id= "sc">
				<h5 class="mt-3" id="amt" data-id="{{ $terminal->currency ?? '' }}">
				Sending order amount {{$terminal->currency ?? ''}}
				<span id="spay_order_amt">0.00</span>
				to S Pay Global...
				</h5>
			</div>
			<div>
				<h5 class="mt-3" id="overwrite">
				</h5>
			</div>
		</div>
         <div style="border:0" class="modal-footer"></div>
      </div>
   </div>
</div>

@endauth
@endsection

@section('script')
<script src="{{asset("js/number_format.js")}}"></script>
<script type="text/javascript">

function messageModal(msg)
{
	$('#modalMessage').modal('show');
	$('#statusModalLabelMsg').html(msg);
	setTimeout(function(){
		$('#modalMessage').modal('hide');
	}, 2500);
}

$.ajaxSetup({
	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
});
let table_active_check = [];
let service_tax = null;
let payment_type = null;
let round = null;
let carparkoper_id = null;
let carparklot_id = null;
let lot_id = null;
let pay_as_click = false;
let description_save = "";
let cash_change = "";
let hours_save = 1;
let rate_save = 1;
let kwh_save = 1;
let myr_save = 1;
let itemAmount_save = 1;
let tax_save = 1;
let total_save = 1;
let amount_pay = 1;

function payClick(id, hr, rate, text_rate, text_amount, amount, carparkoper,
 carparklot, kwh, text_kwh, mode, kwh_db) {

 	// console.log(
 	// 	"id =" , id 
 	// 	,"hr =" , hr
 	// 	,"rate =" , rate
 	// 	,"text_rate =", text_rate
 	// 	,"text_amount =" , text_amount
 	// 	,"amount =" , amount
 	// 	,"carparkoper =" , carparkoper
 	// 	,"carparklot =" , carparklot
 	// 	,"kwh =" , kwh
 	// 	,"text_kwh =" , text_kwh
 	// 	,"mode =", mode
 	// 	,"kwh_db =" , kwh_db);

	carparkoper_id = carparkoper;
	lot_id = id;
	carparklot_id = carparklot;
	// console.log(text_amount.replace(",", ""));
	text_amount = text_amount.replace(",", "");
	// amount = parseInt(text_amount);
	// console.log(text_amount);
	// console.log(parseInt(text_amount));
	let percent = ({{$terminal_all_value->tax_percent}} / 100) + 1;
	let percent_no_one = {{$terminal_all_value->tax_percent}} / 100;
	if(mode == 'hour')
	{
		amount = hr * (rate /100);
	}else{
		amount = kwh * (parseFloat(kwh_db) / 100);
	}
	let itemAmount = (amount).toFixed(2) / percent;
	itemAmount = itemAmount.toFixed(2);
	console.log('amount: ' + (amount).toFixed(2));
	console.log('item amount: ' + itemAmount);
	let sst = amount - itemAmount;
	let tax = atm_money(amount) - itemAmount;
	service_tax = tax;
	pay_as_click = true;
	let rounding = 0;
	amount_pay = amount;
	let total_amount = 0;
	$.ajax({
		method: "post",
		url: "{{route('carparkoper.getRounding')}}",
		data: {amount: amount}
	}).done((data) => {
		rounding = data.text_number;
		round = data['text_number'];
		$("#rounding").text(rounding);
		if(mode == 'hour')
		{
			total_amount = parseFloat(itemAmount) + parseFloat(sst) + parseFloat(rounding);
			total_save = total_amount;
		}else{
			console.log('formula: ' + rounding + ' + ' + sst + ' + ' + itemAmount);
			total_amount = parseFloat(itemAmount) + parseFloat(sst) + parseFloat(rounding);
			total_save = total_amount;
		}
		$("#total").text(Number(total_amount).toFixed(2));
	}).fail((data) => {
		console.log("data", data)
	});

	$("#description").text("Electric Charge");
	if(mode == 'hour')
	{
		hours_save = hr;
		rate = (rate/100).toFixed(2);
		rate_save = rate;
		$("#hours").text(hr);
		$("#amount").text(number_format(amount, 2));
		kwh_save = 0;
	}else{
		hours_save = 0;
		rate = (parseFloat(kwh_db) / 100).toFixed(2);
		$("#hours").text(text_kwh);
		$("#amount").text(number_format(amount, 2));
		rate_save = rate;
		kwh_save = parseFloat(kwh);
	}
	console.log(rate_save);
	$("#rate").text(number_format(rate, 2));
	$("#itemAmount").text(number_format(itemAmount, 2));
	$("#tax").text(number_format(sst, 2));
	// $("#sst").text(number_format(sst, 2));

	description_save = "Electric Charge";
	// rate_save = parseFloat(text_rate);
	myr_save = amount;
	itemAmount_save = parseFloat(itemAmount);
	tax_save = sst;

	$("#row_detail").css("opacity", "1");

	$("#detailOpera").css("opacity", "1");
	$("#detailOpera").css("pointer-events", "auto");

	$("#cash_received_input").css("opacity", "1");
	$("#cash_received_input").css("pointer-events", "auto");

	$("#button-cash-payment0").removeClass("poa-button-cash-disabled");
	$("#button-cash-payment0").addClass("poa-button-cash");


	$("#button-credit-card").removeClass("poa-button-credit-card-disabled");
	$("#button-credit-card").addClass("poa-button-credit-card");


	$("#button-wallet").removeClass("opos-button-wallet-disabled");
	$("#button-wallet").addClass("opos-button-wallet");
}


function removeOnArray(value) {
	//console.log("begin",table_active_check);
	let exist = false;
	let new_tab = [];
	for (var i in table_active_check) {
		if (table_active_check[i] == value) {
			exist = true;
		} else {
			new_tab.push(table_active_check[i]);
		}
	}
	table_active_check = new_tab;
	if (!exist) {
		table_active_check.push(value);
	}

	for (let i = 0; i < table_active_check.length; i++) {
		$(".trigger_active_" + table_active_check[i]).toggleClass("btn-prawn-custom");
		$(".trigger_active_" + table_active_check[i]).toggleClass("btn-prawn-inactive");
	}
	//  console.log("end",table_active_check);
}



function statusClick(id, amount, carparkoper_id) {
	$.ajax({
		method: "post",
		url: "{{route('carparkoper.actionStatus')}}",
		data: {
			id: id,
			int_amount: amount,
			carparkoper_id: carparkoper_id,
		}
	}).done((data) => {
		$("#table_content").html(data);
		removeOnArray(id);

	}).fail((data) => {
		console.log("data", data)
	});
}

$(document).ready(function(){
	// await sleep(200);
	setInterval(function(){
	var stop_count_var = localStorage.getItem('stop_count_var');
	var transaction_count_var = localStorage.getItem('transaction_count_var');
	var paid_count_var = localStorage.getItem('paid_count_var');
		$.ajax({
		method: "post",
		url: "{{route('carparkoper.multiTransactionCheck')}}",
		data: {
			paid_count: paid_count_var,
			stop_count: stop_count_var,
			transaction_count: transaction_count_var,
		}
	}).done((data) => {
		if(data != 'no change')
		{
			// console.log(paid_count_var + ' - ' + stop_count_var + ' - ' + transaction_count_var);
			// console.log(data);
			console.log("refresh");
			$("#table_content").html(data);
		}else{
			// console.log(paid_count_var + ' - ' + stop_count_var + ' - ' + transaction_count_var);
			// console.log("data", data)
		}
	}).fail((data) => {
		console.log("data", data)
	});
}, 5000);
});

function hour_mode() {
	
	if( $( "#tableCarParkOpera .active_button_active" ).hasClass( "btn-prawn-custom" ) ||
		$( "#tableCarParkOpera .active_button" ).hasClass( "btn-pay-prawn-custom" )) {
		
		messageModal("Unable to change the current mode. A parking lot is still busy.");
		return;

	}else{
		console.log('Hour Mode Clicked');
		$.ajax({
			method: "post",
			url: "{{route('carparkoper.changeMode')}}",
			data: {mode: 'hour'}
		}).done((data) => {
			console.log(data);
			location.reload();
		}).fail((data) => {
			console.log("data", data)
		});
	}
}


function kwh_mode() {

	if( $( "#tableCarParkOpera .active_button_active" ).hasClass( "btn-prawn-custom" ) ||
		$( "#tableCarParkOpera .active_button" ).hasClass( "btn-pay-prawn-custom" )) {
		
		messageModal("Unable to change the current mode. A parking lot is still busy.");
		return;

	} else {
		console.log('kWh Mode Clicked');
		$.ajax({
			method: "post",
			url: "{{route('carparkoper.changeMode')}}",
			data: {mode: 'kwh'}
		}).done((data) => {
			console.log(data);
			location.reload();
		}).fail((data) => {
			console.log("data", data)
		});
	}
}

function select_credit_card() {
	if (pay_as_click) {
		$("#cash_received_input").css("opacity", "0");
		$('#change').text('0.00');
		$("#cash_received_input_normal").val('');
		$("#cash_received_input").val('');
		$("#enter-btn").removeClass("poa-button-cash-disabled");
		$("#enter-btn").addClass("poa-button-cash");
		payment_type = "creditcard";
	}
}


function select_wallet() {

		var amount = $('#total').text();
		var product_name = $('#description').text();
		display_wallet_instruction();
		enable_scanner = true;
		scan_wallet_qrcode(amount , product_name , 	enable_scanner); 

	if (pay_as_click) {
		$("#cash_received_input").css("opacity", "0");
		$('#change').text('0.00');
		$("#cash_received_input_normal").val('');
		$("#cash_received_input").val('');
		// $("#detailOpera").hide();
		$("#enter-btn").removeClass("poa-button-cash-disabled");
		$("#enter-btn").addClass("poa-button-cash");
		payment_type = "wallet";
	}
}


function select_cash() {
	$("#cash_received_input").removeAttr('disabled');
	$("#cash_received_input").css("opacity", "1").focus();
	$("#enter-btn").addClass("poa-button-cash-disabled");
	$("#enter-btn").removeClass("poa-button-cash");
	payment_type = "cash";
}
</script>
<!-- OPOS_WALLET END -->

@include('carpark.carpark_payment')
@include('carpark.carpark_wallet')
@endsection
@include("common.footer")
