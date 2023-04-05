<!-- CARPARK_WALLET BEGINS -->
<script>

function display_wallet_instruction() {

	$('#sc').removeClass('d-none'); 
    enableButton();
    $('#overwrite').html("");

	$("#wrong_key_modal .close").click();
	$('#wallet_instruction_modal').modal('toggle'); 
	//console.log("Pump No" , ); 
}
var enable_scanner = false;

function scan_wallet_qrcode(enable_scanner , enable_scanner) {
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

						// Copying order amount from screen 

						var amount = $('#total').text();
						var product_name = $('#description').text();
						$('#spay_order_amt').text(
							amount
						);

						$("#wrong_key_modal .close").click();
						$("#sarawak_pay .close").click(); 
						parse_wallet(scanned_str , amount , product_name);
						keys =[];
					}
				}
			} 
		}, false);
	});
}
}


function parse_wallet(qr_code , amount , product_name) { 

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
			spay_create_order(qr_code , amount , product_name);
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

var oldmerOrderNo = 0;

function spay_create_order(qr_code , amount , product_name) {

	var date = new Date();
  	var merOrderNo = date.getTime();
  	oldmerOrderNo = merOrderNo;

  	//console.log("Qr code is = " , qr_code , "Amount=" , amount , "product_name=" , product_name , "merOrderNo=" , merOrderNo);
	
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
					$('#inquirebtn').removeClass('d-none'); // if ResStatus 0 show inquire btn
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
			if(orderStatus == 1){ // if orderStatus 1 Payment successful button hide
				c = setTimeout(() => {
					$('#overwrite').html("Payment successful.<br>");
					$('#inquirebtn').addClass('d-none');
                   enableButton();
				}, 3000); 

				// Payment successful, now authorize the pump!
				process_finish();

			}else{			// else Payment is not successful. inquire button enable
				b = setTimeout(() => {
					var html = ("Payment is not successful.<br>"+
					resmsg + 'Order received, awaiting confirmation from wallet user.');
					$('#overwrite').html(html);
					enableButton();
				}, 3000); 
			}
		},
		error: function (response) { // if error Something went wrong inquire button enable
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

<!-- CARPARK_WALLET END -->
