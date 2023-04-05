@extends('landing.web')
@section('subheader')
@endsection

@section('content')
<script type="text/javascript" src="{{asset('js/qz-tray.js')}}"></script>
<script type="text/javascript" src="{{asset('js/opossum_qz.js')}}"></script>
<script type="text/javascript" src="{{asset('js/JsBarcode.all.min.js')}}"></script>
<script type="text/javascript" src="{{asset('js/console_logging.js')}}"></script>

<style>
.input-div-0{
	min-height:54px!important;
}
.active-product{
	margin-top: 2px;
	/* margin-right: 5px;  */
	border-radius: 10px;
	border: 4px solid green;
}

.p-product{
	margin-top: 2px;
	/* margin-right: 2px;  */
	border-radius: 10px;
	border: 4px solid transparent;
}
.enter-btn{
	margin-left: 2px !important;
}
.presetmyr{
	background:linear-gradient(rgb(94, 213, 60),rgb(4, 162, 249))!important;
	color:white;
}

.noclick {
   pointer-events: none;
}
.row{
	margin-right: 0;
}
.fixed-bottom{
	z-index: 0;
}
::placeholder {
	text-align:center;
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
/*New CSS*/
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
	width: 367px;
	float: left;
	height: 122px;
	display: flex;
	justify-content: flex-start;
}
.line_before_number {
	border-left: #a0a0a0 1px solid;
    height: 78px;
    padding: 0px;
    padding-top: 0;
    padding-bottom: 0 !important;
    margin-left: 0px;
    margin-top: 6px;
    padding-left: 0px;
}
.col_panel_inner{
	padding: 5px !important;
    font-size: 14px;
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

@media screen and (min-width: 900px) {
	.mainrightsidediv	{
	 	min-height: 90%;
		/*right: -100px;*/
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
		min-height: 860px;
		left: 393px;
		position: absolute;
		top:-252px;
	}
}

@media screen and (min-width: 1500px) {
	.mainrightsidediv	{
	 	min-height: 96%;
		/*right: -100px;*/
		position: absolute;
		top: -55px;
		bottom: 0px !important;
		min-width: 1090px;
 	}
	.col_panel {
		width: 24%;
		height: 170px;
		padding-right: 5px;
	}
	.hr{
		min-height: 865px;
		left: 393px;
		position: absolute;
		top:-238px;
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

.h2-overlay {
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
</style>

@auth
@include("common.header")


<div class="fixed-bottom">
<div class="container-fluid" id="container-blur" style="margin-top:3%">

<div class="row">
	<div class="flex_table" style="width: 397px; height:50px;">
		<div style="padding-left:0"
			class="col-md-12 flex-row-reverse d-flex">

			<div class="col-md-3" style="margin-right: -7px;">
				<button class="btn btn-danger onHoverGreen
					cancel-pump-number-0
					poa-button-number-payment-cancel-disabled"
					style="padding-left:0; padding-right:0;" onclick="">
					<span style="font-size:14px;margin-left:-1px;">Cancel</span>
				</button>
			</div>

			<div class="col-md-9" style="padding-left:10px">
				<img src="{{asset('images/h2_logo.png')}}"
					style="width:70px;height:70px;object-fit:contain;float:left;margin-right: 5px;"/>

				<div class="ml-0 p-0" style="text-align: center;
				    top: 0px;
				    font-size: 28px;
				    color: white;
				    border: 1px solid;
				    float: left;
				    width: 70px;
				    height: 70px;
				    border-radius: 10px;
				    /*padding: 9px 24px  26px 0px !important;*/
				    padding: 10px !important;
				    display: none;  margin-right: 5px;"
					id="pump-number">
					<span id="active-pump"
						style="position:relative;top:2px">
					</span>
					<input type="hidden"
						id="paid-pump" value="">
					<input type="hidden"
						id="previous-pumps" pumpno="" prod="">
				</div>
				<b id="pump-number-main-0">&nbsp;&nbsp;</b>
			</div>

		</div>
	</div>
</div>

<div class="row pt-2 pb-0 m-0 pl-0 pr-0" style="">
	<div class="col-md-4 pt-2 pb-0 m-0 pl-0 pr-0"
		style="display:flex;align-items:flex-end">

		<!-- pump-main-block-0 is the default. This will be displayed
		before user clicks on any pump -->
		<div style="" hidden class="overlay2" id="overlayover"></div>
		<div class="row m-0 pt-2 pb-0 pl-0 pr-0 pump-main-block"
			id="pump-main-block-0" style="display:flex;align-items:flex-end">


			<!-- Start Section -->
			<div class="col-md-10 pt-0 pb-0 m-0 pr-0"
				style="padding-left:15px;width: 503px !important;">

				<div class="row" style="margin-bottom:5px !important">
				<div style="padding-right:20px;min-height: 200px;"
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
							<span id="item-amount-calculated-0">0.00</span>
						</div>
					</div>
					<div class="d-flex bd-highlight">
						<div class="mr-auto bd-highlight text-white">
							{{!empty($terminal->taxtype)?strtoupper($terminal->taxtype):"SST"}} {{$terminal->tax_percent}}%
						</div>
						<div class="bd-highlight text-white">
							<span id="sst-val-calculated-0">0.00</span>
						</div>
					</div>
					<div class="d-flex bd-highlight">
						<div class="mr-auto bd-highlight text-white">
							Rounding
						</div>
						<div class="bd-highlight text-white">
							<span id="rounding-val-calculated-0">0.00</span>
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
							<span id="grand-total-val-calculated-0">0.00</span>
						</div>
					</div>

					<div class="d-flex bd-highlight">
						<div class="mr-auto bd-highlight text-white">
							Change
						</div>
						<div class="bd-highlight text-white">
							<span id="change-val-calculated-0">0.00</span>
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
						</div>
					</div>
					</div>
				</div>
				</div>
			</div>

				<div class="row record1" style="margin-bottom:5px !important">
					<div style="padding-right:20px"
						class="col-md-12 pl-0 mt-1 row-std0">

						<input class="justify-content-center align-items-center"
						style="display:flex; background-color:#f0f0f0;
						width:367px !important;
						border-radius:10px;height: 50px !important;
						font-weight:500; font-size:30px;text-align:right"
						id="input-cash0" placeholder="Cash Received"
						disabled=""
						oninput='cashInput(this)'>

						<input id="cash-input-cash0" type="hidden">
						<input id="buffer-input-cash0" type="hidden">
					</div>
				</div>

				<div class="row">
					<div class="col-md-12 pr-0 pl-0" style="position: relative">

						<button class="btn btn-success btn-sq-lg screend-button
							bg-virtualcabinet"
							onclick="window.open('{{url('h2-receipt-list')}}/{{date("Y-m-d")}}','_blank')"
							style="margin-left:0 !important;outline:none;
								width:145px !important; font-size: 14px">
						<span style="">Today<br>Cabinet</span>
						</button>

						<button class="btn btn-sq-lg
							poa-button-number-payment-zero-disabled"
							onclick="set_cash('zero')">Zero
						</button>
						<button class="btn btn-sq-lg
							enter-btn
							poa-button-number-payment-zero-disabled"
							onclick="process_finish()" style=" width: 145px !important;">Enter
						</button>
					</div>
					<hr class="" style="
						margin-top: 3px !important;
						margin-bottom:5px !important;
						width: 366px;
						margin-left: 2px;
						border:0.5px solid #a0a0a0;">

					<div style="float:left;width:100%">
						<a href="{{route('screen.d')}}" target="_blank"
							rel="noopener noreferrer">
							<button class="btn h2-bluecrab-button mb-1"
								style="float: left !important;margin-right:5px !important"
								id="bluecrab_btn" onclick="">
								<i style="top:2px;margin-left:2px;margin-right:0;
									padding-left:0;padding-right:0;font-size:48px"
									class="far fa-circle"></i>
							</button>
						</a>

						<!-- H2 Exit button: -->
						<button class="btn opos-h2-button"
							style="float: left !important" id="h2_btn">
							<img src="{{asset('images/h2_logo_exit.png')}}"
								onclick="window.close()"
								style="margin-left:0;margin-right:0;
								margin-top:0;width:40px;
								height:auto;object-fit:contain"/>
						</button>

						<button class="btn btn-success h2-button-drawer"
							style="margin-left:5px !important; margin-right:5px !important;
							width:70px !important; float:left !important;
							font-size:15px"
							onclick="open_cashdrawer()">Drawer
						</button>

						<button class="btn btn-success poa-button-cash
							poa-button-cash-disabled"
							id="button-cash-payment0" onclick="select_cash()"
							style="margin-left:0 !important;float:left !important">Cash
						</button>
					</div>
					<div style="float:left; margin-bottom: 5px;">
						<input class="presetInput" type="number"
							style="background-color:#f0f0f0;
							float:left;
							border-width:0;font-size:20px;
							border-radius:10px;height: 70px !important;
							width: 145px;outline: none;text-align:center;"
							placeholder='0.00' id="custom_kg_input_0" disabled />

						<button class="btn mb-1 custom-preset-disable btnrecpt"
							id="custom_kg_btn"
							onclick="select_custom_preset()"
							style="margin-left: 5px !important; font-size:16px;
							padding-left:0 !important;padding-right:0 !important;
							margin-left:5px !important;cursor:pointer !important;">
							Preset<br>
							{{$currency ?? ''}}
						</button>

						<button class="btn btn-success poa-button-credit-card
							poa-button-credit-card-disabled"
							id="button-card-payment0"
							onclick="select_credit_card()"
							style="width: 70px !important;margin-left:0px !important;">Credit Card
						</button>

						<button class="btn btn-success opos-button-wallet
							opos-button-wallet
							opos-button-wallet-disabled"
							id="button-wallet0"
							onclick=""
							style="width: 70px !important;
							border-radius:10px;height:70px;
							margin-left: 0px !important;">Wallet
						</button>
					</div>


				</div>
			</div>
		</div>
		<!-- end section -->

		@for($i = env('H2_MAX_PUMPS'); $i>=1; $i--)
		<div style="" hidden class="overlay2" id="overlayover-{{$i}}"></div>
		<div style="" hidden class="overlay2" id="h2-overlay-sa-{{$i}}"></div>
			<div class="row m-0 pt-2 pb-0 pl-0 pr-0 pump-main-block"
				id="pump-main-block-{{$i}}" style="display:none;align-items:flex-end">


				<!-- Start Section -->
				<div class="col-md-10 pt-0 pb-0 m-0 pr-0"
					style="padding-left:15px;width: 503px !important;">

					<div class="row" style="margin-bottom:5px !important">
					<div style="padding-right:20px;min-height: 200px;"
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
							<div class="col-4 p-0 m-0" id="table-PRODUCT-{{$i}}"></div>

							<div class="col-3 text-center" id="table-PRICE-{{$i}}"></div>

							<div class="col-3 text-center" id="table-QTY-{{$i}}"></div>

							<div class="col-2 p-0 m-0 text-right"
								id="table-MYR-{{$i}}">0.00
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
								{{!empty($terminal->taxtype)?strtoupper($terminal->taxtype):"SST"}} {{$terminal->tax_percent}}%
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
							border:0.5px solid #a0a0a0">

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
							</div>
						</div>
						</div>
					</div>
					</div>
				</div>

				<div class="row record" style="margin-bottom:5px !important">
					<div style="padding-right:20px"
						class="col-md-12 pl-0 mt-1">
						<input class="justify-content-center
						cashInput
						align-items-center"
						style="display:flex; background-color:#f0f0f0;
						width:367px !important;
						border-radius:10px;height: 50px !important;
						font-weight:500; font-size:30px;text-align:right"
						id="input-cash{{$i}}" placeholder="Cash Received"

						oninput='cashInput(this)'
						disabled="">

						<input id="cash-input-cash{{$i}}" type="hidden">
						<input id="buffer-input-cash{{$i}}" type="hidden">
					</div>
				</div>

				<div class="row">
					<div class="col-md-12 pr-0 pl-0" style="position: relative">

						<button class="btn btn-success btn-sq-lg screend-button
							bg-virtualcabinet"
							onclick="window.open('{{url('h2-receipt-list')}}/{{date("Y-m-d")}}','_blank')"
							style="margin-left:0 !important;outline:none;
								width:145px !important; font-size: 14px">
						<span style="">Today<br>Cabinet</span>
						</button>

						<button class="btn btn-sq-lg
							zero-btn
							poa-button-number-payment-zero-disabled"
							onclick="set_cash('zero')">Zero
						</button>
						<button class="btn btn-sq-lg
							enter-btn
							poa-button-number-payment-zero-disabled"
							onclick="process_finish()"
							style=" width: 145px !important;">Enter
						</button>
					</div>
					<hr class="" style="
						margin-top: 3px !important;
						margin-bottom:5px !important;
						width: 100%;
						margin-left: 0;
						margin-right: 5px;
						border:0.5px solid #a0a0a0;">

					<div style="float:left; width:100%">
						<a href="{{route('screen.d')}}" target="_blank"
							rel="noopener noreferrer">
							<button class="btn h2-bluecrab-button mb-1"
								style="float:left !important; margin-right:5px !important"
								id="bluecrab_btn" onclick="">
								<i style="top:2px;margin-left:2px;margin-right:0;
									padding-left:0;padding-right:0;font-size:48px"
									class="far fa-circle"></i>
							</button>
						</a>

						<!-- H2 Exit button: -->
						<button class="btn opos-h2-button"
							style="float: left !important" id="h2_btn">
							<img src="{{asset('images/h2_logo_exit.png')}}"
								onclick="window.close()"
								style="margin-left:0;margin-right:0;
								margin-top:0;width:40px;
								height:auto;object-fit:contain"/>
						</button>

						<button class="btn btn-success btn-sq-lg poa-button-drawer"
							style="margin-left:5px !important;
							width:70px !important; float:left !important;
							font-size:15px"
							onclick="open_cashdrawer()">Drawer
						</button>

						<button class="btn btn-success poa-button-cash
							poa-button-cash-disabled"
							id="button-cash-payment0" onclick="select_cash()"
							style="margin-left:5px !important;float:left !important">Cash
						</button>
					</div>
					<div style="float:left; margin-bottom: 5px;">
						<input class="presetInput" type="number"
							style="background-color:#f0f0f0;
							float:left;
							border-width:0;font-size:20px;
							border-radius:10px;height: 70px !important;
							width: 145px;outline: none;text-align:center;"
							placeholder='0.00' id="custom_kg_input_0"
							oninput="presetInput(this.value)"/>

						<button class="btn mb-1 custom-preset-disable btnrecpt"
							id="custom_kg_btn"
							onclick="select_custom_preset()"
							style="margin-left: 5px !important; font-size:16px;
							padding-left:0 !important;padding-right:0 !important;
							margin-left:5px !important;cursor:pointer !important;">
							Preset<br>
							{{$currency ?? ''}}
						</button>

						<button class="btn btn-success poa-button-credit-card
							poa-button-credit-card-disabled"
							id="button-card-payment0"
							onclick="select_credit_card()"
							style="width: 70px !important;margin-left:0px !important;">Credit Card
						</button>

						<button class="btn btn-success opos-button-wallet
							opos-button-wallet
							opos-button-wallet-disabled"
							id="button-wallet0"
							onclick="select_wallet()"
							style="width: 70px !important;
							border-radius:10px;height:70px;
							margin-left: 0px !important;">Wallet
						</button>
					</div>
				</div>
				</div>
			</div>
		@endfor
	</div>

	<hr class="hr" style="position:absolute;z-index:-9999;">
</div>

<div class="row" style="margin-bottom:12px">
	<div class="col-md-4"></div>
</div>

@include("common.footer")

</div>
</div>

<div class="row">
	<div class="col-md-4"></div>
	<div class="col-md-8"
		style="position:relative;left:0;padding:0px">
		<div class="col-md-12 pt-0 pb-0 m-0 pr-0 mainrightsidediv"
			style="top:30px;left: -125px;" >
		<?php
		$s=1;
		$sno = 1;
		?>

		@for($i=1 ; $i<=env('H2_MAX_ROWS'); $i++)
			<div class="row col-md-12" style="margin:0px; padding:0px;">

			@for($j=1 ; $j<=env('H2_MAX_COLUMNS'); $j++)
				<div class="col_panel">
					<div class="row col-md-12 col_panel_inner" style="
					margin-left: 0px;">
						<div hidden class="overlay1" id = "overlay-{{$s}}">
							<span style="color: white; z-index: 1200; font-weight: 1000; font-size: 45px;"
								class="text-center">Reserved</span>
						</div>
						<div class="d-none" id="selected-pump-id"></div>

						<div class="col-md-4 col_panel_inner">

							<button class="btn h2-button-pump-idle mb-1" style="float: right !important;"
								id="pump-button-{{$s}}" onclick="select_pump({{$s}})" >
								<img src="{{asset('images/h2_logo.png')}}"
									style="width:32px;height:32px;object-fit:contain;margin-left:0"
								/>

								<div class="text-center pl-0 pr-0"
									style="font-size: 18px;">
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
							<span>Kg</span>
							<span>Price</span>

							<span id="payment-status-{{$s}}">Not Paid</span>

							{{--<!--
							<span style="display:none; color:rgb(11, 167, 11);
								font-weight:bold"
								id="authorize-status-{{$s}}">AUTHORIZED
							</span>-->
							--}}
						</div>

						<div class="col-md-4 line_before_number"
							style="color: white;line-height:1.5">
							<span style="line-height:1.2" id="total-fuel-pump-{{$s}}">50.00</span>
							<span style="line-height:1.8" id="total-final-filled-{{$s}}">0.00</span>
							<span id="total-final-kg-{{$s}}">0.00</span>
							<span id="fuel-product-price-{{$s}}">0.00</span>
						</div>

						<div class="col-md-12 p-0 m-0">
							<!-- Display selected product -->
							<div style="height:45px;width: 100%;margin-left: 18px;"
							class="align-items-right">

							<!-- Display product buttons -->
							<div class="row text-white"
								id="product-select-pump-{{$s}}"
								style="margin-left:-15px; margin-top:-7px; ">

								@if (!empty($productData))

								@foreach ($productData as $product)
								@if (!empty($nozzleFuelData->
									where("product_id",$product->id)->
									where("pump_no",$s)->first()))
									<div id="product-first-{{$sno}}" style='display:none;'>
										{{$product->id}}
									</div>
									<input type="hidden" id="selected-pump-{{$s}}" value="{{$sno}}">

									<span class='p-product product-row-{{$sno}} product-item-{{$product->id}}{{$s}}'>
									<img class="iHover fuelproductimages-{{$s}} product-disable-offline noclick fuel-product-image"
										src='/images/product/{{$product->systemid}}/thumb/{{$product->thumbnail_1}}'
										id="fuel-grad-thumb-{{$s}}-option-{{$product->id}}"
										onclick="selectProduct('{{$s}}', '{{$product->id}}', '{{$product->name}}',
										'/images/product/{{$product->systemid}}/thumb/{{$product->thumbnail_1}}')"
										prod_name = "{{$product->name}}"
										prod_img = "/images/product/{{$product->systemid}}/thumb/{{$product->thumbnail_1}}"
										prod_id = "{{$product->id}}"
										style='width:45px;height:45px;display:inline-block;
										object-fit: contain;
										background-color:white; border-radius:5px;
										margin-right:0;' />
									</span>
									<?php
									$sno++;
									?>
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
</div>
@endauth

<div class="modal fade @guest show @endguest " id="userEditModal" tabindex="-1"
	role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true"
	style="padding-right:0 !important; @auth display:none @endauth ">

	<div class="modal-dialog modal-dialog-centered" role="document"
		style=" width: 100% !important; height: 100% !important; margin: 0;
		padding: 0;max-width:100%;max-hight:100%">

		<div class="modal-content modal-inside bg-black"
			style="height: auto; min-height: 100% !important;border-radius:0;">
			<div class="modal-body text-center"
				style="vertical-align: middle !important ;margin-top: 3%;">
				<img style="width:180px;height:180px;object-fit:contain"
					src="{{ asset('images/small_logo.png') }}">
				<br>
				<p class="mb-0" style="margin-bottom:0;margin-top:20px;
					font-size:80px;font-weight:550;line-height:1.0">
				OPOSsum
				</p>
				<div class="row align-items-center">



				@if (!empty($isLocationActive) &&
					(!empty($isTerminalActive) || $isServerEnd) )

				<!-- This is only for OPOSsum Terminal Login -->
				<div style="display:flex;border:1px none red"
					class="col-md-3 align-items-center pl-0 pr-0">
					<div id="login-message"
						style="font-size:20px;color:yellow;line-height:1.3">
					</div>
					<div style="font-size:20px;color:yellow;line-height:1.3"
						class="text-center login_error">
					</div>
				</div>

				<div style="border:1px none yellow;"
					class="col-md-6 pl-0 pr-0 text-center">
					<img style="position:relative;top:-10px;
						cursor:pointer;
						width:100%;height:390px;object-fit:contain"
						onclick="login_form_toggle()"
						src="{{ asset('images/anim_torus.gif') }}"/>
				</div>
				<div style="border:1px none green"
					class="col-md-3 pl-0 pr-0">
					<div id="login_form"
						style="padding:20px auto;display:none;">
						<form autocomplete="off">
                            <input type="hidden" name="hosting" value="opossum" id="hosting"/>
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
							custom_login_btn" onclick="login_me()">
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
					<input autofocus class="form-control keydigit" type="text" id="key_1" maxlength="1">
					<input disabled class="form-control keydigit" type="text" id="key_2" maxlength="1">
					<input disabled class="form-control keydigit" type="text" id="key_3" maxlength="1">
					<input disabled class="form-control keydigit" type="text" id="key_4" maxlength="1">&nbsp;&nbsp;

					<input disabled class="form-control keydigit" type="text" id="key_5" maxlength="1">
					<input disabled class="form-control keydigit" type="text" id="key_6" maxlength="1">
					<input disabled class="form-control keydigit" type="text" id="key_7" maxlength="1">
					<input disabled class="form-control keydigit" type="text" id="key_8" maxlength="1">&nbsp;&nbsp;

					<input disabled class="form-control keydigit" type="text" id="key_9" maxlength="1">
					<input disabled class="form-control keydigit" type="text" id="key_10" maxlength="1">
					<input disabled class="form-control keydigit" type="text" id="key_11" maxlength="1">
					<input disabled class="form-control keydigit" type="text" id="key_12" maxlength="1">&nbsp;&nbsp;

					<input disabled class="form-control keydigit" type="text" id="key_13" maxlength="1">
					<input disabled class="form-control keydigit" type="text" id="key_14" maxlength="1">
					<input disabled class="form-control keydigit" type="text" id="key_15" maxlength="1">
					<input disabled class="form-control keydigit" type="text" id="key_16" maxlength="1">
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
						style="font-size:20px;color:yellow;width: 100%;text-align: center;">
					</div>
					<div style="font-size:20px;color:yellow;width: 100%;text-align: center;"
						class="pl-5 login_error">
					</div>
				</div>
				@endif
				</div>

			</div>
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

<div id="productsModal" class="modal fade" tabindex="-1" role="dialog"
	 aria-hidden="true">
<div class="modal-dialog modal modal-dialog-centered" style="margin: auto;">
	<div style="border-radius:10px"
		 class="modal-content bg-purplelobster">
	<div class="modal-header">
		<h3 style="margin-bottom:0">Calculation: Kg to MYR</h3>
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
							onclick="calculate_fuel_price({{$product->price}})">
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
<!-- Spay modal start -->
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

@endsection

@section('script')

@include('h2.h2_product_logic')
@if (!empty($isLocationActive) && ( !empty($isTerminalActive) ))

@else
@include('landing.license')
@endif

@include('h2.h2_wallet')

@endsection
