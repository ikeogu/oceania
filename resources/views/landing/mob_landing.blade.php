@extends('landing.web')
@section('subheader')
@endsection
@section('content')
<script type="text/javascript" src="{{asset('js/qz-tray.js')}}"></script>
<script type="text/javascript" src="{{asset('js/opossum_qz.js')}}"></script>
<script type="text/javascript" src="{{asset('js/JsBarcode.all.min.js')}}"></script>
<script type="text/javascript" src="{{asset('js/console_logging.js')}}"></script>
<style>
::placeholder {
	text-align:center;
}

.noclick {
   pointer-events: none;
}
.productselect{
	scrollbar-width: thin;
}
.productselect:hover {
    color: #34dabb;
}
.credit_button:hover {
        color: #34dabb;
        font-weight: bold;
    }
.ft-overlay-reserved {
    background: #26232b;
	display:flex;
    opacity: 0.8;
    width: 100%;
    top: -5px;
    left: 3px;
    height: 109%;
    border-radius: 10px;
    position: absolute;
	align-items:center;
	justify-content:center;
    z-index: 10;
}
.ft-overlay-screen-a{
	background: #211f24;
    opacity: 0.78;
    top: -10px;
    left: -10px;
    right: 0;
    padding: 10px;
    width: 86%;
    height: 200%;
    border-radius: 10px;
    position: absolute;
    text-align: center;
    z-index: 10;
}
#foo{
	font-weight: 1000;
	color: #fff;
	color: rgba(255,255,255, 0.2);
	font-size: 48pt;
}
.overlay1 {
    background: #6f42c1;
	display:flex;
    opacity: 0.4;
    width: 100%;
    top: -5px;
    left: 3px;
    height: 109%;
    border-radius: 10px;
    position: absolute;
	align-items:center;
	justify-content:center;
    z-index: 10;
}

.scover-blue {
    background: #87CEEB;
	display:flex;
    opacity: 0.8;
	font-weight: normal;
    width: 88%;
    left: 3px;
    height: 109%;
    border-radius: 10px;
    position: absolute;
	align-items:center;
	justify-content: flex-start;
    z-index: 8;
}

.scover-grey {
    background-color: rgb(146, 146, 146);
	display:flex;
    opacity: 0.8;
	font-weight: normal;
    width: 88%;
    left: 3px;
    height: 109%;
    border-radius: 10px;
    position: absolute;
	align-items:center;
	justify-content:flex-end;
    z-index: 8;
}

.fulltank-overlay {
    background: #211f24;
	/* opacity:80%; */
	/* display:flex; */
    width: 100%;
    top: -5px;
	padding-top: 3px;
    left: 3px;
    height: 109%;
    border-radius: 10px;
    position: absolute;
	align-items:center;
	justify-content:center;
    z-index: 10;
}


.overlay2 {
    background: #6f42c1;
    opacity: 0.4;
    top: -10px;
    left: -10px;
    right: 0;
    padding: 10px;
    width: 78%;
    height: 200%;
    border-radius: 10px;
    position: absolute;
    text-align: center;
    z-index: 10;
}
.overlayxr {
    background: #6f42c1;
    opacity: 0.4;
    top: -10px;
    left: 0;
    right: 0;
    padding: 10px;
    width: 100%%;
    height: 100%;
    border-radius: 10px;
    position: absolute;
    text-align: center;
    z-index: 10;
}
.poa-button-number-payment-cancel-disabled1{
	margin-bottom: 5px !important;
    margin-right: 2px !important;
    padding-left: 0;
    padding-right: 0;
    font-size: 14px;
    border: 0;
    border-radius: 10px;
    border-width: 0;
    color: #ffffff;
    width: 70px !important;
    height: 70px !important;
}

.keydigit {
	font-size:20px;
	width: 40px !important;
	height: 40px !important;
	padding: 6px !important;
	text-align:center;
	color: black;
	margin-right:5px;
    background-color: #ffffff00 !important;
    color: #fff !important;
    border: 1px solid #fff;
}
.pre_setup_field {
	width:100%;
	height:45px;
	margin:auto;
	font-size:20px;
    background: transparent;
    border: 1px solid #fff;
    color: white!important;
    border-radius: 10px;
    text-align: left !important;
}
.pre_setup_field:focus{
    background: transparent;
    border: 1px solid #fff;
    color: white!important;
    border-radius: 10px;
    outline-width: 0;
}
.pre_setup_label {
	margin: 10px 0px;
}
.custom_activate_btn {
    border-radius: 10px;
	padding-left:0;
	padding-right:0;
    margin: auto;
    width: 70px;
    height: 70px;
	font-size:16px;
    border-color: black;
	background-image:linear-gradient(#b4dd9f,#0be020);
}
.login_field {
	height: 40px;
	font-size:17px;
	border-width:0;
    margin-left:0;
    margin-right:0;
    margin-bottom:5px;
    background: transparent;
    border: 1px solid #fff;
	color: white!important;
    border-radius: 10px;
	text-align: left !important;

	@if (!empty($isLocationActive))
	@endif
}
.login_field:focus{
    background: transparent;
    border: 1px solid #fff;
    color: #fff !important;
    border-radius: 10px;
    outline-width: 0;
}
.custom_login_btn {
    width: 65%;
    height: 45px;
	font-size:20px;
    margin-top:10px;
    margin-left:auto;
    margin-right:auto;
    border-radius:10px;
	color:white;
	border:1px solid white;
	background-color:transparent !important;
}
.login_error {
	color: #fff;
}
.fulltank-button {
	border: 0;
    padding: 0px !important;
    border-radius: 5px;
    width: 45px !important;
    height: 45px !important;
    margin-top: 2px !important;
    /* margin-left: 2px !important; */
    margin-right: 7px !important;
    margin-bottom: 0px !important;
	background-image:linear-gradient(#0447af,#3682f8);
}
.fulltank-button-disabled{
	pointer-events: none !important;
    color: #ffffff;
    border-width: 0;
    background-color: rgb(146, 146, 146);
    background-image: none;
}
.btn-size-70 {
	width: 70px;
	height: 70px;
	border-radius: 10px;
}

#dataList li {
	list-style: none;
}

.opos-button-credit-ac {
    margin-top: 0 !important;
    margin-right: 0 !important;
    margin-left: 5px !important;
    margin-bottom: 5px !important;
    width: 70px !important;
    height: 70px !important;
    font-size: 16px;
    color: #ffffff;
    border-width: 0;
    border-radius: 10px;
    background-image: linear-gradient(#49f300, #bcf68c);
}
.opos-button-credit-disabled {
    pointer-events: none !important;
    margin-top: 0 !important;
    margin-right: 0 !important;
    margin-left: 5px !important;
    margin-bottom: 5px !important;
	width: 70px !important;
    height: 70px !important;
    font-size: 16px;
    color: #ffffff;
    border-width: 0;
    border-radius: 10px;
    background-color: rgb(146, 146, 146);
    background-image: none;
}


.poa-finish-button-disabled {
    pointer-events: none !important;
    margin-top: 0 !important;
    margin-right: 0 !important;
    margin-left: 2px !important;
    margin-bottom: 5px !important;
    width: 145px !important;
    height: 70px !important;
    font-size: 16px;
    color: #ffffff;
    border-width: 0;
    border-radius: 10px;
    background-color: rgb(146, 146, 146);
    background-image: none;
}
.onHoverGreen:hover {
	background-color: green;
	color: white;
	border-color: none;
}
.onHoverGreen {
	border: none !important;
}
.poa-button-cash-selected-disabled {
    pointer-events: none !important;
    margin-top: 0 !important;
    margin-right: 0 !important;
    margin-left: 5px !important;
    margin-bottom: 5px !important;
    height: 70px !important;
	font-size: 16px;
    color: #ffffff;
	border-width: 0;
	border-radius: 10px;
    background-color: rgb(146, 146, 146);
	background-image: none;
}
.eightbuttons{
	width: 153px;
    justify-content: flex-end;
    display: flex;
    height: 230px;
}
.flex_table{
	width: 294px;
	float: left;
	height: 122px;
	display: flex;
	justify-content: flex-start;
}
.line_before_number {
	border-left: #a0a0a0 1px solid;
    height: 68px;
    padding: 0px;
    margin-left: -23px;
    padding-left: 0px;
}
.line_before_number_fulltank {
	border-left: #a0a0a0 1px solid;
    height: 52px;
    padding: 0px;
	margin-right: 10px !important;
	margin-bottom: 5px;
}
.col_panel_inner{
	padding: 0px !important;
    font-size: 13px;
}

.col_panel_inner > span {
    padding-left: 7px;
	display: block;
}

.line_before_number > span{
	display: block;
    width: 66%;
    text-align: right;
}
.selectedimag_hover:hover,
.selectedimag{
	/* box-shadow: 0 0 0 5px rgba(72, 180, 97, 0.8); */
    color: #ffffff;
    border-width: 0;
	border-radius: 5px;
	outline: none;
	/*
    background-color: #28a745;
    background-image: linear-gradient(#28a745,#28a745);
	*/

}
.left-7{
	left: 7px !important;
}
.product-disable-offline{
	-webkit-filter: brightness(180%) contrast(70%) grayscale(150%);
	filter: brightness(160%) contrast(70%) grayscale(150%);
}

.cursor-pointer {
	cursor: pointer;
}

@media screen and (min-width: 1400px) and (max-width: 1500px) {
	.overlay2{
		width: 90%;
	}
}

@media screen and (min-width: 1500px) and (max-width: 1600px) {
	.overlay2{
		width: 94%;
	}
}
@media screen and (min-width: 1620px) {
	.overlay2{
		width: 88%;
	}
}
@media screen and (min-width: 900px) {
	.mainrightsidediv	{
	 	min-height: 90%;
		right: -100px;
		position: absolute;
		top: 25px;
		bottom: 0px !important;
		min-width: 952px;
 	}
	.col_panel {
		width: 24%;
		height: 150px;
		padding-right: 5px;
	}
	.hr{
		min-height: 730px;
		left: 474px;
		position: absolute;
		top:-80px;
	}
}

@media screen and (min-width: 1500px) {
	.mainrightsidediv	{
	 	min-height: 96%;
		right: -100px;
		position: absolute;
		top: -55px;
		bottom: 0px !important;
		min-width: 1090px;
 	}
	.col_panel {
		width: 24%;
		height: 163px;
		padding-right: 5px;
	}
	.hr{
		min-height: 96%;
		left: 474px;
		position: absolute;
		top:-7px;
	}
}

.modal-backdrop {
    position: fixed;
    z-index: -1 !important;
}
.selected_preset_button {
	/* color: #ffffff;
    border-width: 0;
    background-color: #28a745;
    background-image: linear-gradient(#28a745,#28a745); */
}
#log{
    width: 80% !important;

}
</style>

@auth
@include("common.header")
<div class="fixed-bottom">
<div class="container-fluid pr-0" id="container-blur" style="">
<div class="row pt-2 pb-0 m-0 pl-0 pr-0" style="width:105% !important">

	<div id = "cli" class="col-md-4 row" style="display:flex;align-items:flex-end">

	<!-- Squidster: 128px -> 0 when PUMPS < 16 -->
	<div class="col-md-12 pt-2 pb-0 m-0 pl-0 pr-0"
		style="position:relative;top:128px">

		<!--
		pump-main-block-0 is the default. This will be displayed
        before user clicks on any pump.
		-->

		<div class="row m-0 pt-2 pb-0 pl-0 pr-0 col-md-12"
			id="pump-main-block-0" style="display:flex;align-items:flex-end">

			<!-- Start pump-0 section -->
			<div class="col-md-12 pt-0 pb-0 m-0 pr-0" style="
					padding-left:15px">
			<div class="row">
				<span id="payment-amount-card-amount2" class=""
					style="color:white;font-weight:500;
					font-size:30px;font-weight: bold;padding-right:5px">
				</span>
				<span id="payment-type-amount2" class="float-right"
					style="color:white;font-weight:500;
					font-size:30px;font-weight: bold;padding-right:25px">
				</span>
			</div>

			<div class="row" style="margin-bottom:5px !important">
				<div style="padding-right:20px;min-height: 280px;"
					class="col-md-12 pl-0 mt-1"
					id="payment-div-cash">

					<div class="flex_table" >
					<div class="col-md-12 mt-auto w-100 mr-0 pr-1 pl-0"
						style="height: 115px;">

					<div class="row p-0 m-0 text-white" style="font-weight:bold">
						<div class="col-4 p-0 m-0">
							Description
						</div>
						<div class="col-3 text-center">
							Price
						</div>
						<div class="col-3 text-center">
							Qty
						</div>
						<div class="col-2 p-0 m-0 text-right">
							{{$currency ?? ''}}
						</div>
					</div>

					<div class="row p-0 m-0 text-white">
						<div class="col-4 p-0 m-0" id="table-PRODUCT-0"></div>

						<div class="col-3 text-center" id="table-PRICE-0"></div>

						<div class="col-3 text-center" id="table-QTY-0"></div>

						<div class="col-2 p-0 m-0 text-right"
							id="table-MYR-0">0.00
						</div>
					</div>

					<hr class="" style="margin-top:5px !important;
						margin-bottom:5px !important;
						border:0.5px solid #a0a0a0">

					<div class="d-flex bd-highlight">
						<div class="mr-auto bd-highlight text-white">
							Item Amount
						</div>
						<div class="bd-highlight text-white">
							<span id="item-amount-calculated-2">0.00</span>
						</div>
					</div>
					<div class="d-flex bd-highlight">
						<div class="mr-auto bd-highlight text-white">
							{{!empty($terminal->taxtype)?strtoupper($terminal->taxtype):"SST"}} {{$terminal->tax_percent}}%
						</div>
						<div class="bd-highlight text-white">
							<span id="sst-val-calculated-2">0.00</span>
						</div>
					</div>
					<div class="d-flex bd-highlight">
						<div class="mr-auto bd-highlight text-white">
							Rounding
						</div>
						<div class="bd-highlight text-white">
							<span id="rounding-val-calculated-2">0.00</span>
						</div>
					</div>

					<hr class="" style="margin-top:5px !important;
						margin-bottom:5px !important;
						border:0.5px solid #a0a0a0">

					<div class="d-flex bd-highlight">
						<div class="mr-auto bd-highlight text-white">
							Total
						</div>
						<div class="bd-highlight text-white">
							<span id="grand-total-val-calculated-2">0.00</span>
						</div>
					</div>

					<div class="d-flex bd-highlight">
						<div class="mr-auto bd-highlight text-white">
							Change
						</div>
						<div class="bd-highlight text-white">
							<span id="change-val-calculated-2">0.00</span>
						</div>
					</div>
					</div>

					<div class=""
						style="height:140px;display:flex;align-items:flex-end;">
					<div class="col-md-12 pr-0">
						<div class="row mr-0 ml-0" style="display: flex;
							align-items: flex-end;
							margin-bottom: 5px;">

						<!-- Display product image -->
						<div style="position:relative;left:-30px;"
							class="col-md-3 text-center">
							<img src="" alt=""
								style="width:100px; height:100px;
								object-fit:contain;display:none;"
								id="display-product-thumb">
						</div>

						<div class="col-md-4 pl-0">
							<div class="text-white"
								id="display-product-name"
								style="line-height: 1.5em;
								height: 3em;
								overflow: hidden;">
							</div>
							<span class="text-white"
								id="display-product-systemid">
							</span>
						</div>

						<div class="col-md-3 row pr-0">
							<div class="text-right col-md-12 pr-0">
								<span class="text-white"
									id="display-product-price">
								</span>
							</div>
						</div>

						<div class="col-md-2 ml-0 pr-0"
							style="position:relative;left:30px">
							<div style="float:right;padding-right:0;">
							<button class="btn btn-sq-lg btn-success
								cstore-redcrab-button" id="cstore-redcrab-btn"
									style="z-index:9999;display:none;
									margin-bottom:0px !important;" onclick="">
								<i style="margin-top:-8px;
									padding-left:0;padding-right:0;
									font-size:80px" class="fa fa-times-thin">
								</i>
							</button>
						</div>
						</div>

						</div>
					</div>
					</div>

					<!-- padding-left:15px or -->
					<div class="row ml-0 mr-0" style="padding-left:0; margin-bottom:5px">

						<!-- <div class="col-md-12 pl-0 pr-0 payment_btns" style="">
						</div> -->

						<div class="col-md-12 row p-0 ml-0 mr-0
							payment_btns align-items-center"
							style="display:none; text-align: center;">

							<input class="" type="number"
								style="background-color:#f0f0f0;
								border-width:0;font-size:16px;border-radius:10px;
								height: 70px !important; width:145px;outline: none;
								text-align:center; float: left;"
								placeholder="Item  Discount" id="item_disc"
								min="1" max="100" maxlength="3" size="3"
								onkeypress="if(this.value.length==3) return false;">

							<button class="" id="show_discount_percent"
								onclick="" style="margin-left:0;margin-right:0;
								display:inline;color:white;font-size:40px;
								background:transparent;
								border:none;
								pointer-events:none;
								width:auto;height:70px;">
								0
							</button>

							<button class="btn btn-sq-lg btn-success
								cstore-redcrab-button"
								id="cstore-redcrab-btn-end" style="
								border-radius:10px;
								margin-bottom:0 !important;
								text-align: center;
								font-size: 12px;
								width: 70px !important;
								margin-left: 5px !important;
								float: right;
								">
								Clear Discount
							</button>

							<button class="btn btn-success
								cstore-button-receiptdisc"
								id="cstore-button-receiptdisc-end"
								onclick="" style="border-radius:10px;
								margin-bottom:0 !important;
								font-size:36px;width:70px;height:70px;
								float:right;">%
							</button>
						</div>
					</div>
				</div>

				<div class="eightbuttons">
					<div class="col-md-12 mt-auto pt-2 pb-0 m-0 pl-0 pr-0"
						style="height: 228px;">
						<div class="row" style="height: 50px;">
							<div style=""
								class="col-md-6 p-0">
									<div style="position:relative;left:-5px;top:-27px; height:70px;"
										class="text-right">
										<img src="{{asset('images/dispenser_icon.png')}}"
											style="transform:scaleX(-1);
											width:70px;height:70px;object-fit:contain"/>
									</div>
								<div style="position:relative;left:-10px;top:20px"
									class="text-right pr-4 pt-0 mr-1">
								<div class="ml-0 p-0" style="text-align:center;
									position:relative; left: 12px; top: -31px;
									font-size: 28px; color:white;"
									id="pump-number">
									<b id="pump-number-main-0">&nbsp;&nbsp;</b>
									<span id="active-pump"></span>
									<input type="hidden"
										id="paid-pump" value="">
									<input type="hidden"
										id="previous-pumps" pumpno="" prod="">
								</div>
								</div>
							</div>

						</div>

						<div class="row float-right">
						<div style="justify-content:flex-end;display:flex"
							class="col-md-12 float-right">
							<button class="btn btn-sq-lg
								button-number-amount poa-button-number-disabled"
								onclick="set_amount(2)" >2
							</button>

							<button class="btn btn-sq-lg
								button-number-amount poa-button-number-disabled"
								onclick="set_amount(5)">5
							</button>
						</div>
						</div>

						<div class="row float-right">
						<div style="justify-content:flex-end;display:flex"
							class="col-md-12 float-right">
							<button class="btn btn-sq-lg
								button-number-amount poa-button-number-disabled"
								onclick="set_amount(10)">10
							</button>

							<button class="btn btn-sq-lg
								button-number-amount poa-button-number-disabled"
								onclick="set_amount(20)">20
							</button>
						</div>
						</div>
						<div class="row float-right">
						<div style="justify-content:flex-end;display:flex"
							class="col-md-12 float-right">
							<button class="btn btn-sq-lg
								button-number-amount poa-button-number-disabled"
								onclick="set_amount(50)">50
							</button>

							<button class="btn btn-sq-lg
								button-number-amount poa-button-number-disabled"
								onclick="set_amount(100)">100
							</button>
						</div>
						</div>
						<div class="row float-right">
						<div style="justify-content:flex-end;display:flex"
							class="col-md-12 float-right">
							<button class="btn btn-sq-lg
								button-number-amount poa-button-number-disabled"
								onclick="set_amount(150)">150
							</button>

							<button class="btn btn-sq-lg
								button-number-amount poa-button-number-disabled"
								onclick="set_amount(800)">800
							</button>
						</div>
						</div>
						<div class="row float-right">
						<div class="col-md-12 float-right pl-0">
							{{-- <button class="btn poa-authorize-disabled"
							style="" id="authorize-button__" >Authorize
							</button> --}}
							</div>
						</div>
						</div>
					</div>
					<input class="justify-content-center align-items-center"
						style="display:flex; background-color:#f0f0f0;
						width:295px !important;
						border-radius:10px;height: 50px !important;
						font-weight:500; font-size:30px;text-align:right"
						id="input-cash" placeholder="Cash Received"
						disabled="disabled">
					<input id="buffer-input-cash" type="hidden">
				</div>

				<!--
				<div style="padding-right:20px;display:none"
					class="col-md-12 pl-0 mt-1" id="payment-div-cash-card2">
					<div class="justify-content-center align-items-center"
					style="display:flex; background-color:black;
						border-radius:10px;height: 50px !important">
						<span class="text-center pl-3"
							style="color:#a0a0a0;font-weight:500;
							font-size:30px">
							<b>Scan QR</b>
						</span>
					</div>
				</div>
				-->
			</div>

			<div class="row" style="">
			<div class="col-md-6 pr-0 pl-0"
				style="min-width: 302px !important;">

				<button class="btn btn-success btn-sq-lg screend-button
					bg-virtualcabinet"
					onclick="window.open('{{route('fuel-receipt-list',
						['date'=>date('Y-m-d',strtotime(now()))] )}}',
						'_blank')"
					style="margin-left:0 !important;outline:none;
					font-size: 14px; width: 70px !important;">
					<span style="">Today<br>Cabinet</span>
				</button>
				<button onclick="pumpCancel(2)" class="btn btn-sq-lg
					numpad-cancel-payment2
					poa-button-number-payment-cancel-disabled"
					style="display: none">Cancel
				</button>
				<button style="margin-left:0 !important"
					class="btn btn-sq-lg
					numpad-zero-payment2
					poa-button-number-payment-zero-disabled"
					onclick="set_cash('zero')"> Zero
				</button>
				<button class="btn poa-finish-button-disabled
					finish-button-0" onclick="process_finish(0)"
					style="margin-left:2px !important;width: 145px !important;">
					Enter
				</button>
			</div>
			</div>
		</div>
		</div>
		<!-- End pump-0 section -->


		<!-- Start common pump-N section -->
		@for($i = env('MAX_PUMPS'); $i>=1; $i--)
		<div style="" hidden class="overlay2" id="overlayover-{{$i}}"></div>
		<div style="" hidden class="overlay2" id="ft-overlay-sa-{{$i}}"></div>
		<div class="row m-0 pt-2 pb-0 pl-0 pr-0 col-md-12"
			id="pump-main-block-{{$i}}"
			style="display: none;align-items:flex-end">
			<div class="col-md-12 pt-0 mt-0 pb-0 m-0 pr-0"
				style="padding-left:15px;line-height:1.05">
				<span id="payment-type-message{{$i}}"
					class=""
					style="color:white;
					font-size:30px;font-weight: bold;">
				</span>
				<span  id="payment-type-paid-right{{$i}}"
					class="float-right"
					style="color:white;font-weight:500;
					font-size:30px;font-weight: bold;padding-right:25px">
				</span>
			</div>

			<div class="col-md-12 pt-0 pb-0 m-0 pr-0"
				style="
					padding-left:15px">
				<div class="row">
					<span  id="payment-amount-card-amount{{$i}}"
						class=""
						style="color:white;font-weight:500;
						font-size:30px;font-weight: bold;padding-right:5px">
					</span>
					<span  id="payment-type-amount{{$i}}"
						class="float-right"
						style="color:white;font-weight:500;
						font-size:30px;font-weight: bold;padding-right:25px">
					</span>
				</div>

				<div class="row" style="margin-bottom:5px !important">
					<div style="padding-right:20px;min-height: 280px;"
						class="col-md-12 pl-0 mt-1"
						id="payment-div-cash{{$i}}">

						<div class="flex_table" style="
							">
						<div class="col-md-12 mt-auto w-100 mr-0 pr-1 pl-0"
							style="height: 115px;">
							<div style="font-weight:bold" class="row p-0 m-0 text-white">
								<div class="col-4 p-0 m-0">
									Description
								</div>

								<div class="col-3 text-center">
									Price
								</div>

								<div class="col-3 text-center">
									Qty
								</div>

								<div class="col-2 p-0 m-0 text-right">
									{{$currency ?? ''}}
								</div>
							</div>

							<div class="row p-0 m-0 text-white">
								<div class="col-4 p-0 m-0" id="table-PRODUCT-{{$i}}">
								</div>

								<div class="col-3 text-center" id="table-PRICE-{{$i}}">
								</div>

								<div class="col-3 text-center" id="table-QTY-{{$i}}">
								</div>

								<div class="col-2 p-0 m-0 text-right" id="table-MYR-{{$i}}" amt-val="table-MYR-{{$i}}">
								</div>
							</div>

								<hr class="" style="margin-top:5px !important;
								margin-bottom:5px !important;
								border:0.5px solid #a0a0a0">
							<div class="d-flex bd-highlight">
								<div class="mr-auto bd-highlight text-white">
									Item Amount
								</div>
								<div class="bd-highlight text-white">
									<span id="item-amount-calculated-{{$i}}">0.00</span>
								</div>
							</div>
							<div class="d-flex bd-highlight">
								<div class="mr-auto bd-highlight text-white">
									SST {{$terminal->tax_percent}}%
								</div>
								<div class="bd-highlight text-white">
									<span id="sst-val-calculated-{{$i}}">0.00</span>
								</div>
							</div>
							<div class="d-flex bd-highlight">
								<div class="mr-auto bd-highlight text-white">
									Rounding
								</div>
								<div class="bd-highlight text-white">
									<span id="rounding-val-calculated-{{$i}}">0.00</span>
								</div>
							</div>

							<hr class="" style="margin-top:5px !important;
								margin-bottom:5px !important;
								border:0.5px solid #a0a0a0" />

							<div class="d-flex bd-highlight">
								<div class="mr-auto bd-highlight text-white">
									Total
								</div>
								<div class="bd-highlight text-white">
									<span id="grand-total-val-calculated-{{$i}}">0.00</span>
								</div>
							</div>

							<div class="d-flex bd-highlight">
								<div class="mr-auto bd-highlight text-white">
									Change
								</div>
								<div class="bd-highlight text-white">
									<span id="change-val-calculated-{{$i}}">0.00</span>
								</div>
							</div>
						</div>

						<div class="" style="height:140px;display: flex;align-items: flex-end;">
							<div class="col-md-12 pr-0">
								<div class="row mr-0 ml-0" style="display: flex;
									align-items: flex-end;
									margin-bottom: 5px;">

								<!-- Display product image -->
								<div style="position:relative;left:-30px;"
									class="col-md-3 text-center">
									<img src="" alt=""
										style="width:100px; height:100px;
										object-fit:contain;display:none;"
										id="display-product-thumb">
								</div>

								<div class="col-md-4 pl-0">
									<div class="text-white"
										id="display-product-name"
										style="line-height: 1.5em;
										height: 3em; overflow: hidden;">
									</div>
									<span class="text-white"
										id="display-product-systemid">
									</span>
								</div>

								<div class="col-md-3 row pr-0">
									<div class="text-right col-md-12 pr-0">
										<span class="text-white"
											id="display-product-price">
										</span>
									</div>
								</div>

								<div class="col-md-2 ml-0 pr-0"
									style="position:relative;left:30px">
									<div style="float:right;padding-right:0;">
									<button class="btn btn-sq-lg btn-success
										cstore-redcrab-button"
										id="cstore-redcrab-btn"
										style="z-index:9999;display:none;
										margin-bottom:0px !important;" onclick="">
										<i style="margin-top:-8px;
											padding-left:0;padding-right:0;
											font-size:80px" class="fa fa-times-thin">
										</i>
									</button>
								</div>
								</div>
								</div>
							</div>
						</div>

						<!-- padding-left:15px or -->
						<div class="row ml-0 mr-0"
							style="padding-left:0; margin-bottom:5px">

							<!-- <div class="col-md-12 pl-0 pr-0 payment_btns" style="">
							</div> -->

							<div class="col-md-12 row p-0 ml-0 mr-0
								payment_btns align-items-center"
								style="display:none; text-align: center;">

								<!--
								<button style="margin-bottom:0 !important"
									class="btn btn-success lg-custom-button
									poa-cash-btn
									poa-button-cash w-100"
									onclick="select_cash()" id="">Cash
								</button>
								<button style="margin-bottom:0 !important"
									class="btn btn-success lg-custom-button
									poa-button-credit-card w-100 poa-card-btn"
									onclick="select_credit_card()">Credit Card
								</button>
								<button style="margin-bottom:0 !important"
									class="btn btn-success lg-custom-button
									bg-point poa-button-cash-card w-100" id="">
									Discount
								</button>
								-->

								<input class="" type="number" style="background-color:#f0f0f0;
									border-width:0;font-size:16px;border-radius:10px;
									height: 70px !important; width:145px;outline: none;
									text-align:center; float: left;"
									placeholder="Item  Discount"
									id="item_disc" min="1" max="100"
									maxlength="3" size="3"
									onkeypress="if(this.value.length==3) return false;">

								<button class="" id="show_discount_percent"
									onclick="" style="margin-left:0;margin-right:0;
									display:inline;color:white;font-size:40px;
									background:transparent;
									border:none;
									pointer-events:none;
									width:auto;height:70px;">
									0
								</button>

								<button class="btn btn-sq-lg btn-success
									cstore-redcrab-button"
									id="cstore-redcrab-btn-end" style="
									border-radius:10px;
									margin-bottom:0 !important;
									text-align: center;
									font-size: 12px;
									width: 70px !important;
									margin-left: 5px !important;
									float: right;
									">
									Clear Discount
								</button>

								<button class="btn btn-success
									cstore-button-receiptdisc"
									id="cstore-button-receiptdisc-end"
									onclick="" style="border-radius:10px;
									margin-bottom:0 !important;
									font-size:36px;width:70px;height:70px;
									float:right;">%
								</button>
							</div>
						</div>
					</div>

					<div class="eightbuttons">
						<div class="col-md-12 mt-auto pt-2 pb-0 m-0 pl-0 pr-0"
							style="height: 228px;">
							<div class="row" style="height: 50px;">
								<div style=""
									class="col-md-6 p-0">
									<div style="position:relative;left:-5px;top:-27px; height:70px;"
										class="text-right">
										<img src="{{asset('images/dispenser_icon.png')}}"
											style="transform:scaleX(-1);
											width:70px;height:70px;object-fit:contain"/>
									</div>
									<?php
										if ($i > 9) {
											$left = "28px";
										} else {
											$left = "37px";
										}
									?>

									<div style="position:absolute;left:{{ $left }};top:25px"
										class="">
										<div class="ml-0 p-0" style="text-align:center;
											position:relative; left: 14px; top: -27px;
											font-size: 28px; color:white;"
											id="pump-number">
											<b id="pump-number-main-{{$i}}" style="color: black; font-size: 25px">{{$i}}</b>
										</div>
									</div>
								</div>

							</div>

							<div class="row float-right">
								<div style="justify-content:flex-end;display:flex"
									class="col-md-12 float-right">
									<button class="btn btn-sq-lg poa-button-number-disabled
										button-number-amount"  id="set-default-preset-button-{{$i}}-two"
										onclick="set_amount(2 , 'two')" >2
									</button>

									<button class="btn btn-sq-lg poa-button-number-disabled
										button-number-amount"  id="set-default-preset-button-{{$i}}-five"
										onclick="set_amount(5 , 'five')">5
									</button>
								</div>
							</div>
							<div class="row float-right">
								<div style="justify-content:flex-end;display:flex"
									class="col-md-12 float-right">
									<button class="btn btn-sq-lg poa-button-number-disabled
										button-number-amount" id="set-default-preset-button-{{$i}}-ten"
										onclick="set_amount(10 , 'ten')">10
									</button>

									<button class="btn btn-sq-lg poa-button-number-disabled
										button-number-amount" id="set-default-preset-button-{{$i}}-twenty"
										onclick="set_amount(20 , 'twenty')">20
									</button>
								</div>
							</div>
							<div class="row float-right">
								<div style="justify-content:flex-end;display:flex"
									class="col-md-12 float-right">
									<button class="btn btn-sq-lg poa-button-number-disabled
										button-number-amount" id="set-default-preset-button-{{$i}}-fifty"
										onclick="set_amount(50 , 'fifty')">50
									</button>

									<button class="btn btn-sq-lg poa-button-number-disabled
										button-number-amount" id="set-default-preset-button-{{$i}}-hund"
										onclick="set_amount(100 , 'hund')">100
									</button>
								</div>
							</div>
							<div class="row float-right">
								<div style="justify-content:flex-end;display:flex"
									class="col-md-12 float-right">
									<button class="btn btn-sq-lg poa-button-number-disabled
										button-number-amount" id="set-default-preset-button-{{$i}}-onefifty"
										onclick="set_amount(150 , 'onefifty')">150
									</button>

									<button class="btn btn-sq-lg poa-button-number-disabled
										button-number-amount" id="set-default-preset-button-{{$i}}-eighthund"

										onclick="set_amount(800 , 'eighthund')">800
									</button>
								</div>
							</div>
							<div class="row float-right">
								<div class="col-md-12 float-right pl-0">
									{{-- <button class="btn poa-authorize-disabled"
									style="" id="authorize-button__" >Authorize
									</button> --}}
									</div>
								</div>
							</div>

						</div>

						<input class="justify-content-center align-items-center"
							style="display:flex; background-color:#f0f0f0;
							width:295px !important;
							border-radius:10px;height: 50px !important;
							font-weight:500; font-size:30px;text-align:right"
							id="input-cash{{$i}}" placeholder="Cash Received"
							disabled>
						<input id="buffer-input-cash{{$i}}" type="hidden" />
					</div>

					<!--
					<div style="padding-right:20px;display:none"
						class="col-md-12 pl-0 mt-1" id="payment-div-cash-card{{$i}}">
						<div class="justify-content-center align-items-center"
							style="display:flex; background-color:black;
							border-radius:10px;height: 50px !important">
							<span
								class="text-center pl-3"
								style="color:#a0a0a0;font-weight:500;
								font-size:30px">
								<b>Scan QR</b>
							</span>
						</div>
					</div>
					-->
				</div>

				<div class="row" style="">
					<div class="col-md-6 pr-0 pl-0" style=" min-width: 302px !important;">
						<button class="btn btn-success btn-sq-lg screend-button
							bg-virtualcabinet"
							onclick="window.open('{{route('fuel-receipt-list',
							['date'=> date('Y-m-d', strtotime(now()))])}}','_blank')"
							style="margin-left:0 !important;outline:none;
								font-size: 14px;     width: 70px !important;">
						<span style="">Today<br>Cabinet</span>
						</button>

						<button	onclick="pumpCancel({{$i}})"
						class="btn btn-sq-lg
							numpad-cancel-payment{{$i}}
							poa-button-number-payment-cancel-disabled"
							style="display: none">Cancel
						</button>
						<button style="margin-left:0px !important"
							class="btn btn-sq-lg
							numpad-zero-payment{{$i}}
							poa-button-number-payment-zero-disabled"
							onclick="set_cash('zero')" > Zero
						</button>

						<button  class="btn poa-finish-button-disabled
							finish-button-{{$i}}"
							onclick="process_finish({{$i}})"
							style="margin-left:2px!important;width:145px !important;">
							Enter
						</button>

					</div>
				</div>
			</div>
		</div>
		@endfor
	</div>

	<!-- Squidster: 62px -> 0 when PUMPS < 16 -->
	<hr class="" style="position:relative;top:62px;
		margin-top: 3px !important;
		margin-bottom:5px !important;
		width: 440px;
		margin-left: 2px;
		border:0.5px solid #a0a0a0;"/>

	<div  style="margin-bottom:12px;width:460px;" >
	<div class="col-md-12 ml-0 pl-0 mb-1">

		<div style="float:left;">
			<a href="{{route('screen.d')}}" target="_blank"
				rel="noopener noreferrer">
				<button class="btn poa-bluecrab-button mb-1"
					style="float: left !important;"
					id="bluecrab_btn" onclick="">
					<i style="top:2px;margin-left:0 !important;
						margin-right:0; padding-left:0;
						padding-right:0;font-size:48px"
						class="far fa-circle"></i>
				</button>
			</a>

			<!-- This to replace cstore and the plain button
			<button style="float:left !important;
				pointer-events:none;
				margin-right:5px !important"
				class="btn btn-sql-lg phantom-button1">
			</button>
			-->

			<button class="btn opos-cstore-button"
				style="float: left !important" id="cstore_btn"
				onclick="window.open('{{ route("index.cstore") }}','_blank')">
				<img src="{{asset('images/basket_transparent.png')}}"
				style="width:45px;height:auto;object-fit:contain"/>
			</button>

			<button class="btn opos-h2-button"
				style="float: left !important" id="h2_btn"
				onclick="window.open('{{ route("h2-landing") }}','_blank')">
				<img src="{{asset('images/h2_logo4.png')}}"
				style="margin-left:0;margin-right:0;
					margin-top:0;width:40px;
					height:auto;object-fit:contain"/>
			</button>

			<button class="btn opos-ev-button"
				style="float: left !important" id="ev_btn"
				onclick="window.open('{{ route("car_park_landing") }}','_blank')">

				<img src="{{asset('images/ev_transparent.png')}}"
				style="margin-left:-2px;margin-top:-2px;width:50px;
					height:auto;object-fit:contain"/>
			</button>


			@for($i=1 ; $i<=env('MAX_PUMPS'); $i++)
			<button class="btn btn-success poa-button-cash
				poa-button-cash-disabled"
				id="button-cash-payment{{$i}}"
				onclick="select_cash()"
				style="margin-left:5px !important;display:none">Cash
			</button>
			@endfor

			<button class="btn btn-success poa-button-cash selected_preset_button
				poa-button-cash-disabled"
				id="button-cash-payment0" onclick="select_cash()"
				style="margin-left:5px !important">Cash
			</button>
		</div>
		<div style="float:left;">

			<input class="" type="number"
				style="background-color:#f0f0f0;
				float:left !important;
				border-width:0;font-size:20px;
				border-radius:10px;height: 70px !important;
				width: 145px;outline: none;text-align:center;"
				placeholder='0.00' id="custom_litre_input_0" disabled />

			@for($i=1 ; $i<=env('MAX_PUMPS'); $i++)
			<input class="hide" type="number"
				style="background-color:#f0f0f0;
				float:left !important;
				border-width:0;font-size:20px;
				border-radius:10px;height: 70px !important;
				width: 145px;outline: none;text-align:center;"
				placeholder='0.00' id="custom_litre_input_{{$i}}"/>

			<input class="hide" type="number"
				id="custom_litre_input_{{$i}}_buffer"/>

			@endfor

			<button class="btn mb-1 custom-preset-disable"
				id="custom_litre_btn"
				onclick="select_custom_litre()"
				style="margin-left: 5px !important; font-size:16px;
				float:left !important;
				padding-left:0 !important;padding-right:0 !important;
				cursor:pointer !important;">
				Preset<br>Litre
			</button>

			<button class="btn opos-topup-button"
				style="float-left !important" id="topup_btn"
				onclick="">
				<img src="{{asset('images/topup_transparent.png')}}"
				style="margin-top:-2px;height:40px;
					object-fit:contain"/>
			</button>

			@for($i=1 ; $i<=env('MAX_PUMPS'); $i++)
			<button class="btn btn-success poa-button-credit-card
				poa-button-credit-card-disabled"
				id="button-card-payment{{$i}}"
				onclick="select_credit_card()"
				style="float:right;display:none;">
				Credit Card
			</button>
			@endfor

			<button class="btn btn-success poa-button-credit-card
				poa-button-credit-card-disabled"
				id="button-card-payment0"
				onclick="select_credit_card()"
				style="float:right;">
				Credit Card
			</button>
		</div>

		<div style="clear:both">
			<input class="" type="number"
				style="background-color:#f0f0f0;
				float:left !important;
				border-radius:10px;height: 70px !important;
				border-width:0;font-size:20px;
				width: 145px;outline: none;text-align:center;"
				placeholder='0.00' id="custom_amount_input_0" disabled />

			@for($i=1 ; $i<=env('MAX_PUMPS'); $i++)
			<input class="hide" type="number"
				style="background-color:#f0f0f0;
				float:left !important;
				border-width:0;font-size:20px;
				border-radius:10px;height: 70px !important;
				width: 145px;outline: none;text-align:center;"
				placeholder='0.00' id="custom_amount_input_{{$i}}"/>

			<input class="hide" type="number"
				id="custom_amount_input_{{$i}}_buffer"/>
			@endfor

			<button class="btn mb-1 custom-preset-disable"
				id="custom_amount_btn"
				onclick="select_custom_amount()"
				style="margin-left: 5px !important; font-size:16px;
				float:left !important;
				padding-left:0 !important;padding-right:0 !important;
				margin-left:5px !important;
				cursor:pointer !important;">
				Preset<br>
				{{$currency ?? ''}}
			</button>

			<button class="btn btn-success btn-sq-lg poa-button-drawer"
				style="margin-left:5px !important;
				float:left !important;
				margin-right:0 !important; font-size:15px"
				onclick="open_cashdrawer()">Drawer
			</button>

			@for($i=1 ; $i<=env('MAX_PUMPS'); $i++)
			<button class="btn btn-success opos-button-credit-disabled
				"
				id="button-credit-ac{{$i}}"
				onclick="select_credit_ac({{$i}})"
				style="float:left !important;display:none;
					border-radius:10px;font-size:13px;
					width:145px;height:70px">Credit<br>A/C
			</button>
			@endfor

			<button  class="btn btn-success opos-button-credit-ac
				opos-button-credit-disabled"
				id="button-credit-ac0"
				style="float:left !important;width:145px;
					border-radius:10px;font-size:13px;
					height:70px; cursor: pointer">Credit<br>A/C
			</button>


			@for($i=1 ; $i<=env('MAX_PUMPS'); $i++)
			<button class="btn btn-success opos-button-wallet
				opos-button-wallet-disabled"
				id="button-wallet{{$i}}"
				onclick="select_wallet({{$i}})"
				style="float:left !important;display:none;
					border-radius:10px;">Wallet
			</button>
			@endfor

			<!-- it is original button which contains id and disable class -->
			<button class="btn btn-success opos-button-wallet
				opos-button-wallet-disabled"
				id="button-wallet0"
				onclick=""
				style="float:left !important;
					border-radius:10px;">Wallet
			</button>
		</div>
	</div>
	</div>
</div>

<hr class="hr" style="position:absolute;z-index:-9999">

<div class="col-md-8 m-0 p-0"
	style="margin-left:-40px !important;">
	<div class="col-md-12 m-0 p-0">
	<?php $s=1 ?>

	@for($i=1 ; $i<=env('MAX_ROWS'); $i++)
		<div class="row col-md-12 m-0 p-0" style="max-width:unset;width:104%">

		@for($j=1 ; $j<=env('MAX_COLUMNS'); $j++)
		<div class="col_panel">
		<div class="row col-md-12 col_panel_inner" style="
		margin-left: 0px;">
			<div hidden class="overlay1" id = "opt-overlay-{{$s}}">
				<span style="color: white; z-index: 1200; font-weight: 1000; font-size: 45px;"
					class="text-center">Reserved</span>
			</div>
			<div hidden class="overlay1" id = "ft-overlay-{{$s}}">
				<span style="color: white; z-index: 1200; font-weight: 1000; font-size: 45px;"
					class="text-center">Reserved</span>
			</div>
			<div hidden class="overlay1" id = "overlay-{{$s}}">
				<span style="color: white; z-index: 1200; font-weight: 1000; font-size: 45px;"
					class="text-center">Reserved</span>
			</div>
			<div hidden class="fulltank-overlay" style=""
				id = "fulltank-overlay-{{$s}}">
				<div class="row m-0 p-0" style="">
				<div style="position:relative;top:40px;left:12px"
					class="col-md-1 col_panel_inner">
					<h4 style="padding-left:2px;font-weight:700;color: #fff;">{{$s}}</h4>
				</div>
				<div class="col-md-6 pl-0 pr-0" style="">
				<div class="row" style="
					margin-left: 4px; margin-right: 3px;">
					<div class="col-md-12 col_panel_inner" style="color: white;">
						<span id="product-fulltank-{{$s}}"
							style="font-weight:bold;line-height: 1.5;">&nbsp;</span>
					</div>
					<div class="col-md-5 col_panel_inner"
						style="line-height:1.5;color: white; margin-right:0;padding-left:10px !important">
						<span>Filled</span>
						<span>Litre</span>
						<span>Price</span>
					</div>

					<div class="col-md-6 col_panel_inner line_before_number_fulltank text-right"
						style="line-height:1.5;color: white; margin-bottom:0 !important;
						margin-right:0 !important">
						<span id="total-final-filled-fulltank-{{$s}}">0.00</span>
						<span id="total-final-litre-fulltank-{{$s}}">0.00</span>
						<span id="fuel-product-price-fulltank-{{$s}}">0.00</span>
					</div>
					<div class="col-md-12 col_panel_inner"
						style="line-height:1.4;color: white; padding-left:10px !important">
						<span id="payment-status-fulltank-{{$s}}">Not Paid</span>
						{{-- <button style="height: 10px; width: 10px" onclick="nozzleUp({{$s}})"></button>
						<button style="height: 10px; width: 10px" onclick="nozzleDown({{$s}})"></button> --}}

					</div>
				</div>
				</div>
				<div class="col-md-5 col_panel_inner">
				<div class="row col-md-12" style="margin-left: 0px; padding: 0;">
				<div class="col-md-6 col_panel_inner p-0 text-center" style="padding-right:0 !important">
					<button style="padding-left:0; padding-right:0;
						width: 50px !important; height: 45px !important;
						border-radius: 5px;"
						id="cancel-pump-fulltank-{{$s}}"
						class="btn btn-danger onHoverGreen"
						onclick="fullTankCancel({{ $s }})">
						<span style="font-size:12px;">Cancel</span>
					</button>
				</div>
				<div class="col-md-6 col_panel_inner p-0 text-center" style="">
					<button style="padding-left:0; padding-right:0;
						width: 50px !important; height: 45px !important; border-radius: 5px;"
						id="fulltank-next-{{$s}}"
						onclick="ft_next({{$s}})"
						class="btn btn-success onHoverGreen poa-finish-button-disabled">
						<span style="font-size:14px;">Next</span>
					</button>
				</div>
				</div>
				<div class="row col-md-12" style="margin-left: 0px; padding: 0;">
				<div class="col-md-6 col_panel_inner text-center"
					style="padding: 0;">
					<button style="padding-left:0; padding-right:0;
						width: 50px !important; height: 45px !important;
						border-radius: 5px; padding-top:5px !important;
						margin-left: 0px !important; line-height: 0.9 !important;"
						id="fulltank-creditcard-{{$s}}"
						class="btn btn-success lg-custom-button
						poa-button-credit-card w-100 poa-card-btn poa-button-credit-card-disabled"
						onclick="ft_select_credit_card({{$s}})">
						<span style="font-size:12px;">Credit Card</span>
					</button>
				</div>

				<div class="col-md-6 col_panel_inner text-center"
					style="padding: 0;">

				<button style="padding-left:0; padding-right:0;
					width: 50px !important; height: 45px !important;
					margin-left: 0px !important;
					border-radius: 5px;"
					class="btn btn-success opos-button-wallet
					opos-button-wallet-disabled"
					id="fulltank-wallet-{{$s}}"
					onclick="select_full_tank_wallet({{$s}})">
					<span style="font-size:12px;">Wallet</span>
				</button>
				</div>
				</div>
				</div>
				</div>
				<div class="row m-0 ml-1 p-0" style="">
				<div class="col-md-7 m-0 p-0" style="">
					<div class="row col-md-12 p-0 m-0" style="">
					<div class="col_panel_inner" style="width:80px !important">
						<input class="justify-content-center align-items-center"
						style="display:flex; background-color:#f0f0f0;
						width:100% !important;
						border-radius:5px;height: 45px !important;
						font-weight:500; font-size:15px;text-align:right"
						id="input-cash-fulltank-{{$s}}" placeholder=""
						disabled>
						<input id="buffer-input-cash-ft{{$s}}"
							type="hidden"/>
					</div>

					<div class="col_inner_panel text-center m-0 p-0"
						style="">
						<button style="padding-left:0; padding-right:0;
						width: 60px !important; height: 45px !important;
						margin-left:5px !important; border-radius: 5px;"
						id="fulltank-finish-{{$s}}"
						class="btn poa-finish-button-disabled"
							onclick="process_receipt_ft({{$s}})">
							<span style="font-size:13px;">
								Enter
							</span>
						</button>
					</div>
					</div>
					</div>

					<div class="col-md-5 col_inner_panel m-0 p-0" style="">
					<div class="row col-md-12 m-0 p-0" style="">
						<div class="col-md-6 col_inner_panel text-center m-0 p-0"style="">
							<button style="padding-left:0; padding-right:0;
							width: 50px !important; height: 45px !important;
							margin-right:2px !important; border-radius: 5px;
							margin-left: 0px !important;
							"
							class="btn btn-success poa-button-cash
							poa-button-cash-disabled"
							id="fulltank-cash-{{$s}}"
							onclick="ft_select_cash({{$s}})">
								<span style="font-size:14px;">Cash</span>
							</button>
						</div>
						<div class="col-md-6 col_inner_panel text-center m-0 p-0"style="">
							<button style="padding-left:0; padding-right:0;
							width: 50px !important; height: 45px !important;
							border-radius: 5px; padding-top:5px;
							line-height: 0.9 !important;
							margin-left: 0px !important;
							"
							id="fulltank-creditac-{{$s}}"
							onclick="ft_select_credit_ac({{$s}})"
							class="btn btn-success opos-button-credit-disabled">
							<span style="font-size:12px;">Credit A/C</span>
						</button>
						</div>
					</div>
					</div>
				</div>
			</div>

			<div class="col-md-4 col_panel_inner">

			<!-- Squidster WARNING:
				This is to force ENABLE pump buttons instead of
				poa-button-pump-offline  -->
			<button class="btn poa-button-pump-offline mb-1"
				id="pump-button-{{$s}}" disabled >
				<img src="{{asset('images/dispenser_icon.png')}}"
					style="transform:scaleX(-1);width:32px;
					margin-top:-6px; height:32px;
					object-fit:contain;margin-left:0"/>
				<br>
				<div class="text-center pl-0 pr-0"
					style="margin-top:3px;font-size:18px;">
					{{$s}}
				</div>
				<p style="font-size: 0.7em;"
					id="pump-status-{{$s}}">Offline
				</p>
			</button>
			</div>
			<div class="col-md-4 col_panel_inner" style="color: white">
				<span>Fuel</span>
				<span>Filled</span>
				<span>Litre</span>
				<span>Price</span>

				<span id="payment-status-{{$s}}">Not Paid</span>

				<!--
				<span style="display: none; color:rgb(11, 167, 11);
					font-weight:bold"
					id="authorize-status-{{$s}}">AUTHORIZED
				</span>
				-->
			</div>

			<div class="col-md-4 line_before_number"
				style="color: white">
				<span id="total-fuel-pump-{{$s}}">0.00</span>
				<span id="total-final-filled-{{$s}}">0.00</span>
				<span id="total-final-litre-{{$s}}">0.00</span>
				<span id="fuel-product-price-{{$s}}">0.00</span>
				<b style="display: none"
					id="total_amount-main-{{$s}}">0.00
				</b>
			</div>

			<div class="col-md-12 p-0 m-0">
				<!-- Display selected product --->
				<div style="height:45px;width: 100%;margin-left: 7px;"
				class="align-items-right">

				<!-- Display grey cover when authorised -->
				<div style="cursor:pointer;"
					class="scover-grey" id = "scover-grey-{{$s}}">
					<button
						hidden
						id = "scover-grey-btn-{{$s}}"
						onclick="voidPumpAndDeAuthorize('{{$s}}')"
						class="btn btn-danger onHoverGreen cancel-pump-number-{{$s}}"
						style="height: 100%;width: 43%;border-radius: 10px;">
						Cancel
					</button>
				</div>

				<!-- Display blue cover over products -->
				<div style="cursor:pointer;" onclick="remove_start_cover('{{$s}}')"
					class="scover-blue" id = "scover-{{$s}}">
					<span style="margin-left: 10%;color: white; z-index: 1200; font-size: 25px;"
						class="text-center">New
					</span>
					<div style="width:130px;margin-left:10px">
					</div>
				</div>

				<!-- Display product buttons -->
				<div class="row text-white"
					id="product-select-pump-{{$s}}"
					style="margin-left:0; ">
					<button class="btn btn-success fulltank-button fulltank-button-disabled"
						id="fulltank-button-{{$s}}" onclick="fulltank_authorize({{$s}})">
						Full
					</button>
					@if (!empty($productData))
					@foreach ($productData as $product)
			 		@if (!empty($nozzleFuelData->
						where("product_id",$product->id)->
						where("pump_no",$s)->first()))

						@if (!empty($product->og_id))
						<span style="margin-top:2px;margin-right:7px">
						<img class="iHover fuelproductimages-{{$s}}"
							src='/images/product/{{$product->systemid}}/thumb/{{$product->thumbnail_1}}'
							id="fuel-grad-thumb-{{$s}}-option-{{$product->id}}"
							onclick="selectProduct('{{$s}}',
							'{{$product->og_id}}',
							'{{$product->id}}',
							'{{$product->name}}',
							'/images/product/{{$product->systemid}}/thumb/{{$product->thumbnail_1}}')"
							bakclick="selectProduct('{{$s}}',
							'{{$product->og_id}}',
							'{{$product->id}}',
							'{{$product->name}}',
							'/images/product/{{$product->systemid}}/thumb/{{$product->thumbnail_1}}')"
							onmouseover="confirmProductSelect('{{$s}}')"
							prod_name = "{{$product->name}}"
							prod_img = "/images/product/{{$product->systemid}}/thumb/{{$product->thumbnail_1}}"
							prod_id = "{{$product->id}}"
							style='width:45px;height:45px;display:inline-block;
							/*cursor:auto*/;object-fit: contain;
							border:2px solid white;border-radius:5px;
							margin-right:0;' />
						</span>
						@endif


					@endif
					@endforeach
					@endif
				</div>
			</div>
			</div>
			</div>
		<?php $s++; ?>
		</div>
		@endfor
		</div>
	@endfor
	</div>
</div>

@include("common.footer")

</div>
</div>
@endauth

<div class="modal fade @guest show @endguest " id="userEditModal" tabindex="-1"
	role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true"
	style="padding-right:0 !important; @auth display:none @endauth ">

	<div class="modal-dialog modal-dialog-centered" role="document"
		style=" width: 100% !important; height: 100% !important; margin: 0;
		padding: 0;max-width:100%;max-height:100%">

		<div class="modal-content modal-inside bg-black"
			style="height: auto; min-height: 100% !important;border-radius:0;">
			<div class="modal-body text-center"
				style="vertical-align: middle !important ;">
				<img style="position:relative;top:50px;width:250px;height:300px;object-fit:contain"
					src="{{ asset('images/opossum_vpos.png') }}">
				<br>
				<!--
				<p class="mb-0" style="margin-bottom:0;margin-top:20px;
					font-size:80px;font-weight:550;line-height:1.0">
				OPOSsum
				</p>
				-->
				<div class="row align-items-center">

				@if (!empty($isLocationActive) &&
					(!empty($isTerminalActive) || $isServerEnd) )

				<!-- This is only for OPOSsum Terminal Login -->
				<div style="display:flex;"
					class="col-md-3 align-items-center pl-0 pr-0">
					<div id="login-message"
						style="font-size:20px;color:yellow;line-height:1.3">
					</div>
					<div style="font-size:20px;color:yellow;line-height:1.3"
						class="text-center login_error">
					</div>
				</div>

				{{-- <div style=""
					class="col-md-6 pl-0 pr-0 text-center">
					<img style="position:relative;top:-10px;
						cursor:pointer;
						width:100%;height:390px;object-fit:contain"
						onclick="login_form_toggle()"
						src="{{ asset('images/anim_torus.gif') }}"/>
				</div> --}}
				<div style=""
					class="col-md-3 pl-0 pr-0 d-flex justify-content-center">
					<div id="log"
						style="padding:20px auto;display:block; margin-top:50%;"class="" >
						<form autocomplete="off" id="formLogin">
                            <input type="hidden" name="hosting"
								value="opossum" id="hosting"/>
							<input autofocus
								class="text-center form-control login_field"
								style="width:100%"
								id="email" name="email"
								autocomplete="off"
								type="text" placeholder="Email"/>

							<input autofocus
								class="text-center form-control login_field"
								style="width:100%"
								id="password" name="password"
								type="password" placeholder="Password"/>
						</form>
						<button style="width:100%"
							class="btn-primary btn-md
							custom_login_btn" onclick="login_me()" id="loginBtn">
							<span style="position:relative;top:-1px">
							Log In
							</span>
						</button>
					</div>
				</div>

				@else
				<!-- This is only for OPOSsum Terminal Setup -->
				<div class="mt-4 col-sm-12">
				<div style="display: flex; justify-content: center;">
					<input autofocus class="form-control keydigit" type="text"
						id="key_1" maxlength="1">
					<input disabled class="form-control keydigit" type="text"
						id="key_2" maxlength="1">
					<input disabled class="form-control keydigit" type="text"
						id="key_3" maxlength="1">
					<input disabled class="form-control keydigit" type="text"
						id="key_4" maxlength="1">&nbsp;&nbsp;

					<input disabled class="form-control keydigit" type="text"
						id="key_5" maxlength="1">
					<input disabled class="form-control keydigit" type="text"
						id="key_6" maxlength="1">
					<input disabled class="form-control keydigit" type="text"
						id="key_7" maxlength="1">
					<input disabled class="form-control keydigit" type="text"
						id="key_8" maxlength="1">&nbsp;&nbsp;

					<input disabled class="form-control keydigit" type="text"
						id="key_9" maxlength="1">
					<input disabled class="form-control keydigit" type="text"
						id="key_10" maxlength="1">
					<input disabled class="form-control keydigit" type="text"
						id="key_11" maxlength="1">
					<input disabled class="form-control keydigit" type="text"
						id="key_12" maxlength="1">&nbsp;&nbsp;

					<input disabled class="form-control keydigit" type="text"
						id="key_13" maxlength="1">
					<input disabled class="form-control keydigit" type="text"
						id="key_14" maxlength="1">
					<input disabled class="form-control keydigit" type="text"
						id="key_15" maxlength="1">
					<input disabled class="form-control keydigit" type="text"
						id="key_16" maxlength="1">
				</div>

				<div class="mt-3 row align-items-center"
					style="width: 50%;margin:auto;justify-content: center;">

					<div class="col-5">
						<input autofocus
							class="text-center form-control pre_setup_field"
							id="terminal_id_field"
							type="text" placeholder="Terminal ID"/>
					</div>

					<div class="col-2">
						<button onclick="activateLicence()"
							class="btn-primary btn-md
							custom_activate_btn">Set Up
						</button>
					</div>
				</div>
				</div>

				<div class="col-md-10 align-items-center m-auto">
					<div id="login-message"
						style="font-size:20px;color:yellow;width: 100%;
							text-align: center;">
					</div>
					<div style="font-size:20px;color:yellow;width: 100%;
						text-align: center;"
						class="pl-5 login_error">
					</div>
				</div>
				@endif
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog"
style="padding-right:0 !important"
aria-labelledby="cancelModalLabel"
aria-hidden="true">
<div class="modal-dialog modal-dialog-centered  mw-75 w-50" role="document">
    <div class="modal-content modal-inside bg-purplelobster">
        <div style="border:0" class="modal-header"></div>
        <div class="modal-body text-center">
            <h5 class="modal-title text-white" id="logoutModalLabel">
           Would you like to cancel the current transaction?</h5>
        </div>

        </div>
    </div>
</div>


<div class="modal fade"  id="inProgressModal"  tabindex="-1" role="dialog"
 	aria-hidden="true" style="text-align: center;">
    <div class="modal-dialog modal-dialog-centered  mw-75 w-50" role="document"
     style="display: inline-flex;">

	 <div class="modal-content modal-inside bg-purplelobster">
        <div style="border:0" class="modal-header">&nbsp;</div>
        <div class="modal-body text-center">
            <h5 class="modal-title text-white">
				The delivering is in progress.</h5>
        </div>
        <div style="border:0" class="modal-footer">&nbsp;</div>
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
            <div class="modal-body text-center pt-0 pb-0">
                <h5 class="modal-title text-white" style="padding-bottom:0"
					id="statusModalLabelMsg">
				</h5>
            </div>
            <div class="modal-footer" style="border:0">&nbsp;</div>
        </div>
    </div>
</div>

<!-- Popup for Credit Account List start -->
<div id="cal" class="modal fade" style='scrollbar-width: thin;' tabindex="-1" role="dialog"
	 aria-hidden="true">
	<div class="modal-dialog modal modal-dialog-centered" style="margin: auto;">
		<div style="border-radius:10px;scrollbar-width: thin;"
			 class="modal-content bg-purplelobster">
			<div class="modal-header">
				<h3 style="margin-bottom:0">Credit Account List</h3>
			</div>
			<div class="modal-body" style="">
			<div class="row" style="width:100%">
				<div class="col-md-12" style="">
				<div id="call" class="creditmodelDV"
					style="display:flex; flex-wrap: wrap;
					justify-content: flex-start;scrollbar-width: thin;">
				</div>
				</div>
			</div>
			</div>
		</div>
	</div>
</div>

<!-- Popup for Credit Account List end -->
<div class="modal fade" id="wallet_instruction_modal" tabindex="-1"
	role="dialog" style="padding-right:0 !important" aria-labelledby=""
	aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered  mw-75 w-50" role="document">
      <div class="modal-content modal-inside bg-purplelobster">
         <div style="border:0" class="modal-header">
            <button class="close " type="button" data-dismiss="modal"
				aria-label="Close">
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

<div class="modal fade" id="select_product_modal" tabindex="-1"
	role="dialog" style="padding-right:0 !important" aria-labelledby=""
	aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered  mw-75 w-50" role="document">
      <div class="modal-content modal-inside bg-purplelobster">
         <div style="border:0" class="modal-header">
            <button class="close " type="button" data-dismiss="modal"
				aria-label="Close">
            <span aria-hidden="true"></span>
            </button>
         </div>
         <div class="modal-body text-center" id="start-wallet">
            <h5 class="modal-title text-white">
               Please select Pump, Product, Amount, Payment and press Enter.
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
            <button class="close clw" type="button" data-dismiss="modal"
				aria-label="Close">
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
			<button class="close " type="button" data-dismiss="modal"
			 	aria-label="Close">
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
				<h5 class="mt-3" id="amt" data-id="{{ $currency ?? '' }}">
				Sending order amount {{$currency ?? ''}}
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

<div class="modal fade" id="sarawak_pay_full_fulltank" tabindex="-1" role="dialog"
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
				<div class="pt-2 d-none" id="inquirebtn-full-tank">
				<button class="btn btn-success opos-topup-button"
					id="query_order_btn_full_tank"
					style="height:35px !important; width:80px !important; border-radius:5px"
					onclick="query_order_full_tank();">Inquire
					</button>
					</div>
			<div id= "sc-full-fulltank">
				<h5 class="mt-3" id="amt" data-id="{{ $currency ?? '' }}">
				Sending order amount {{$currency ?? ''}}
				<span id="spay_order_amt_full_tank">0.00</span>
				to S Pay Global...
				</h5>

			</div>
			<div>
				<h5 class="mt-3" id="overwrite_full_tank">
				</h5>
			</div>
		</div>
         <div style="border:0" class="modal-footer"></div>
      </div>
   </div>
</div>
<div class="modal fade" id="listMerchantModal" tabindex="-1"
	 role="dialog" aria-labelledby="staffNameLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered  mw-75 w-50"
		 role="document">
		<div class="modal-content modal-inside bg-purplelobster">
			<div class="modal-header" style="font-size: 15pt">
				Credit Account
			</div>
            <hr>
			<div class="modal-body text-center">
				<div id="dataList" class=""
					style="widows: 100%; height: 300px; overflow-y: auto">
				</div>
			</div>
		</div>
	</div>
</div>

<div id="productsModal" class="modal fade" tabindex="-1" role="dialog"
	 aria-hidden="true">
<div class="modal-dialog modal modal-dialog-centered" style="margin: auto;">
	<div style="border-radius:10px"
		 class="modal-content bg-purplelobster">
	<div class="modal-header">
		<h3 style="margin-bottom:0">Calculation: Litre to MYR</h3>
	</div>
	<div class="modal-body" style="">
		<div class="row" style="width:100%">
		<div class="col-md-12" style="">
			<div id="productList" class="creditmodelDV"
				 style="display:flex; flex-wrap: wrap;
				 justify-content: flex-start;">

			<div class="row" style="width:100%">
				<div class="col-md-12" style="">
				<div id="productList" class="creditmodelDV"
					style="display:flex; flex-wrap: wrap;
					justify-content: flex-start;">

					@foreach ($productData as $product)

					<div class="col-md-12 ml-0 pl-0">
						<div class="row align-items-center d-flex">
						<div class="col-md-2">
							<img class="thumbnail productselect sellerbutton"
								style="padding-top:0;object-fit:contain;
								float:right;width:30px !important;
								height:30px !important;margin-left:0;
								margin-top:2px;margin-right:0;margin-bottom:2px"
								src="/images/product/{{$product->systemid}}/thumb/{{$product->thumbnail_1}}">
						</div>
						<div class="col-md-10 pl-0 productselect"
							style="cursor:pointer;line-height:1.2;
							margin-left:0;font-size:20px;
							padding-top:0;text-align: left;"

							@if (!empty($product->price))
							onclick="calculate_fuel_price({{$product->price}})"
							@endif
							>
							{{$product->name}}
						</div>
						</div>
					</div>
					@endforeach

				</div>
				</div>
			</div>
			</div>
		</div>
		</div>
	</div>
	</div>
</div>
</div>

@endsection

@section('script')

@if (!empty($isLocationActive) && (!empty($isTerminalActive)))
	@include('landing.opos2_fuelpage')
	@include('landing.opos_fuelpage')
	@include('landing.opos_pumpingloop')
	@include('landing.opos_prepaid')
    @include('landing.opos_wallet')
    @include('fuel_fulltank.fuel_fulltank')
	@include('fuel_fulltank.fulltank_wallet')
	@include('outdoor_payment.opt_dynamic_prepaid')
@else
	@include('landing.license')
@endif

<script>
$(document).ready(function() {
    /* code here */
  document.getElementById('log').css('display','none');
});
$.ajaxSetup({
	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
});

function select_credit_ac(selected_pump) {
	let prd_selected = show_select_pump_modal()
	if (prd_selected) {
		if (selected_pump != 0) {
			$.ajax({
				method: "post",
				url: "{{ route('creditaccount.listMerchantActive') }}",
			}).done((data) => {
				let dataList = data.data;
				console.log(data.data);

				$("#call").html("");
				for (let i = 0; i < dataList.length; i++) {
					if(dataList[i]['status'] == "active"){
						$("#call").append('<div onclick="selectac(' + dataList[i]["company_id"] + ')" class="col-md-12 pl-3 productselect" style="scrollbar-width: thin;cursor:pointer;line-height:1.2;margin:5px;font-size:20px;padding-top:0;text-align: left;">' + dataList[i]["name_company"] + '</div>');
					}
				}
					$("#cal").modal({
				    backdrop: 'static',
				    keyboard: false
				});
			}).fail((data) => {
				console.log("data", data)
			});

			$('#button-cash-payment' + selected_pump).
				removeClass('selected_preset_button');

			$('.finish-button-' + selected_pump).
				removeClass('poa-finish-button-disabled');

			$('.finish-button-' + selected_pump).addClass('opos-topup-button');
			$(`#buffer-input-cash${selected_pump}`).val('');
			$(`#input-cash${selected_pump}`).val('');
			$(`#input-cash${selected_pump}`).hide();
			dis_cash['pump' + selected_pump].dis_cash = "";
			dis_cash['pump' + selected_pump].payment_type = "creditac";
			$(`#change-val-calculated-${selected_pump}`).text('0.00');
			//$("#payment-div-cash"+selected_pump).hide();
			$(".payment-div-refund" + selected_pump).hide();
			$("#payment-div-cash-card" + selected_pump).hide();
			check_enter()
		}
	}
}

function select_credit_(selected_pump) {
	if (selected_pump != 0) {
		$.ajax({
			method: "get",
			url: "{{route('Clist.get')}}",
			success: function (data) {

				console.log(data);
				$("#cal").modal("show");
				$("#call").append(data);
			},
			error: function() {
				console.log(data);
			}
		});
	}
}


function selectac(company_id) {
	$.ajax({
		url: "{{route('creditaccount.receiptCreditAction')}}",
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').
			attr('content')},
		type: 'post',
		data: {
			companyId:company_id,
		},
		success: function (response) {
			console.log('CA '+JSON.stringify(response));
			$("#cal").modal('hide');

			// New line -- Save the creditact id
			localStorage.setItem("creditac_id",response.creditact_id);

		},
		error: function (e) {
			console.log('CA '+JSON.stringify(e));
		}
	});
}

</script>

<script>
$.ajaxSetup({
	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
});
function listMerchantData() {
	$.ajax({
		method: "post",
		url: "{{route('creditaccount.list')}}",
	}).done((data) => {

		let dataList = data.data;
		console.log(data.data);

		$("#dataList").html("");
		$("#listMerchantModal").modal("show");
		for (let i = 0; i < dataList.length; i++) {
			$("#dataList").append('<li class="p-2 text-left" style="width: 100%;">'+dataList[i]["name_company"]+'</li>');
		}

	}).fail((data) => {
		console.log("data", data)
	});
}

</script>

@endsection
