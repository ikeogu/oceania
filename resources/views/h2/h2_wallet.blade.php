<!-- OPOS_WALLET BEGINS -->
<script>

function display_wallet_instruction(my_pump) {

	 $('#sc').removeClass('d-none'); 
    enableButton();
    $('#overwrite').html("");

	$("#wrong_key_modal .close").click();
	$('#wallet_instruction_modal').modal('toggle'); 
	//console.log("Pump No" , my_pump); 
}
var enable_scanner = false;
function scan_wallet_qrcode(my_pump , enable_scanner) {
	var scanned_str = '';
	if (enable_scanner){
	$(document).ready(function() {
		var keys = [];
		var index = 0;
		var status = 0;

		window.addEventListener("keydown", function(e){
			if (e.keyCode != 18 && e.keyCode != 16) {
				if (e.keyCode != 13) {
					keys[index++] = e.key;
					//console.log('key='+e.key);

				} else {
					if (status == 0){
						status = 1;
						scanned_str = keys.join('');

						// Copying order amount from screen A
						$('#spay_order_amt').text(
							$('#grand-total-val-calculated-'+my_pump).text()
						);

						var amount = $('#grand-total-val-calculated-'+my_pump).text();

						$("#wrong_key_modal .close").click();
						$("#sarawak_pay .close").click(); 
						parse_wallet(my_pump , scanned_str , amount);
						keys =[];
					}
				}
			} 
		}, false);
	});
}
}


function parse_wallet(my_pump, qr_code , amount) { 

	var st = qr_code.substring(0, 2);
	//console.log("qr_code=" , qr_code);
	
	// Parse for wallet service provider
	switch(st) {
		case "96":	// Sarawak Pay
		$("#wallet_instruction_modal .close").click(); 
		$('#sarawak_pay').modal('toggle'); 
		//console.log('Sarawak Pay');
		//if (!empty(qr_code)) {
			if (qr_code != null) {
			spay_create_order(my_pump, qr_code , amount);
		}
		break;

		case "":
		$("#wallet_instruction_modal .close").click(); 
		$("#sarawak_pay .close").click(); 
		$("#wrong_key_modal .close").click();
		//console.log('Nothing Passed');
		break;
			
		default:
		$("#wallet_instruction_modal .close").click(); 
		$('#wrong_key_modal').modal('toggle');
		setTimeout(() => {
			$("#wrong_key_modal .close").click()
		}, 3000); 
		//console.log('Unlisted Wallet Provider');
	}
}


function select_wallet() {


	localStorage.setItem("payment_type","wallet")
	enable_scanner = true;
	var selected_pump = sessionStorage.getItem("selected_pump")

	// $(".enter-btn").removeClass("poa-button-number-payment-zero-disabled")
    // $(".enter-btn").addClass("poa-button-cash")

    $(".enter-btn").addClass("poa-button-cash")
    $(".enter-btn").removeClass("poa-button-cash-disabled")
    $(".enter-btn").removeClass("poa-button-number-payment-zero-disabled")

    $("#input-cash0").hide();
    $("#input-cash"+selected_pump).hide();
    
    $(".record").removeClass("input-div-0")
    $(".record1").removeClass("input-div-0")
    $(".record").addClass("input-div-0")
    $(".record1").addClass("input-div-0")

// end
	
	var my_pump = $('#selected-pump-id').text(); 

	console.log("this " , my_pump);

	if (selected_pump != 0) {
		/* Here you pop up the modal */
		display_wallet_instruction(my_pump);

		/* Here you scan QR code */
		scan_wallet_qrcode(my_pump , enable_scanner); 
		
		/* WARNING: Don't disturb these code below!!! */
		$('#button-cash-payment' + selected_pump).removeClass('selected_preset_button');
		$('.finish-button-' + selected_pump).removeClass('poa-finish-button-disabled');
		$('.finish-button-' + selected_pump).addClass('opos-topup-button');
		$(`#buffer-input-cash${selected_pump}`).val('');
		$(`#input-cash${selected_pump}`).val('');
		$(`#input-cash${selected_pump}`).hide();
		dis_cash['pump' + selected_pump].dis_cash = "";
		dis_cash['pump' + selected_pump].payment_type = "wallet";
		$(`#change-val-calculated-${selected_pump}`).text('0.00');

		$(".payment-div-refund" + selected_pump).hide();
		$("#payment-div-cash-card" + selected_pump).hide();

		check_enter()
	}
}

var oldmerOrderNo = 0;

function spay_create_order(my_pump, qr_code , amount) {
	/*
	var curType = "RM";
	var notifyURL = "https://google.com";
	var merchantId = "M100004540";
	var detailURL = "https://youtube.com";
	var remark ="Special Promotional Pack";
	var transactionType = 1;
	var qr = "965280156454333771";
	*/

	var product_name = $('#table-PRODUCT-'+my_pump).text();
	var date = new Date();
  	var merOrderNo = date.getTime();
  	oldmerOrderNo = merOrderNo;

  	console.log("my_pump" ,my_pump, "qr_code" ,qr_code , "amount" , amount , "product_name" ,product_name ,"merOrderNo",  merOrderNo);

		$.ajax({		
		type: "POST",
		url: '{{ route('wallet.spay.create_order') }}', // first request call
		data: {
			//merchantId: merchantId,
			qrCode: qr_code,
			//curType: curType,
			//notifyURL: notifyURL,
			merOrderNo: merOrderNo,
			goodsName: product_name,
			//detailURL : detailURL,
			orderAmt: amount,
			//remark: remark,
			//transactionType: transactionType
		},
		success: function (data) {

			var a, b, c;
			a = setTimeout(() => {
				$('#overwrite').removeClass('d-none'); // use for response data
				$('#sc').addClass('d-none'); // this shows Sending order amount
			}, 3000); 

			var response = JSON.parse(data.data.response);
			var ResStatus = response.ResStatus;
			var resmsg = response.ResMsg;
			//console.log(ResStatus , resmsg);
			//console.log("Not Successful!"+'</n>'+resmsg);

			if (resmsg == 'Communication success') {
				resmsg = '';
			} else {
				resmsg = resmsg + '.<br>';
			}

			if(ResStatus != 0){
				b = setTimeout(() => {
					var html = ("Payment is not successful."+'<br>'+
						resmsg + 'Please rescan.');
					$('#overwrite').html(html);
				}, 3000); 

			}else{
				c = setTimeout(() => {
					$('#overwrite').html("Order received, awaiting confirmation from wallet user.<br>");
					// if ResStatus 0 show inquire btn
					$('#inquirebtn').removeClass('d-none'); 
				}, 3000); 
			}
		},
		error: function (response) {
			//$('#inquirebtn').removeClass('d-none');
			//console.log("error");
			setTimeout(() => {
				$('#overwrite').html("Some parameters is in error. Please scan again.");
			}, 3000); 
		}
	});
	// Execute Ajax SPayController::SPayCreateOrder();
	// Pass in dummy values for the fields.
}
	function query_order(){

		$('#sc').addClass('d-none'); // this shows Sending order amount
		disableButton();

		//console.log("mar" , oldmerOrderNo);

		$.ajax({		
		type: "POST",
		url: '{{ route('spay.query_order') }}', // second request
		data: {
			merOrderNo: oldmerOrderNo,
		},
			success: function (data) {
			var a, b, c;
			a = setTimeout(() => {
				$('#overwrite').removeClass('d-none');
				$('#sc').addClass('d-none');
			}, 3000); 
			console.log("This data is coming = " ,data);
			var response = JSON.parse(data.data.response);
			var orderStatus = response.orderStatus;
			var resmsg = response.ResMsg;

			if (resmsg == 'Communication success') {
				resmsg = '';
			} else {
				resmsg = resmsg + '.<br>';
			}

			console.log("Order status and resmsg=" ,orderStatus , resmsg);
			// if orderStatus 1 Payment successful button hide
			if(orderStatus == 1){ 
				c = setTimeout(() => {
					$('#overwrite').html("Payment successful.<br>");
					$('#inquirebtn').addClass('d-none');
                   enableButton();
				}, 3000); 

				// Payment successful, now authorize the pump!
				process_finish();

			// else Payment is not successful. inquire button enable
			}else{	
				b = setTimeout(() => {
					var html = ("Payment is not successful.<br>"+
					resmsg + 'Order received, awaiting confirmation from wallet user.');
					$('#overwrite').html(html);
					enableButton();
				}, 3000); 
				
			}
		},
		// if error Something went wrong inquire button enable
		error: function (response) { 
			enableButton();
			$('#inquirebtn').removeClass('d-none');
			console.log("error");
			setTimeout(() => {
				$('#overwrite').html("Order received, awaiting confirmation from wallet user.");
			}, 3000); 
		}

	});
}

function disableButton() {
	var btn = document.getElementById('query_order_btn');
	btn.disabled = true;
	btn.innerText = 'Inquire';
}

function enableButton() {
	var btn = document.getElementById('query_order_btn');
	btn.disabled = false;
	btn.innerText = 'Inquire';
}    

</script>

<!-- OPOS_WALLET END -->
