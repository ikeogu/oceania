<?php $__env->startSection('styles'); ?>

<script type="text/javascript" src="<?php echo e(asset('js/console_logging.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('js/qz-tray.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('js/opossum_qz.js')); ?>"></script>
<style>
.col-md-left{
	width: 30.0%;
}
.col-md-mid{
	width: 40.0%;
}
.col-md-right{
	width: 28.0%;
}

html, body {
	height: 100%;
}

body {
	background: #000;
}

.single {
	width: 17px;
	height: 32px;
}

.col-md-12.top-bg {
	background: #0062cc;
}

.cross {
	width: 45px !important;
	height: 45px !important;
	margin-top: 96px;
}

.h-t {
	height: 250px;
}

.col-md-12.m-t {
	margin-top: 186px;
}

.header-text {
	margin-bottom: 8px;
	margin-top: 8px;
}
.h-100 {
    height: 100% !important;
    padding-bottom: 16px;
}
/*button.w-100 {*/
/*    width: 100% !important;*/
/*}*/

.poa-button-number-payment-enter {
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
	width: 70px !important;
	height: 70px !important;
	background-image: linear-gradient(#2d1382, #397df3);;
}

.poa-fuel-page-button {
	margin-bottom: 5px !important;
	margin-left: 5px !important;
	margin-right: 0 !important;
	margin-top: 0 !important;
	/* font-size: 20px; */
	border-radius: 10px;
	color: #ffffff;
	border: 0;
	border-width: 0;
	padding-left: 0;
	padding-right: 0;
	width: 70px !important;
	height: 70px !important;
	background-image: linear-gradient(#12f30d, #b9f691);
}

.poa-button-number-payment-enter-disabled {
	pointer-events: none !important;
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
	width: 70px !important;
	height: 70px !important;
	background-color: rgb(146, 146, 146);
}

.btn-close {
	color: white;

	margin-bottom: 5px !important;
	margin-left: 0 !important;
	margin-right: 0 !important;
	margin-top: 0 !important;
	/* font-size: 20px; */
	border-radius: 10px;
	border-width: 0;
	color: #ffffff;
	padding-left: 0;
	padding-right: 0;
	width: 69px !important;
	height: 70px !important;
	background-image: linear-gradient(rgb(247 17 38), rgb(247 17 38));
}

.lg-custom-button {
	width: 145px !important;
}
#input-cash:focus, #input-cash:active {
	border:none;
	outline:none;
}
#input-cash::placeholder {
	text-align:center;
}
#all_products_main_page {

	position: relative;
    right: 0;
    padding-right: 28px !important;
    top: 0;
    display: flex;
    height: 97%;
    overflow-y: auto;
    align-items: flex-start;
    overflow-x: hidden;

}

/* width */
::-webkit-scrollbar {
  width: 10px;
}

/* Track */
::-webkit-scrollbar-track {
  background: #f1f1f1;
}

/* Handle */
::-webkit-scrollbar-thumb {
  background: #888;
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<script type="text/javascript" src="<?php echo e(asset('js/qz-tray.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('js/opossum_qz.js')); ?>"></script>

<body>
<div class="container-fluid h-100" >
<div class="row h-100">
<div class="col-md-12 hidden-md-down"
	style=" height: 100%;display: inline-block;min-height:100%;">

	<div class="row h-100">
	<div class="col-md-12" style="background-image:
		linear-gradient(rgb(38,8,94), rgb(86,49,210));">

		<div class="row">
		<div class="col-md-8 align-items-center d-flex">
			<img src="<?php echo e(asset('images/opossum_lgtst-11.png')); ?>" alt=''
				style="object-fit:contain;width:auto; height:20px;
				margin-top:-3px; cursor: pointer;"
				srcset="" class="mr-1" onclick="location.href='#';">
		</div>
		<div class="col-md-4">
			<div class="row">
				<div class="col-md-8">
					<p class="text-white header-text">Description</p>
					  <div class="d-none" id="get-product-name"></div>
 				</div>
				<div style="margin-left:-10px"
					 class="col-md-1 text-left">
					<p class="text-white float-right header-text">Qty</p>
					  <div class="d-none" id="total-qty"></div>
				</div>
				<div class="col-md-3 text-right pr-2">
					<p class="text-white header-text">
					<?php echo e((!empty($currency) ? $currency : 'MYR')); ?>&nbsp;&nbsp;&nbsp;
					</p>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Start Section -->
<div class="col-md-12 hidden-md-down"
	style="height: 100%;display: inline-block;min-height:100%;">

	<div class="row m-t h-100">
		<div class="col-md-left mt-0 p-0 m-0"
			style="
				width:27%;
				align-items:flex-end;display:flex;">

			<!-- Column: Payment Section -->
			<!-- WARNING: pl-3 or pl-4 depending on monitor -->
			<div style="width:100%"
				class="col pr-0 mr-0 float-right pl-3">

				<div class="col-md-12 pt-2_ mt-4_ pb-0 m-0 pr-0"
					style="padding-left:15px;
						display:flex;justify-items: flex-end;
					    align-items: flex-end;min-height: 55px;">
					<span id="payment-type-message" class=""
						style="color:white;font-weight:500;
						font-size:30px;font-weight: bold;display:inline-flex">
					</span>
					<span id="payment-type-paid-right"
						class="float-right"
						style="color:white;font-weight:500;
						font-size:30px;font-weight: bold;padding-right:5px;display:inline-flex">
					</span>

					<span id="payment-amount-card-amount"
						class="" style="color:white;font-weight:500;
						font-size:30px;font-weight: bold;display:inline-flex">
					</span>
					<span id="payment-type-amount"
						class=""
						style="color:white;font-weight:500;
						font-size:30px;font-weight: bold;padding-right:5px;display:inline-flex">
					</span>
				</div>

				<div class=""style="
					margin-bottom:5px !important;">
					<div style="justify-content:flex-end"
						class="col-md-12 pl-0 pr-0 mt-1 payment-div-cash">
						<div class="align-items-center justify-content-center "
							style="display:flex; background-color:#f0f0f0;
							width:370px !important;
							border-radius:10px;height: 50px !important">
							<!--div id="payment-value"
								class="pr-2 text-center"
								style="color:#a0a0a0;font-weight:500;
								font-size:30px">
								Cash Received
							</div-->

						<input class="justify-content-center align-items-center"
							style="display:flex; background-color:#f0f0f0;
							width:368px !important;
							border-radius:10px;height: 50px !important;
							font-weight:500; font-size:30px;text-align:right"
							id="input-cash" placeholder="Cash Received"
							onkeyup="calculate_change()"
							disabled>
						<input type="hidden" id="input-cash-buffer"/>

						</div>
					</div>
					<div class="col-md-12 pl-auto pr-0 mt-1">
						<div class="justify-content-center mt-1
							align-items-center pr-2 col-md-8 payment-div-card"
							style="display:flex;
							background-color:#f0f0f0;padding-top:10px;
							border-radius:10px;height: 50px !important;
							display:none;vertical-align: top; ">
							<span id="" class="text-center"
								style="color:#a0a0a0;font-weight:500;
								font-size:20px"><b>XXXX-XXXX-XXXX</b>
							</span>
						</div>
						<div class="justify-content-center align-items-center
							payment-div-card"
							style="display:flex; width:89px !important;
							background-color:#f0f0f0; border-radius:10px;
							height: 50px !important;display:none;
							vertical-align: bottom;padding-top:3px;">
							<span id="payment-value-card"
								class="text-center"
								style="color:black;font-weight:500;
								font-size:30px;padding-left: 12px;
								padding-right: 5px;">
							</span>
						</div>
					</div>
				</div>

				<div class="">
					<div class="col-md-12 pr-0 pl-0"
						id="keypad-div" style="">
					<div>
					<button class="btn btn-success btn-sq-lg screend-button
						bg-virtualcabinet"
						onclick="window.open('<?php echo e(route('local_cabinet.cstore_receipt_landing' , ['date'=>date('dMY',strtotime(now()))] )); ?>','_blank')"
						style="margin-left:0 !important;outline:none;
							font-size: 14px">
					<span style="">Today Cabinet</span>
					</button>
					<button style="margin-bottom:5px !important"
						class="btn btn-sq-lg poa-button-number-payment-zero"
						onclick="reset_window()">
						Reset
					</button>
					<button class="btn btn-sq-lg
						poa-button-number-payment-zero"
						onclick="set_cash('zero')">Zero
					</button>
					<button
						class="btn btn-success lg-custom-button
						numpad-enter-payment
						poa-button-number-payment-enter"
						onclick="process_finish()">Enter
					</button>

					<hr style="border:1px solid white;width:370px;margin-left:0;
						margin-top:2px !important; margin-bottom:6px !important"/>
					</div>

					<div>
					<button class="btn cstore-bluecrab-button mb-1"
                        style="float: left !important;"
                        id="bluecrab_btn"
						onclick="window.open('<?php echo e(route("screen.d")); ?>')">
                        <i style="top:2px;margin-left:0;margin-right:0;
                            padding-left:0;padding-right:0;font-size:48px"
                            class="far fa-circle"></i>
                    </button>
					<button class="btn opos-cstore-button"
						style="float: left !important; margin-right:15px;"
							onclick="window.close()">

						<img src="<?php echo e(asset('images/cstore_exit_button.png')); ?>"
							style="padding-left:0;;padding-right:0;;
							margin-right:5px;
							width:70px;height:auto;object-fit:contain"/>
					</button>
					<button class="btn btn-success btn-sq-lg
						cstore-button-drawer"
						style="font-size:15px;margin-left:5px!important;
						margin-bottom:0 !important"
						onclick="open_cashdrawer()">Drawer
					</button>
					<button style="margin-bottom:0 !important; margin-left:0 !important"
						class="btn btn-success lg-custom-button
						poa-cash-btn
						poa-button-cash w-100"
						onclick="select_cash()" id="">Cash
					</button>
					</div>

					<div class="row ml-0"
						style="width:100%;margin-bottom:2px">

						<input class="" type="number"
							style="background-color:#f0f0f0;
							border-width:0;font-size:16px;
							border-radius:10px;height: 70px !important;
							width: 145px;outline: none;text-align:center;"
							placeholder="Receipt Discount" id="receipt_disc"
							min="1" max="100"
							onKeyPress="if(this.value.length==3) return false;"
							disabled />

						<button class="btn btn-success cstore-button-receiptdisc"
							id="cstore-button-receiptdisc"
							onclick=""
							style="border-radius:10px; margin-left:5px !important;
							font-size:36px;width:70px;height:70px">%
						</button>

						<button style="margin-left:4px !important;
							margin-bottom:0 !important"
							class="btn btn-success lg-custom-button
							poa-button-credit-card w-100 poa-card-btn"
							onclick="select_credit_card()">Credit Card
						</button>
					</div>

					<div class="row ml-0"
						style="width:100%;">
						<input class="" type="text"
							style="background-color:#f0f0f0;
							border-width:0;font-size:20px;
							border-radius:10px;height: 70px !important;
							width: 220px;outline: none;text-align:center;"
							placeholder="Search" id="product_search" />

						<button class="btn btn-success cstore-button-wallet
							cstore-button-wallet-disabled"
							id="cstore-button-wallet"
							onclick="select_wallet()"
							style="border-radius:10px;
							margin-left:4px !important;
							width:145px;height:70px">Wallet
						</button>
					</div>
					</div>

				</div>
			</div>
		</div>

		<!-- Column: Screen A -->
		<div id="all_products_main_page" style="
			height:100%;
			border-left:1px solid #808080;
			border-right:1px solid #808080;
			padding-left:10px;
			position:relative;left:0"
			class="col-md-mid pt-2 m-0 pr-0 ">

			<div id="product_data_screen_a" class="row col-md-12"
				style=" margin-left: -10px;">
				<?php echo $product_data; ?>

			</div>

			<div class="" style="bottom:0;position: absolute;">
			</div>
		</div>

		<!-- Column: Item Display Section -->
		<div style="
			width:33%;
			position:relative;top:0;left:0"
			class="col-md-right pt-2 pb-0 m-0 pr-0">

			<!--
			<div class="m-0 p-0"
				style="position:absolute;left:2px;height:98%;
				border-left:2px solid #a0a0a0">
			</div>
			-->

			<div class=""
				style="width:98%;height:50%;margin-left:10px;overflow-y: auto;">
				<table class="w-100">
				<tbody id="product_table_staged">
				<?php if(!empty($product)): ?>
					<tr id="product_row1" onclick="showProduct(1)">
					<td style="width: 60%">
						<img
							class="width:25px;height:25px;object-fit:contain"
							src="/images/product/<?php echo e($product->systemid??""); ?>/thumb/<?php echo e($product->thumbnail_1??""); ?>"
							alt="" class="single">
						<span class="text-white">
							<?php echo e($product->name??""); ?>

						</span>
					</td>
					<td style="width: 10%;tex-talign:center;"
						class="text-white">
						<?php echo e(number_format($productData['product_quantity'],2)??""); ?>

					</td>
					<td style="width: 30%;text-align: right"
						class="text-white pr-4">
						<?php echo e(number_format($productData['product_amount'],2)??""); ?>

					</td>
					</tr>
				<?php else: ?>
					<tr>
					
					</tr>
				<?php endif; ?>


				</tbody>
				</table>
			</div>

			<div class="col-md-12"
				style="position:absolute;left:0; bottom: 0;height:330px;">
			<div class="col-md-12 mt-auto w-100 mr-0 pr-1 pl-0" style="height: 80px;">
				<hr class="" style="margin-top:5px !important;
					margin-bottom:5px !important;
					border:0.5px solid #a0a0a0">
				<div class="d-flex bd-highlight">
					<div class="mr-auto bd-highlight text-white">
						Item Amount
					</div>
					<div class="bd-highlight text-white">
						<span id="item-amount">0.00</span>&nbsp;
					</div>
				</div>
				<div class="d-flex bd-highlight">
					<div
						class="mr-auto bd-highlight text-white">
						<?php echo e(!empty($terminal->taxtype)?strtoupper($terminal->taxtype):"SST"); ?> <?php echo e($terminal->tax_percent??"6"); ?>

						%
					</div>
					<div class="bd-highlight text-white">
						<span id="sst-val">0.00</span>&nbsp;
					</div>
				</div>
				<div class="d-flex bd-highlight">
					<div class="mr-auto bd-highlight text-white">
						Rounding
					</div>
					<div class="bd-highlight text-white">
						<span id="rounding-val">0.00</span>&nbsp;
					</div>
				</div>
				<hr class="" style="margin-top:5px !important;
					margin-bottom:5px !important;
					border:0.5px solid #a0a0a0">
				<div class="d-flex justify-content-end">
					<div class="">
						<b class="text-white" style="font-size:20px ">
							<span style="margin-right:20px">Total</span>
							<span style="padding-right:5px"
								id="total-val">0.00
							</span>
						</b>
					</div>
				</div>
			</div>

			<div class="" style="height:172px;display: flex;align-items: flex-end;">
				<div class="col-md-12 pr-0">
					<div class="row mr-0 ml-0" style="display: flex;
						align-items: flex-end;
						margin-bottom: 5px;">

					<!-- Display product image -->
					<div style="position:relative;left:-30px;"
						 class="col-md-3 ">
						<img src="<?php echo e(asset('images/DKOrecast.png')); ?>"
							alt="" style="width:100px; height:100px;
								background-color:white;border-radius:10px;
								object-fit:contain;display:none;"
							id="display-product-thumb">
					</div>

					<div class="col-md-4 pl-0" >
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

					<div class="col-md-3 row pr-0" >
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
								margin-bottom:0px !important;"
							onclick="">
							<i style="margin-top:-8px;
								padding-left:0;padding-right:0;
								font-size:80px"
								class="fa fa-times-thin">
							</i>
						</button>
					</div>
					</div>
					<!--
					<div style="height:145px;
						padding-right:0 !important;
						align-items:flex-end;justify-content:flex-end"
						class="d-flex col-md-3 pr-0 mt-1">
						<button class="btn btn-sq-lg btn-success
							cstore-redcrab-button"
							id="cstore-redcrab-btn"
							style="display:none;" onclick="">
							<i style="margin-top:-8px;
								padding-left:0;padding-right:0;
								font-size:80px"
								class="fa fa-times-thin">
							</i>
						</button>
					</div>
					-->
					</div>
				</div>
			</div>

			<!-- padding-left:15px or -->
			<div class="row ml-0 mr-0" style="padding-left:0; ">

				<!-- <div class="col-md-12 pl-0 pr-0 payment_btns" style="">
				</div> -->

				<div class="col-md-12 row p-0 ml-0 mr-0 payment_btns align-items-center"
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

					<input class="" type="number"
						style="background-color:#f0f0f0;
						border-width:0;font-size:16px;border-radius:10px;
						height: 70px !important; width:145px;outline: none;
						text-align:center; float: left;"
						placeholder="Item  Discount" id="item_disc"
						min="1" max="100" maxlength="3" size="3" onKeyPress="if(this.value.length==3) return false;" />

					<button class=""
						id="show_discount_percent"
						onclick=""
						style="margin-left:0;margin-right:0;
							display:inline;color:white;font-size:40px;
							background:transparent;
							border:none;
							pointer-events:none;
							width:auto;height:70px;">
							0
					</button>

					<button class="btn btn-sq-lg btn-success
						cstore-redcrab-button"
						id="cstore-redcrab-btn-end"
						style="
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
						onclick=""
						style="border-radius:10px;
							margin-bottom:0 !important;
							font-size:36px;width:70px;height:70px;
							float:right;">%
					</button>
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
<!-- Modal Starta -->
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
				src="<?php echo e(asset('images/sarawakpay_orange.png')); ?>"/>
				<div class="pt-2 d-none" id="inquirebtn">
				<button class="btn btn-success opos-topup-button"
					id="query_order_btn"
					style="height:35px !important; width:80px !important; border-radius:5px"
					onclick="query_order();">Inquire
					</button>
					</div>
			<div id= "sc">
				<h5 class="mt-3" id="amt" data-id="<?php echo e($currency ?? ''); ?>">
				Sending order amount <?php echo e($currency ?? ''); ?>

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

</body>
<?php echo $__env->make('common.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<?php echo $__env->make('cstore.cstore_wallet', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<script>
var products = {};
var payment_type = "";
var dis_cash = "";
var item_amount = 0.00;
var sst = 0.00;
var rounding = 0.00;
var total = 0.00;
var total_amount = "50.00";
var total_product = 1;
var sum_of_raw_amount = 0.00;
var selected_pump = <?php echo e(request()->selected_pump ?? 'null'); ?>;
var terminal_sst = <?php echo e($terminal->tax_percent ?? 6); ?>;
<?php if(!empty($product)): ?>
products['product<?php echo e($product->id??""); ?>'] = {
	name: '<?php echo e(str_limit($product->name, $limit = 50, $end = '...')??""); ?>',
	qty: '<?php echo e($productData["product_quantity"]??""); ?>',
	price: '<?php echo e(number_format($productData["price"])??""); ?>',
	total_amount: '<?php echo e($productData["product_amount"]??""); ?>',
	product_id: '<?php echo e($product->id??""); ?>',
	product_systemid: '<?php echo e($product->systemid??""); ?>',
	product_thumbnail: '<?php echo e($product->thumbnail_1??""); ?>',
	removed: 'false',
};
<?php endif; ?>

$(document).ready(function () {
	 total = calculate_amount();
	$.ajaxSetup({
    	headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
		}
	});

	$('.numpad-enter-payment').removeClass('poa-button-number-payment-enter');
	$('.numpad-enter-payment').addClass('poa-button-number-payment-enter-disabled');
	disable_payment();
})


function select_cash() {
	dis_cash = "";
	payment_type = "cash";
	$("#keypad-div").show();
	$(".payment-div-card").hide();
	$(".payment-div-cash").show();
	$('.numpad-enter-payment').removeClass('poa-button-number-payment-enter');
	$('.numpad-enter-payment').addClass('poa-button-number-payment-enter-disabled');
	$('.numpad-number-payment').removeClass('poa-button-number-payment-disabled');
	$('.numpad-number-payment').addClass('poa-button-number-payment');
	$(".payment-div-cash div").addClass("justify-content-center");
	$("#payment-value").css("color", "#a0a0a0");
	$("#payment-value").html("Cash Received");
	$("#payment-type-amount").html("");
	$("#payment-type-message").html("");
	$("#payment-type-paid-right").html("");
	$("#payment-amount-card-amount").html("");
	$('.numpad-number-payment').show();
	$("#input-cash").removeAttr('disabled');
	$("#input-cash").click();
	$("#input-cash").focus();
	$("#input-cash").val('');
	$("#input-cash-buffer").val('');
}


function select_credit_card() {
	dis_cash = "";
	payment_type = "card";
	$("#keypad-div").show();
	$("#payment-value-card").html("");
	$(".payment-div-cash").hide();
	//$(".payment-div-card").css("display", "inline-block");
	$('.numpad-enter-payment').removeClass('poa-button-number-payment-enter');
	$('.numpad-enter-payment').addClass('poa-button-number-payment-enter-disabled');
	$('.numpad-number-payment').removeClass('poa-button-number-payment-disabled');
	$('.numpad-number-payment').addClass('poa-button-number-payment');
	 $("#payment-type-message").html("");
 	$("#payment-type-amount").html('');
	/*
	amount = ((5 * Math.round((sum_of_raw_amount * 100) / 5)) / 100).toFixed(2);
	$("#payment-type-amount").html(amount);
	$("#payment-type-message").html("Rounding");
	$("#payment-type-paid-right").html((amount - sum_of_raw_amount).toFixed(2));
	$("#payment-amount-card-amount").html("Amount");
	$('.numpad-number-payment').hide();
	 */

	$("#input-cash").attr('disabled');
	$('.numpad-enter-payment').addClass('poa-button-number-payment-enter');
	$('.numpad-enter-payment').removeClass('poa-button-number-payment-enter-disabled');
}

function select_wallet() {
	enable_scanner = true;

	display_wallet_instruction();
	//scan_wallet_qrcode();

	dis_cash = "";
	payment_type = "wallet";
	$("#payment-value-card").html("");
	$(".payment-div-cash").hide();

	//$('.numpad-enter-payment').removeClass('poa-button-number-payment-enter');

	$('.numpad-enter-payment').addClass('poa-button-number-payment-enter-disabled');
	$('.numpad-number-payment').removeClass('poa-button-number-payment-disabled');
	$('.numpad-number-payment').addClass('poa-button-number-payment');

	$('.numpad-enter-payment').addClass('poa-button-number-payment-enter');
	//$('.numpad-enter-payment').addClass('opos-topup-button');
	$('.numpad-enter-payment').removeClass('poa-button-number-payment-enter-disabled');

	$("#payment-type-message").html("");
 	$("#payment-type-amount").html('');
	$("#input-cash").attr('disabled');

}

function set_cash(amount) {
	if (amount == "zero") {

		if (payment_type == "cash") {
			$('.numpad-number-payment').removeClass('poa-button-number-payment-disabled');
			$('.numpad-number-payment').addClass('poa-button-number-payment');
			$(".payment-div-cash div").addClass("justify-content-center");
			$("#payment-value").css("color", "#a0a0a0");
			dis_cash = "";
			$("#payment-value").html("Cash Received");
			$("#payment-type-message").html("");
			$("#payment-type-amount").html("");
			$('.numpad-enter-payment').removeClass('poa-button-number-payment-enter');
			$('.numpad-enter-payment').addClass('poa-button-number-payment-enter-disabled');
		} else if (payment_type == "card") {
			dis_cash = "";
			$('.numpad-number-payment').removeClass('poa-button-number-payment-disabled');
			$('.numpad-number-payment').addClass('poa-button-number-payment');
			$("#payment-value-card").html("");
			$('.numpad-enter-payment').removeClass('poa-button-number-payment-enter');
			$('.numpad-enter-payment').addClass('poa-button-number-payment-enter-disabled');
		}

		clear_all_discounts()
		$("#receipt_disc").val('')
		$("#receipt_disc").css("font-size", "16px");

	} else {
		if (payment_type == "cash") {
			$(".payment-div-cash div").removeClass("justify-content-center");
			$("#payment-value").css("color", "black");
			dis_cash = dis_cash + amount;
			$("#payment-value").html((parseFloat(dis_cash) / 100).toFixed(2));
			calculate_change();
			dis_cash_ = (parseFloat(dis_cash) / 100).toFixed(2);
			if (dis_cash_ >= parseFloat(total_amount)) {
				$('.numpad-number-payment').removeClass('poa-button-number-payment');
				$('.numpad-number-payment').addClass('poa-button-number-payment-disabled');
				$('.numpad-enter-payment').removeClass('poa-button-number-payment-enter-disabled');
				$('.numpad-enter-payment').addClass('poa-button-number-payment-enter');
				// if(pumpData['pump' + selected_pump].product){
				// $('.numpad-enter-payment').removeClass('poa-button-number-payment-enter-disabled');
				// $('.numpad-enter-payment').addClass('poa-button-number-payment-enter');
				// }
			}

		} else if (payment_type == "card") {
			if (dis_cash.length < 4) {
				dis_cash = dis_cash + amount;
				$("#payment-value-card").html(dis_cash);
			}
			if (dis_cash.length == 4) {
				$('.numpad-number-payment').removeClass('poa-button-number-payment');
				$('.numpad-number-payment').addClass('poa-button-number-payment-disabled');
				$('.numpad-enter-payment').removeClass('poa-button-number-payment-enter-disabled');
				$('.numpad-enter-payment').addClass('poa-button-number-payment-enter');
			}
		}
	}
}


function calculate_change() {

	dis_cash = $("#input-cash-buffer").val();

	if (dis_cash == '') {
		dis_cash = 0;
	}

	dis_cash_ = (parseFloat(dis_cash) / 100).toFixed(2);
	var change_amount = dis_cash_ - total_amount;
	$("#payment-type-message").html("Change");
	$("#payment-type-amount").html(parseFloat(change_amount).toFixed(2));
		//console.log('change_amount',change_amount);
	if (change_amount >= 0) {
		$("#input-cash").attr('disabled', true);
		$('.numpad-enter-payment').addClass('poa-button-number-payment-enter');
		$('.numpad-enter-payment').removeClass('poa-button-number-payment-enter-disabled');
	} else {
		$('.numpad-enter-payment').removeClass('poa-button-number-payment-enter');
		$('.numpad-enter-payment').addClass('poa-button-number-payment-enter-disabled');
	}
}


function showProduct(id) {
	$("#display-product-systemid").css("display", "unset")
	$("#display-product-name").css("display", "unset")
	$("#display-product-price").css("display", "unset")
	$("#display-product-thumb").css("display", "unset")
	$("#cstore-redcrab-btn").css("display", "unset")
	$("#curr-display").css("display", "unset")
	$("#cstore-redcrab-btn").css('display', 'unset');

	$("#display-product-systemid").html(products['product' + id].product_systemid);
	$("#display-product-name").html(products['product' + id].name);
	$("#display-product-price").html(products['product' + id].price);
	$("#display-product-thumb").attr("src", "/images/product/" + products['product' + id].product_systemid + "/thumb/" + products['product' + id].product_thumbnail);
	$("#show_discount_percent").text(products['product' + id].discount);
	$('.payment_btns').css('display', 'block');
	$("#cstore-redcrab-btn").attr("onclick", "removePoduct('" + id + "')");
}


function removePoduct(id) {
	$("#display-product-systemid").html("");
	$("#display-product-name").html("");
	$("#display-product-price").html("");
	$("#display-product-thumb").attr("src", "");
	$("#display-product-thumb").hide();
	$("#cstore-redcrab-btn").attr("onclick", "");
	$("#product_row" + id).hide();
	$("#curr-display").css("display", "none")
	$("#cstore-redcrab-btn").css('display', 'none');
	$('.payment_btns').css('display', 'none');
	if (products[`product${id}`] != undefined) {
		products['product' + id].removed = "true";
		delete products[`product${id}`];
	}

	total = calculate_amount();
	deletevalue(id , total);
	render_product()
 	//alert("dsad");
}


function calculate_amount() {
	sst = 0.00;
	item_amount = 0.00;
	sum_of_raw_amount = 0.00;

	for (i in products) {
		if (products[i].removed == "false") {
			sum_of_raw_amount = parseFloat(products[i].total_amount) + parseFloat(sum_of_raw_amount);
		}
	}

	var amount_total = ((5 * Math.round((parseFloat(sum_of_raw_amount) * 100) / 5)) / 100);
	<?php if(!empty($terminal)): ?>
	sst = parseFloat(sst) + parseFloat((sum_of_raw_amount) - ((sum_of_raw_amount) / (1 + (<?php echo e($terminal->tax_percent); ?>/100))));

	item_amount = parseFloat(sum_of_raw_amount) - parseFloat(((sum_of_raw_amount) - ((sum_of_raw_amount) / (1 + (<?php echo e($terminal->tax_percent); ?>/100)))));
	<?php else: ?>
	sst = parseFloat(sst) + parseFloat((sum_of_raw_amount) - ((sum_of_raw_amount)));
	item_amount = parseFloat(sum_of_raw_amount) - parseFloat(((sum_of_raw_amount) - ((sum_of_raw_amount))));
	<?php endif; ?>

	rounding = parseFloat(amount_total) - parseFloat(sum_of_raw_amount);
	rounding = parseFloat(rounding.toFixed(2));
	total = amount_total;
	total_amount = total;

	/* Check for occurence of NaN */
	if (item_amount != 'NaN') {
		$("#item-amount").html(number_format(item_amount.toFixed(2)));
	}

	$("#sst-val").html(number_format(sst.toFixed(2)));

	$("#rounding-val").html(number_format(rounding.toFixed(2)));

	$("#total-val").html(number_format(total.toFixed(2)));
	return total.toFixed(2);
}

function reset_window() {
/*	length = Object.keys(products).length;
	for( k = 1; k <= length; k++) {
		removePoduct(k);
	}

	select_cash();
	$('.numpad-enter-payment').removeClass('poa-button-number-payment-enter');
	$('.numpad-enter-payment').addClass('poa-button-number-payment-enter-disabled');
	$('.numpad-number-payment').removeClass('poa-button-number-payment');
	$('.numpad-number-payment').addClass('poa-button-number-payment-disabled');
 */
 	$(".payment-div-cash").show();
	$("#input-cash").val('');
	$("#input-cash-buffer").val('')
	$("#receipt_disc").val('')
	$("#receipt_disc").css("font-size", "16px");
	products = {};
	removePoduct(0)
	render_product();
	localStorage.removeItem('clear_products');
	localStorage.setItem("clear_products", 'yes');

}

function process_finish(){
	dis_cash_ = (parseFloat(dis_cash) / 100).toFixed(2);
	var change_amount = Math.abs(dis_cash_ - total_amount).toFixed(2);

	dis_cash_ = isNaN(dis_cash_) ? total: dis_cash_;
	change_amount = isNaN(change_amount) ? 0:change_amount;

	cal_item_amount = item_amount.toFixed(2);
	cal_sst 		= sst.toFixed(2);
	cal_rounding 	= rounding.toFixed(2);
	cal_total		= total;
	for( i in products) {
		p = products[i];
		p.price = parseFloat(p.price.replace(/\,/g,''));
	}

	$.post("<?php echo e(route('local_cabinet.receipt.create.cstore')); ?>", {
			products: products,
			payment_type: payment_type,
			cash_received: dis_cash_,
			change_amount: change_amount,
			cal_item_amount,
			cal_sst,
			cal_rounding,
			cal_total
		})
		.done( (response) => {

			//console.log('PR local_cabinet.receipt.create:');
			//console.log('PR ***** SUCCESS *****');
			//console.log('response='+JSON.stringify(response));
			//my ESCPOS printing function
			receipt_id = response;
			////console.log('data='+JSON.stringify(data));

			/* Need to have Qz.io running, otherwise print_receipt()
			 * will bomb out and will not execute lines after it. We
			 * trap the error so that we can still run even if Qz is
			 * NOT running!! */
			try {
				// Output receipt via thermal printer
				//print_receipt(response);

				// Open cash drawer
				//open_cashdrawer();

				// Automatically generate upon pressing Enter
				cstore_generate_pdf(receipt_id);

			} catch (error) {
				/* This will catch if Qz.io is not being run!! */
				//alert('ERROR! print_receipt(). Check Qz!!');
				//alert("ERROR print_receipt(): " + JSON.stringify(error));
				console.error("ERROR! Check if Qz.io is being run!!");
				console.error("ERROR: "+ JSON.stringify(error));
			}
		})
		.fail( (e) => console.error(e));

	$('.numpad-enter-payment').removeClass('poa-button-number-payment-enter');
	$('.numpad-enter-payment').addClass('poa-button-number-payment-enter-disabled');
	reset_window();

}


// Automatically generate PDF upon pressing Enter
function cstore_generate_pdf(receipt_id) {
	$.ajax({
            url: "<?php echo e(route('sales.cstore.generate.pdf')); ?>",
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


// For printing ESCPOS
function print_receipt(receipt_id){

	//console.log('PR print_receipt()');
	//console.log('PR receipt_id='+JSON.stringify(receipt_id));

	$.ajax({
        url: "/print_receipt",
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'post',
        data: {
        	'receipt_id':receipt_id,
        },
        success: function (response) {
			var error1=false, error2=false;
        	//console.log('PR '+JSON.stringify(response));

			try {
				eval(response);
			} catch (exc) {
				error1 = true;
				console.error('ERROR eval(): '+exc);
			}

			if (!error1) { try {
					escpos_print_template();
				} catch (exc) {
					error2 = true;
					console.error('ERROR escpos_print_template(): '+exc);
				}
			}
        },
        error: function (e) {
        	//console.log('PR '+JSON.stringify(e));
        }
    });
}

function disable_payment(){
	$(".poa-cash-btn").removeClass('poa-button-cash');
	$(".poa-cash-btn").addClass('poa-button-cash-disabled');

	$(".poa-card-btn").removeClass('poa-button-credit-card');
	$(".poa-card-btn").addClass('poa-button-credit-card-disabled');

	$('.numpad-enter-payment').removeClass('poa-button-number-payment-enter');
	$('.numpad-enter-payment').addClass('poa-button-number-payment-enter-disabled');
	$('.numpad-number-payment').removeClass('poa-button-number-payment');
	$('.numpad-number-payment').addClass('poa-button-number-payment-disabled');

	$("#input-cash").attr('disabled');
	$( "#receipt_disc" ).removeAttr('disabled')

	$(".cstore-button-wallet").addClass("cstore-button-wallet-disabled");
}

function enable_payment(){
	$(".poa-cash-btn").addClass('poa-button-cash');
	$(".poa-cash-btn").removeClass('poa-button-cash-disabled');

	$(".poa-card-btn").addClass('poa-button-credit-card');
	$(".poa-card-btn").removeClass('poa-button-credit-card-disabled');

	$(".cstore-button-wallet").removeClass("cstore-button-wallet-disabled");

	$('.numpad-number-payment').addClass('poa-button-number-payment');
	$('.numpad-number-payment').removeClass('poa-button-number-payment-disabled');
	$( "#receipt_disc" ).attr('disabled')
}

$("#product_search").on( "keyup", function( event ) {
	query_string = $(event.target).val();
	url = '<?php echo e(route('cstore.search', 'SEARCH_STRING')); ?>'.
		replace('SEARCH_STRING', query_string);

	$.get(url)
		.done( (res) => $('#product_data_screen_a').html(res))
		.fail( (res) => console.error(res));
});



document.addEventListener("DOMContentLoaded", function (event) {
	window.onstorage = function (e) {

		switch(e.key) {

			case "update_product":
				row_id = JSON.parse(localStorage.getItem('update_product'));

				if(row_id){

					var myEle = document.getElementById('show_hide_single_product'+row_id.id);
					if(myEle){
						myEle.remove();
						var myEle2 = document.getElementById('product_row'+row_id.id);
						if(myEle2){
							myEle2.remove();
						}
						removePoduct(row_id.id);
					}

					$.ajax({
						url: "/getproduct",
						headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
						type: 'get',
						data: {
							'product_id':row_id.id,
						},
						success: function (response) {
							$('#product_data_screen_a').append(response);
							localStorage.removeItem('update_product');
							i = 1;
							$('.index_div').each(function() {
								$(this).html(i);
								i++;
							});
						},
						error: function (e) {
							//console.log('PR '+JSON.stringify(e));
						}
					});
				}
				break;

			case "hide_show_product":
				row_id = JSON.parse(localStorage.getItem('hide_show_product'));

				if(row_id.type=='hide'){
					$('#show_hide_single_product'+row_id.id).hide();
					$('#product_row'+row_id.id).hide();
					removePoduct(row_id.id);
				}else{

					if ($("#product_data_screen_a").find('#show_hide_single_product'+row_id.id).length == 0){
						$.ajax({
							url: "/getproduct",
							headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
							type: 'get',
							data: {
								'product_id':row_id.id,
							},
							success: function (response) {
								$('#product_data_screen_a').append(response);
							},
							error: function (e) {
								//console.log('PR '+JSON.stringify(e));
							}
						});

					}else{
						$('#show_hide_single_product'+row_id.id).show();
						$('#product_row'+row_id.id).show();
					}
				}
				localStorage.removeItem('hide_show_product');
				break;

			case "delete_product":
				row_id = JSON.parse(localStorage.getItem('delete_product'));
				$('#show_hide_single_product'+row_id.id).remove();
				$('#product_row'+row_id.id).remove();
				removePoduct(row_id.id);
				localStorage.removeItem('delete_product');
				break;
		}
	}
});


function add_product(p_id, product_systemid, product_thumbnail, product_name, qty, product_amount) {
	//console.log('add_product: p_id='+p_id);
	//console.log('add_product: product_systemid='+product_systemid);
	//console.log('add_product: product_thumbnail='+product_thumbnail);
	//console.log('add_product: product_name='+product_name);
	//console.log('add_product: qty='+qty);
	//console.log('add_product: product_amount='+product_amount);

	if (products[`product${p_id}`] == undefined) {
		products[`product${p_id}`] = {
			name: product_name,
			qty: qty,
			price: product_amount,
			total_amount: product_amount,
			product_id: p_id,
			product_systemid: product_systemid,
			product_thumbnail: product_thumbnail,
			discount: 0,
			discount_amount: 0,
			removed: 'false',
		};
	} else {
		products[`product${p_id}`].qty += 1;
		products[`product${p_id}`].total_amount =
			(products[`product${p_id}`].qty *
			products[`product${p_id}`].price).toFixed(2);
	}

	render_product();
}


function changeValue(totat) {
  //var x = window.open("", "myWindow", "width=200,height=100");
  localStorage.removeItem('update-screen-e');
  localStorage.setItem("update-screen-e", JSON.stringify(products));
  //x.close();
}


function deletevalue(id , total) {

	//var x = window.open("", "myWindow", "width=200,height=100");
	var payload = {};
	payload = {
		id: id,
		total: total
	};
	localStorage.removeItem('delete_product');
	localStorage.setItem("delete_product",JSON.stringify(payload));
}


function number_format(nStr)
{
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}

function caculate_qty(){
	var total_qty = $('#total-qty').val();
	if (total_qty == ""){
		document.getElementById('total-qty').value = 1;
		var now = $('#total-qty').val();
	}else{
		var now = $('#total-qty').val();
		document.getElementById('total-qty').value = now + 1;
	}
}


function get_product_name(product_name){
	var product_exist = $('#get-product-name').val();
	if (product_exist == ""){
		document.getElementById('get-product-name').value = product_name;
	}else{
		document.getElementById('get-product-name').value = product_exist+"-"+product_name;
	}
}

function render_product() {
	html = '';
	calculate_item_amount();
	caculate_qty();
	for( i in products) {
		p = products[i];
		html += `
		<tr id="product_row${p.product_id}" onclick="showProduct(${p.product_id})">
			<td style="width: 65%">
				<img
					style="width:25px;height:25px;object-fit:contain;cursor:pointer;background-color:white;border-radius:5px"
					src="/images/product/${p.product_systemid}/thumb/${p.product_thumbnail}"
					alt="" class="single">
				<span style="cursor:pointer" class="text-white">
					${p.name}
				</span>
			</td>

			<td style="width: 10%;align:left;"
				class="text-white">
				${p.qty}
			</td>
			<td style="width: 25%;text-align: right"
				class="text-white pr-4">
				${number_format(p.total_amount)}
			</td>
		</tr>
		`;
	get_product_name(p.name);
 }

	$('#product_table_staged').html(html);

	total = calculate_amount();

	changeValue(total);

	if (html.length > 1)
		enable_payment();
	else
		disable_payment();

	reset_payment_values();
}



function reset_payment_values() {
	$("#payment-value").css("color","#a0a0a0");
	$("#payment-value").html("Cash Received");
	$("#payment-div-cash div").addClass("justify-content-center");
	$(".payment-div-card").hide();
	$("#payment-div-cash").show();
	$("#payment-value").css("color","#a0a0a0");
	$("#payment-value").html("Cash Received");
	$("#payment-div-cash div").addClass("justify-content-center");
	$("#payment-value-card").html("");
	$("#payment-type-message").html("");
	$("#payment-type-amount").html('');
	$("#input-cash").val('');
	$("#input-cash-buffer").val('')
}

function calculate_item_amount() {
	for( i in products) {
		p = products[i];

		//console.log('p='+ JSON.stringify(p));

		total_amount 	= p.qty * parseFloat(p.price.replace(/\,/g,''));
		discount_amount = total_amount / 100 * p.discount;
		total_amount 	= total_amount - discount_amount;

		products[i].discount_amount	= discount_amount.toFixed(2);

		products[i].total_amount	= total_amount.toFixed(2);

		cal_total_amount 			= total_amount / (1 + (terminal_sst / 100));
		cal_sst 					= total_amount - cal_total_amount;
		products[i].sst				= cal_sst.toFixed(2);
		products[i].item_amount		= cal_total_amount.toFixed(2);
		products[i].rounding 		= total_amount  - (cal_total_amount + cal_sst);

	}
}

$(function () {
	$( "#item_disc" ).keyup(function() {
		var max = parseInt($(this).attr('max'));
		var min = parseInt($(this).attr('min'));

		vals = $(this).val();
		vals =vals.replace('.', '');
		if(vals==''){
			$(this).val('');
		} else if (vals > max) {
			$(this).val(max);
		} else if (vals < min) {
			$(this).val(min);
		} else {
			$(this).val(vals);
		}
		 $(this).css("font-size", "36px");
		if(vals==''){
		 	$(this).css("font-size", "16px");
		}
		 //$(this).removeAttr('placeholder');

	});
});


$(function () {
	$( "#cstore-button-receiptdisc-end" ).click(function() {

		if ($( "#item_disc" ).val() == '')
			return;

		display_product_systemid = $("#display-product-systemid").text();
		$('#show_discount_percent').html($( "#item_disc" ).val());
		if (display_product_systemid.length > 0) {
			item_discount(display_product_systemid, $( "#item_disc" ).val() );
			render_product();
		}

		$( "#item_disc" ).val('');
		$("#item_disc").css("font-size", "16px");
	});
});


$(function () {
	$( "#cstore-button-receiptdisc" ).click(function() {
		if ($( "#receipt_disc" ).val() == '')
			return;

		discount = $( "#receipt_disc" ).val();

		for( i in products) {
			item_discount(products[i].product_systemid, discount);
		}

		render_product();

		$( "#receipt_disc" ).val('');
		$("#receipt_disc").css("font-size", "16px");
	});
});


function item_discount(product_systemid, discount) {
	for( i in products) {
		if (products[i].product_systemid == product_systemid)
			products[i].discount = parseFloat(discount);
	}
}


function clear_all_discounts() {

	for( i in products) {
		item_discount(products[i].product_systemid, 0);
	}

	removePoduct(0)
	render_product();
}


$(function () {
	$( "#cstore-redcrab-btn-end" ).click(function() {
		display_product_systemid = $("#display-product-systemid").text();

		$('#show_discount_percent').html(0);
		$( "#item_disc" ).val('');
		$("#item_disc").css("font-size", "16px");

		if (display_product_systemid.length > 0) {
			cancel_item_discount(display_product_systemid);
			render_product();
		}
	});
});


function cancel_item_discount(product_systemid) {
	for( i in products) {
		if (products[i].product_systemid == product_systemid)
			products[i].discount = 0;
	}
}


$(function () {
	$( "#receipt_disc" ).keyup(function() {
		var max = parseInt($(this).attr('max'));
		var min = parseInt($(this).attr('min'));

		vals = $(this).val();
		vals =vals.replace('.', '');
		if(vals==''){
			$(this).val('');
		}else if (vals > max) {
			$(this).val(max);
		} else if (vals < min) {
			$(this).val(min);
		} else {
			$(this).val(vals);
		}
		 $(this).css("font-size", "36px");

		if(vals==''){
		 	$(this).css("font-size", "16px");
		}
		 //$(this).removeAttr('placeholder');
	});
});


filter_price("#input-cash","#input-cash-buffer");

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


var keys = [];
var index = 0;

$(document).ready(function (){
	window.addEventListener("keydown",
	function(e){
		console.log(e.keyCode);

		if(e.keyCode != 16 ){
			if( e.keyCode != 13){
				keys[index++] = e.key;
			}else {
				console.log('enable_scanner='+enable_scanner);

                if(enable_scanner == true){
                    scan_wallet_qrcode(keys)
                }else{
                    add_scanned_keycode(e, keys);
                }
			}
		}
	}, false
	);
});

/* Chrome, Safari and Opera */

function add_scanned_keycode(e, keys) {
	console.log(keys.join(""));
	add_product_scanner(keys.join(""))
	keys.length = 0;
	flag = 0;
	index = 0;
}


function add_product_scanner(product_barcode) {
	$.post("cstore/barcode-fetch-product", {"search_string":product_barcode})
	.done(function(res) {
		console.log('info', 'add_product_scanner: After AJAX: res='+
			JSON.stringify(res));

		// Test for "Barcode not found" error
		if (!res.error) {

			// Yes, add product to item list
			add_product(res.id, res.systemid, res.thumbnail_1, res.name, 1, res.price);

		} else {
			// That was an illegal/invalid barcode
			$("#message").html("Barcode not found");
			$("#messageModal").modal('show');
			setTimeout(function () {
				$("#messageModal").modal('hide')
				$("#message").html('')
			}, 2000);
		}

	}).fail(function(res)	{
		$("#message").html("Scanning error");
		$("#messageModal").modal('show');
		setTimeout(function () {
			$("#messageModal").modal('hide')
			$("#message").html('')
		}, 2000);
	});
}

</script>
<?php echo $__env->make('cstore.cstore_wallet', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('common.web', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/emmanuel/oceania/trunk/oceania/resources/views/cstore/cstore.blade.php ENDPATH**/ ?>