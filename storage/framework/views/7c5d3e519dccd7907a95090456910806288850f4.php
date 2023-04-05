<!-- CSTORE_WALLET BEGINS -->
<script>

function display_wallet_instruction() {

	$('#sc').removeClass('d-none');
    enableButton();
    $('#overwrite').html("");

	$("#wrong_key_modal .close").click();
	$('#wallet_instruction_modal').modal('toggle');
	//////console.log("Pump No" , );
}


var enable_scanner = false;
function scan_wallet_qrcode(keys) {
	var scanned_str = '';

    if (status == 0 && typeof keys !== 'undefined'){

        status = 1;
        scanned_str = keys.join('');

		console.log('scanned_str='+scanned_str);

        // Copying order amount from screen A
		$('#spay_order_amt').text(
            $('#total-val').text()
        );
        var get_product_name = $('#get-product-name').val();
		var total_qty = $('#total-qty').val();
        var amount = $('#total-val').text();
        var myArr = get_product_name.split("-");
        var product_name = myArr[0];
        var unique = myArr.filter(onlyUnique);
        var array_length = unique.length;
        console.log(unique , "length = " , array_length);

        console.log("pname" , get_product_name , "Qty = " , total_qty , "Amount" ,amount);
        if (array_length == 1){
            var product_name = product_name;
        }
        else if(array_length >= 2){
            var product_name = "Multiple:"+product_name;
        }
        $("#wrong_key_modal .close").click();
        $("#sarawak_pay .close").click();
        parse_wallet(scanned_str , amount , product_name);
        keys =[];
    }
    enable_scanner = false;
}


function parse_wallet(qr_code , amount , product_name) {

	var st = qr_code.substring(0, 2);
	console.log("parse_wallet: qr_code=" , qr_code);

	// Parse for wallet service provider
	switch(st) {
		case "96":	// Sarawak Pay
		$("#wallet_instruction_modal .close").click();
		$('#sarawak_pay').modal('toggle');
		//console.log('Sarawak Pay');
		if (typeof qr_code !== 'undefined' && qr_code !== '') {
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

function onlyUnique(value, index, self) {
  return self.indexOf(value) === index;
}
var oldmerOrderNo = 0;

function spay_create_order(qr_code , amount , product_name) {
	//console.log(qr_code,amount,product_name);

	var date = new Date();
  	var merOrderNo = date.getTime();
  	 oldmerOrderNo = merOrderNo;

	$.ajax({
		type: "POST",
		url: '<?php echo e(route('wallet.spay.create_order')); ?>', // first request call
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
}


function query_order(){
	$('#sc').addClass('d-none'); // this shows Sending order amount

	disableButton();

	//console.log("mar" , oldmerOrderNo);

	$.ajax({
		type: "POST",
		url: '<?php echo e(route('spay.query_order')); ?>', // second request
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

			console.log("Order status and resmsg=", orderStatus, resmsg);
			// if orderStatus 1 Payment successful button hide
			if(orderStatus == 1){
				c = setTimeout(() => {
					$('#overwrite').html("Payment successful.<br>");
					$('#inquirebtn').addClass('d-none');
					enableButton();
				}, 3000);

				// Payment successful, now authorize the pump!
				process_finish();

			}else{// else Payment is not successful. inquire button enable
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

<!-- CSTORE_WALLET END -->
<?php /**PATH /home/emmanuel/oceania/trunk/oceania/resources/views/cstore/cstore_wallet.blade.php ENDPATH**/ ?>