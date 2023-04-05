@extends('landing.web')
@section('subheader')
@endsection

@section('content')
<script type="text/javascript" src="{{asset('js/qz-tray.js')}}"></script>
<script type="text/javascript" src="{{asset('js/opossum_qz.js')}}"></script>
<script type="text/javascript" src="{{asset('js/JsBarcode.all.min.js')}}"></script>
<script type="text/javascript" src="{{asset('js/console_logging.js')}}"></script>
<style>

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
</style>

@auth
@include("common.header")


<div class="fixed-bottom">
<div class="container-fluid" id="container-blur" style="margin-top:3%">

<div class="row pt-2 pb-0 m-0 pl-0 pr-0" style="">
	<div class="col-md-10 pt-2 pb-0 m-0 pl-0 pr-0"
		style="display:flex;align-items:flex-end">

		<!-- pump-main-block-0 is the default. This will be displayed
        before user clicks on any pump -->

		<div class="row m-0 pt-2 pb-0 pl-0 pr-0"
			id="pump-main-block-0" style="display:flex;align-items:flex-end">

			<!-- Start Section -->
			<div class="col-md-4 pt-0 pb-0 m-0 pr-0"
                style="padding-left:15px;width: 503px !important;">

				<div class="row" style="margin-bottom:5px !important">
					<div style="padding-right:20px"
						class="col-md-12 pl-0 mt-1">
						<div id="payment-div"
							class="justify-content-center align-items-center"
							style="display:flex; background-color:#f0f0f0;
							width:368px !important;
							border-radius:10px;height: 50px !important">

							<span id="payment-value"
								class="pr-2"
								style="color:#a0a0a0;font-weight:500;
								font-size: 30px;">
								Cash Received
							</span>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12 pr-0 pl-0" style="position: relative">
						
						<button class="btn btn-success btn-sq-lg screend-button
							bg-virtualcabinet"
							onclick="window.open('{{route('local_cabinet')}}','_blank')"
							style="margin-left:0 !important;outline:none;
								width:145px !important; font-size: 14px">
						<span style="">Today<br>Cabinet</span>
						</button>
					
						<!--
						<button id="cancel_btn_numpad"
							 class="btn btn-sq-lg
							 poa-button-number-payment-cancel-disabled">Cancel
						</button>
						-->
						
						<button class="btn btn-sq-lg
							poa-button-number-payment-zero-disabled"
							onclick="set_cash('zero')">Zero
						</button>
						<button class="btn btn-sq-lg
							poa-button-number-payment-zero-disabled"
							onclick="" style=" width: 143px !important;">Enter
						</button>
					</div>
				</div>
            </div>


			<div class="col-md-6 pt-2 pb-0 m-0 pr-0 pl-5"
                style="position:relative;left:70px;bottom:0">
                <div class="row mt-auto">

				<div class="col-md-8 mx-auto pr-1 pl-1"
					id="dCalc" style="font-size:20px;
					/*position:absolute*/; top: -30%;
					line-height:1.2; z-index: 9999;
					margin-bottom:10px; padding-left:-20px;
					text-align: center;display:none;">

					<div id="refundCustomer"
						style="border:orange;background:orange;
						border-style:solid;padding:5px;
						border-radius: 15px;">

						<div class="" style="width:100%;padding-left:0px;
							padding-right:0px;padding-bottom:5px">
							<div class="col col-md-12 text-center"
								style="font-size: large; color:white">
							   The refund amount is as follow <br>
								<b id="change"></b>
								<b> {{empty($company->currency->code) ? 'MYR': $company->currency->code }}</b>
								<!--b id="dose"></b-->
							</div>
						</div>

						<div class="" style="width: 100%; padding-top:5px;
							padding-left: 0px; padding-right: 0px;">
							<div class="col col-md-12 text-center">

							<!--- button type="button" class="btn btn-success"
								onclick="display_refund()"
								removeRefund()
								style="width:100px">Confirm
							</button --->

							<button type="button" class="btn"
								onclick="removeRefund()"
								style="width:100px;
								background: #000;color: #fff;vertical-align: bottom;">Close
							</button>
							</div>
						</div>
					</div>
				</div>
                </div>

				<div class="row mt-auto" style="">
					<div style="color:white;font-size:20px;
						position:relative;left:-20px; line-height:1.2;
						margin-top:7px;
						padding-left:-20px; text-align: center;">
						<b>Total<br>Amount</b><br>
						{{empty($company->currency->code) ? 'MYR': $company->currency->code }}
					</div>
					<div style="margin-bottom:5px"
                        class="col-md-8 ml-0 pr-0 pl-0">

						<div id="amount-myr"
							class="pl-1 pr-1" style="
							background-color:#f0f0f0;width:385px;
							border-radius:10px;height: 85px !important">
						</div>
					</div>
				</div>

				<div class="row" style="">
					<div style="position:relative;left:-26px;top:5px;width:70px"
						class="text-right  pt-0 mr-1">
						<img src="{{asset('images/h2_logo.png')}}"
						style="margin-top:12px;width:50px;height:auto;object-fit:contain"/>
					</div>
					<div style="margin-bottom:5px" class="col-md-8 pr-0 pl-0">
						<div id="volume-liter"
							class="pl-1 pr-1"
							style="background-color:#f0f0f0;width:385px;
							border-radius:10px;height:85px !important">
						</div>
					</div>
					<div class="text-center col-md-1 mt-auto mb-4 pl-0"
						style="position:relative;top:15px;left: 35px;color:white;font-size:20px;">
						<b> kg  </b>
					</div>
				</div>

				<div class="row">
					<div class="ml-0 p-0"
						style="width:60px; height:60px;text-align:center;
							position:relative;left:-10px;top:3px;
							font-size:40px;color:white;"
							id="pump-number">
						<b id="pump-number-main"></b>
					</div>
					<div class="mr-2 ml-2 mt-2"
						style="position:relative;left:15px;
							line-height:1.2; color:white;
							font-size:20px;text-align: center;">
						<b>Price</b><br>
						{{empty($company->currency->code) ? 'MYR': $company->currency->code }}
					</div>
					<div class="col-md-6 pr-0 pl-4">
						<div id="price-meter"
							class="pl-8 pr-1 pt-1 pb-1"
							style="
							padding-top:10px;padding-bottom:10px;
							background-color:#f0f0f0;
							border-radius:10px;height: 60px !important">
						</div>
					</div>
					<div class="text-center col-md-2 pl-0"
						style="color:white;padding-top:15px;font-size:20px;">
						<b>/kg</b>
					</div>
				</div>

				<div style="min-height:100px;max-height:100px;display:flex">
				<div class="row"></div></div>
			</div>

			<div class="col-md-2 pt-2 pb-0 m-0 mb-3 pl-0 pr-0"
				style="position:relative;left: 50px;top:-35px;">

				<div id="disp_ltr-0" style="display:none;">
					<h1 class="text-center"
						style="margin-bottom:0 !important;color:white;
						font-size:20px">
						Kg
					</h1>
					<div class="text-center mb-2"
						style="color:white;font-size:2em; display: block;">
						<b id="total_volume-main-0">0.00</b>
					</div>
				</div>

				<div class="text-center"
					style="margin-bottom:0 !important;color:white;
					font-size:20px" >
					<b id="payment-status"
						style="margin: 30px auto;display: block;">Not Paid
					</b>
				</div>

				<h1 class="text-center"
					style="margin-bottom:0 !important;color:white;
					font-size:20px">
					{{empty($company->currency->code) ? 'MYR': $company->currency->code }}
				</h1>
				<div class="text-center mb-2"
					style="color:white;font-size:2em; display: block;">
					<b id="total_amount-main">0.00</b>
				</div>
				<h1 class="mb-0 text-center"
					style="color:white;font-size:20px">Status
				</h1>
				<div class="text-center"
					style="color:white;font-size:20px">
					<b id="pump-status-main">Online</b>
				</div>

				<div class="text-center"
					style="position:relative;top:10px;color:white;font-size:20px">
					<img src="{{asset('images/delivering_spinball.gif')}}"
						style="width:80px;height:80px;object-fit:contain"/>
				</div>
			</div>
		</div>
		<!-- end section -->


		@for($i = env('MAX_PUMPS'); $i>=1; $i--)
		<div class="row m-0 pt-2 pb-0 pl-0 pr-0"
			id="pump-main-block-{{$i}}"
			style="display: none;align-items:flex-end">
            <div class="col-md-4 pt-0 mt-0 pb-0 m-0 pr-0"
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
            <div  class="col-md-8 pt-2 pb-0 m-0 pr-0"><br></div>

			<!-- Start Section -->
			<div class="col-md-4 pt-0 pb-0 m-0 pr-0"
                style="padding-left:15px">
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
				<div style="padding-right:20px"
					class="col-md-12 pl-0 mt-1"
					id="payment-div-cash{{$i}}">
					<input class="justify-content-center align-items-center"
						style="display:flex; background-color:#f0f0f0;
						width:368px !important;justify-content: flex-end;
						border-radius:10px;height: 50px !important;font-weight:500;
						font-size:30px;text-align:right"
						id="input-cash{{$i}}" placeholder="Cash Received"
						disabled>

						<input id="buffer-input-cash{{$i}}" type="hidden" />
						<!--span id="payment-value{{$i}}"
							class="text-right pr-2 float-right"
							style="color:#a0a0a0;font-weight:500;
							font-size:30px">
							Cash Received
						</span>
					</div-->
				</div>
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

		
				<!--
				<div class="justify-content-center mt-1 align-items-center
					pt-2 pr-2 col-md-8 payment-div-refund{{$i}}"
					style="display:flex; background-color:black;
					border-radius:10px;height:50px !important;
					display:none">
					<span id="payment-div-refund-amount-bl-message{{$i}}"
						class="text-center" style="font-weight:bold;
						color:white;font-size:25px">
					</span>
				</div>

				<div class="justify-content-center mt-1 align-items-center
					ml-1 payment-div-refund{{$i}}"
					style="display:flex;padding-top:3px;
					width:104px !important; background-color:black;
					border-radius:10px; height: 50px !important;
					display:none;">

					<span id="payment-div-refund-amount{{$i}}"
						class="text-center float-right"
						style="color:white;font-weight:bold;
						font-size:30px;">
					</span>
				</div>
				-->
			</div>

			<div class="row" style="">
				<div class="col-md-12 pr-0 pl-0">
				
					<button	onclick="pumpCancel({{$i}})"
					class="btn btn-sq-lg
						numpad-cancel-payment{{$i}}
						poa-button-number-payment-cancel-disabled">Cancel
					</button>
					<button class="btn btn-success btn-sq-lg screend-button
						bg-virtualcabinet"
						onclick="window.open('{{route('local_cabinet')}}','_blank')"
						style="margin-left:0 !important;outline:none;
							font-size: 14px">
					<span style="">Today<br>Cabinet</span>
					</button>
					<button class="btn btn-sq-lg
						numpad-zero-payment{{$i}}
						poa-button-number-payment-zero-disabled"
						onclick="set_cash('zero')" > Zero
					</button>
					<button class="btn btn-sq-lg
						numpad-enter-payment{{$i}}
						poa-button-number-payment-enter-disabled"
						onclick="process_enter()"  > Enter
					</button>
				</div>
			</div>
		</div>

		<div class="col-md-6 pt-2 pb-0 m-0 pr-0 pl-5"
			style="position:relative;left:70px;">

			<div id='pump-auth-warn-{{$i}}'
				class="text-center"
				style="padding-left:0;display:none;">
				<h5 style='margin-bottom:15px;color:yellow;'>
					Pump already authorized by the other terminal. Due to pump being locked, payment status and MYR do not reflect the actual values
				</h5>
			</div>
			<div class="row mt-auto" style="">
				<div style="color:white;font-size:20px;
					position:relative;left:-20px; line-height:1.2;
					margin-top:7px;border-radius:10px;
					padding-left:-20px; text-align: center;">
					<b>Total<br>Amount</b><br>
					{{empty($company->currency->code) ? 'MYR': $company->currency->code }}
				</div>
				<div style="margin-bottom:5px"
					class="col-md-8 ml-0 pr-0 pl-0">
					<div id="amount-myr-{{$i}}"
						class="pl-1 pr-1" style="
						padding-top:10px; padding-bottom:10px;
						background-color:#f0f0f0;width:385px;
						border-radius:10px;height: 85px !important">
					</div>
				</div>
			</div>

			<div class="row" style="">
				<div style="position:relative;left:-10px;top:20px"
					class="text-right pr-4 pt-0 mr-1">
					<img src="{{asset('images/dispenser_icon.png')}}"
					style="transform:scaleX(-1);
					width:50px;height:auto;object-fit:contain"/>
				</div>
				<div style="margin-bottom:5px" class="col-md-8 pr-0 pl-0">
					<div id="volume-liter-{{$i}}"
						class="pl-1 pr-1"
						style="background-color:#f0f0f0;width:385px;
						padding-top:10px;padding-bottom:10px;
						border-radius:10px;height:85px !important">
					</div>
				</div>
				<div class="text-center col-md-2 mt-auto mb-4 pl-0"
					style="position:relative;top:15px;left:25px;
					color:white; font-size:20px;">
					<b>Litre</b>
				</div>
			</div>

			<div class="row">
				<div class="ml-0 p-0"
					style="width:60px; height:60px;text-align:center;
						position:relative;left:-10px;top:3px;
						font-size:40px;color:white;"
						id="pump-number">
					<b id="pump-number-main-{{$i}}">{{$i}}</b>
				</div>
				<div class="mr-2 ml-2 mt-2"
					style="position:relative;left:15px;
						line-height:1.2; color:white;
						font-size:20px;text-align: center;">
					<b>Price</b><br>{{empty($company->currency->code) ? 'MYR': $company->currency->code }}
				</div>
				<div class="col-md-6 pr-0 pl-4">
					<div id="price-meter-{{$i}}"
						class="pl-8 pr-1"
						style="
						padding-top:10px; padding-bottom:10px;
						background-color:#f0f0f0;
						border-radius:10px;height: 60px !important">
					</div>
				</div>
				<div class="text-center col-md-2 pl-0"
					style="color:white;padding-top:15px;font-size:20px;">
					<b>/Litre</b>
				</div>
			</div>

			<!-- Display selected product -->
			<div style="min-height:100px;max-height:100px;display:flex"
				class="align-items-center">
				<div class="row text-white"
					id="product-display-pump-{{$i}}"
					style="position:relative;top:0;left:0;
					align-items:center;">
					<img src='' id="fuel-grad-thumb-{{$i}}"
						style='width:70px;height:70px;display:inline-block;
						border-radius:10px;
						margin-right:12px;object-fit:contain;display:none'>
					<p class='m-0 p-0' id="fuel-grad-name-{{$i}}"
						style="text-align: center;font-size: 25px;
						font-weight: 600;"></p>
				</div>

				<!-- Display product buttons -->
				<div class="row text-white" id="product-select-pump-{{$i}}"
					style="margin-left:15px;margin-top:0;
					align-items: center; display:none;">
					@if (!empty($productData))
					@foreach ($productData as $product)
						@if (!empty($nozzleFuelData->
							where("product_id",$product->id)->
							where("pump_no",$i)->first()))
							<img src='/images/product/{{$product->systemid}}/thumb/{{$product->thumbnail_1}}'
								id="fuel-grad-thumb-{{$i}}-option-{{$product->systemid}}"
								onclick="selectProduct('{{$i}}', '{{$product->id}}', '{{$product->name}}',
								'/images/product/{{$product->systemid}}/thumb/{{$product->thumbnail_1}}')"
								style='width:70px;height:70px;display:inline-block;cursor:pointer;object-fit: contain;
								border-radius:10px;margin-right: 8px;' />
						@endif
					@endforeach
					@endif
				</div>
			</div>
		</div>

		<div class="col-md-2 pt-2 pb-0 m-0 mb-3 pl-0 pr-0"
			style="position:relative;left:50px;top:-35px">

			<!--div id="disp_ltr-{{$i}}"
				style="display:none">
				<h1 class="text-center"
					style="margin-bottom:0 !important;color:white;
					font-size:20px">
					Litre
				</h1>
				<div class="text-center mb-2"
					style="color:white;font-size:2em; display: block;">
					<b id="total_volume-main-{{$i}}">0.00</b>
				</div>
			</div-->

			<div class="text-center"
				style="margin-bottom:0 !important;color:white;
				font-size:20px">
				<b id="payment-status-{{$i}}"
					style="margin: 30px auto;display: block;">
					Not Paid
				</b>
			</div>

			<h1 class="text-center"
				id="preset-type-main-{{$i}}"
				style="margin-bottom:0 !important;color:white;
				font-size:20px">
				{{empty($company->currency->code) ? 'MYR': $company->currency->code }}
			</h1>
			<div class="text-center mb-2"
				style="color:white;font-size:2em; display: block;">
				<b id="total_amount-main-{{$i}}">0.00</b>
			</div>
			<h1 class="mb-0 text-center"
				style="color:white;font-size:20px">Status
			</h1>
			<div class="text-center"
				style="color:white;font-size:20px">
				<b id="pump-status-main-{{$i}}">Online</b>
			</div>

			<div class="text-center"
				style="position:relative;top:10px;color:white;font-size:20px">
				<img id="pump-delivering-spinball-{{$i}}"
					src="{{asset('images/delivering_spinball.gif')}}"
					style="width:80px;height:80px;object-fit:contain"/>
			</div>
		</div>
		</div>
		@endfor
	</div>

	
	</div>

	<hr class="" style="margin-top:0px !important;
		margin-bottom:5px !important; border:0.5px solid #a0a0a0"/>

	<div class="row" style="margin-bottom:12px">
		<div class="col-md-4">
			<div style="float:left;">
				<a href="{{route('screen.d')}}" target="_blank"
					rel="noopener noreferrer">
					<button class="btn poa-bluecrab-button mb-1"
						style="float: left !important;"
						id="bluecrab_btn" onclick="">
						<i style="top:2px;margin-left:2px;margin-right:0;
							padding-left:0;padding-right:0;font-size:48px"
							class="far fa-circle"></i>
					</button>
				</a>

				<!-- This to replace cstore and the plain button -->
				<!--
				<button style="float:left !important;
					pointer-events:none;
					margin-right:5px !important"
					class="btn btn-sql-lg phantom-button1">
				</button>

				<div class="btn"
					style="height:auto;margin-top: -5px !important;padding: 4px;width: 72px;object-fit:contain;" id="cstore_btn">
					<img src="{{asset('images/h2_logo.png')}}"
						style="width: 69px;top: 0px;height:auto;object-fit:contain;"/>
					
				</div>
				-->

				<!--
				<button class="btn opos-plain-button"
					style="float: left !important;
					pointer-events:none;cursor:normal;" id="" >
					<img src=""
					style="width:45px;height:auto;object-fit:contain;"/>
				</button>
				-->

				<button class="btn btn-success btn-sq-lg poa-button-drawer"
					style="margin-left:5px !important;
					width:145px !important; float:left !important;
					font-size:15px"
					onclick="open_cashdrawer()">Drawer
				</button>

                <button class="btn btn-success poa-button-cash
					poa-button-cash-disabled"
					id="button-cash-payment0" onclick="select_cash()"
					style="margin-left:5px !important;float:left !important">Cash
                </button>
			</div>
			<div style="float:left;">
				<!--
				<button class="btn btn-success btn-sq-lg poa-button-drawer"
					style="float: left !important; font-size:15px"
					onclick="open_cashdrawer()">Drawer
				</button>

				<button class="btn btn-sql-lg phantom-button0"></button>
				<button class="btn btn-sql-lg phantom-button0"></button>
                <button class="btn btn-sql-lg phantom-button1"></button>
				-->

				<!--
				<span style="color:#fff;width: 55px !important;
					display: inline-block;">Litre
				</span>
				-->

				<input class="" type="number"
					style="background-color:#f0f0f0;
					float:left;
					border-width:0;font-size:20px;
					border-radius:10px;height: 70px !important;
					width: 145px;outline: none;text-align:center;"
					placeholder='0.00' id="custom_litre_input_0" disabled />

				<button class="btn mb-1 custom-preset-disable"
					id="custom_litre_btn"
					onclick="select_custom_litre()"
					style="margin-left: 5px !important; font-size:16px;
					padding-left:0 !important;padding-right:0 !important;
					margin-left:5px !important;cursor:pointer !important;
					/*background:linear-gradient(rgb(94, 213, 60),rgb(4, 162, 249))*/">
					Preset<br>MYR
				</button>

                <button class="btn btn-success poa-button-credit-card
					poa-button-credit-card-disabled"
					id="button-card-payment0"
					onclick="select_credit_card()"
					style="float:right; width: 146px !important;">Credit Card
                </button>
			</div>

			<div style="float:left !important;">
				<button style="float:left !important;
                    pointer-events:none;
					width:220px !important;"
                    class="btn btn-sql-lg phantom-button1">
                </button>

				<!--
                <button class="btn btn-success poa-authorize
					poa-authorize-disabled"
					id="button-authorize0
					onclick=""
					style="float:left;width: 219px !important;
					margin-left:0 !important;
					border-radius:10px;height:70px;">Authorize
                </button>
				-->

                <button class="btn btn-success opos-button-wallet
					opos-button-wallet"
					id="button-wallet0"
					onclick=""
					style="float:left !important;width: 145px !important;
					border-radius:10px;height:70px;
					margin-left: 2px !important;">Wallet
                </button>
			</div>

		</div>
		<div class="col-md-1"></div>
		<div class="col-md-7">

		<!-- For Hydrogen H2 and Electrical Vehicle EV charger -->
		<!--
		<div class="float-right mr-0 ">
			<button class="btn h2-button-pump-idle mb-1" style="float: right !important;"
				id="pump-button-{{$i}}" onclick="pump_selected({{$i}})" >
				<img src="{{asset('images/dispenser_icon.png')}}"
					style="transform:scaleX(-1);width:32px;height:32px;object-fit:contain;margin-left:0"
				/>
				<br>
				<div class="text-center pl-0 pr-0"
					style="font-size: 18px;">
					{{$i}}
				</div>
				<p style="font-size: 0.7em;"
					id="pump-status-{{$i}}">Offline
				</p>
			</button>
		</div>
		-->

		<div class="float-right mr-0 ">
			@for($i=1 ; $i<=env('MAX_PUMPS'); $i++)
			<button class="btn h2-button-pump-idle mb-1" style="float: right !important;"
				id="pump-button-{{$i}}" onclick="pump_selected({{$i}})" >
				<img src="{{asset('images/h2_logo.png')}}"
					style="width:32px;height:32px;object-fit:contain;margin-left:0"
				/>
				
				<div class="text-center pl-0 pr-0"
					style="font-size: 18px;">
					{{$i}}
				</div>
				<p style="font-size: 0.7em;"
					id="pump-status-{{$i}}">Offline
				</p>
			</button>
			@endfor
		</div>
		</div>
	</div>

@include("common.footer")

	<!--
	<nav class="navbar navbar-light bg-light p-0"
		style="background-image:linear-gradient(rgb(38, 8, 94),rgb(86, 49, 210));">
	<nav class="navbar navbar-light bg-light p-0">
		<span class="navbar-text m-0"></span>
	</nav>
	-->
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

<!--
<div class="modal fade bd-example-modal-lg" id="driverfuelledger"
	tabindex="-1" role="dialog" aria-labelledby="driverfuelledger"
	aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered "
		style="max-width: 75% !important;">
		<div class="modal-content bg-purplelobster" >
			<div class="modal-header">
				<h3 class="mb-0">Local Fuel Ledger</h3>
			</div>
			<div class="modal-body" id="driverfuelledger-table"></div>
		</div>
    </div>
</div>
-->

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
{{-- <?php 
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
					?> --}} 
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

@if (!empty($isLocationActive) && ( !empty($isTerminalActive) ))

@else
@include('landing.license')
@endif

@endsection
