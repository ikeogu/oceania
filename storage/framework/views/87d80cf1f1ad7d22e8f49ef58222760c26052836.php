<!-- FUELFULLTANK_WALLET BEGINS -->
<script>
var ft_selected_pump = 0;

function display_full_tank_wallet_instruction(my_pump) {
	$('#sc-full-fulltank').removeClass('d-none');
	enableButton_full_tank();
	$('#overwrite_full_tank').html("");

	$("#wrong_key_modal .close").click();
	$('#wallet_instruction_modal').modal('toggle');
	//console.log("Pump No" , my_pump);
}

var enable_scanner = false;
function scan_full_tank_wallet_qrcode(my_pump , enable_scanner=false) {
	console.log('WS scan_full_tank_wallet_qrcode: my_pump='+my_pump+
		', enable_scanner='+enable_scanner);
		log2laravel('DEBUG', 'WS scan_full_tank_wallet_qrcode: my_pump='+my_pump+
		', enable_scanner='+enable_scanner);

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
						var getamount = $('#total-final-filled-fulltank-'+my_pump).text();
						var rawamount = parseFloat(getamount) + calc_rounding(getamount);
						var amount = parseFloat(rawamount).toFixed(2);
						$('#spay_order_amt_full_tank').text(amount);
						$("#wrong_key_modal .close").click();
						$("#sarawak_pay_full_fulltank .close").click();
						parse_full_tank_wallet(my_pump , scanned_str , amount);
						keys =[];
					}
				}
			}
		}, false);
	});
}
}


function parse_full_tank_wallet(my_pump, qr_code , amount) {

	var st = qr_code.substring(0, 2);
	//console.log("qr_code=" , qr_code);

	// Parse for wallet service provider
	switch(st) {
		case "96":	// Sarawak Pay
		$("#wallet_instruction_modal .close").click();
		$('#sarawak_pay_full_fulltank').modal('toggle');
		//console.log('Sarawak Pay');
		//if (!empty(qr_code)) {
			if (qr_code != null) {
			spay_create_full_tank_order(my_pump, qr_code , amount);
		}
		break;

		case "":
		$("#wallet_instruction_modal .close").click();
		$("#sarawak_pay_full_fulltank .close").click();
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

function enableInput() {
	  var eventList = ["keydown", "keyup", "keypress"];
		eventList.forEach(function(ev) {
	  	window.removeEventListener(ev, disableInput, false);
		});
}

function select_full_tank_wallet(my_pump) {

	var eventList = ["keydown", "keyup", "keypress"];
  eventList.forEach(function(ev) {
    window.addEventListener(ev, disableInput, false);
  });

function disableInput(e) {
  e.preventDefault();
}

	console.log("select_full_tank_wallet: my_pump="+my_pump);
	log2laravel('DEBUG', 'Payment/CHeckUp mode select__wallet function pump+no=' +
				JSON.stringify(my_pump));

	enable_scanner = true;
	ft_selected_pump = my_pump;
	/* Here you pop up the modal */
	display_full_tank_wallet_instruction(my_pump);

	/* Here you scan QR code */
	scan_full_tank_wallet_qrcode(my_pump, true);

	/* WARNING: Don't disturb these code below!!! */
	fulltank_payment['pump' + my_pump].dis_cash = "";
	fulltank_payment['pump' + my_pump].payment_type = "wallet";

}


function spay_create_full_tank_order(my_pump, qr_code , amount) {

	/*
	var curType = "RM";
	var notifyURL = "https://google.com";
	var merchantId = "M100004540";
	var detailURL = "https://youtube.com";
	var remark ="Special Promotional Pack";
	var transactionType = 1;
	var qr = "965280156454333771";
	*/

	var product_name = $('#product-fulltank-'+my_pump).text();
	var date = new Date();
  	var merOrderNo = date.getTime();
  	oldmerOrderNo = merOrderNo;

  	console.log("this =" ,amount , "and pump is = " , my_pump , "product_name" , product_name , "merOrderNo" , merOrderNo);


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
                $('#overwrite_full_tank').removeClass('d-none'); // use for response data
                $('#sc-full-fulltank').addClass('d-none'); // this shows Sending order amount
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
                    $('#overwrite_full_tank').html(html);
                }, 3000);
            }else{
                c = setTimeout(() => {
                    $('#overwrite_full_tank').html("Order received, awaiting confirmation from wallet user");
                    $('#inquirebtn-full-tank').removeClass('d-none'); // if ResStatus 0 show inquire btn
                }, 3000);
            }
        },
        error: function (response) {
            //$('#inquirebtn-full-tank').removeClass('d-none');
            //console.log("error");
            setTimeout(() => {
                $('#overwrite_full_tank').html("Some parameters is in error. Please scan again.");
            }, 3000);
        }
    });
    // Execute Ajax SPayController::SPayCreateOrder();
    // Pass in dummy values for the fields.
}
	function query_order_full_tank(){

		$('#sc-full-fulltank').removeClass('d-none');
		disableButton_full_tank();

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
				$('#overwrite_full_tank').removeClass('d-none');
				$('#scsc-full-fulltank').addClass('d-none');
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
					$('#overwrite_full_tank').html("Payment successful.<br>");
					$('#inquirebtn-full-tank').addClass('d-none');
					$('#sc-full-fulltank').addClass('d-none');
					enableButton_full_tank();
				}, 3000);

				// Payment successful, now process the fulltank receipt!!
				// BUT HOW TO GET THE SELECTED PUMP?
				process_receipt_ft(ft_selected_pump);

			}else{			// else Payment is not successful. inquire button enable
				b = setTimeout(() => {
					var html = ("Payment is not successful.<br>"+
					resmsg + 'Order received, awaiting confirmation from wallet user.');
					$('#overwrite_full_tank').html(html);
					enableButton_full_tank();
				}, 3000);

			}
		},
		error: function (response) { // if error Something went wrong inquire button enable
			enableButton_full_tank();
			//$('#inquirebtn-full-tank').removeClass('d-none');
			console.log("error");
			setTimeout(() => {
				$('#overwrite_full_tank').html("Order received, awaiting confirmation from wallet user.");
			}, 3000);
		}

	});
}

function disableButton_full_tank() {
	$('#sc-full-fulltank').addClass('d-none');
	var btn = document.getElementById('query_order_btn_full_tank');
	btn.disabled = true;
	btn.innerText = 'Inquire';

}

function enableButton_full_tank() {
	var btn = document.getElementById('query_order_btn_full_tank');
	btn.disabled = false;
	btn.innerText = 'Inquire';
}



function calc_rounding(val){
    var round_val=""
    last_digit =val[val.length-1]

    switch(last_digit){
        case "0":
            round_val = 0.00;
        break;
        case "1":
            round_val = -0.01;
        break;
        case "2":
            round_val = -0.02;
        break;
        case "3":
            round_val = 0.02;
		break;
        case "4":
            round_val = 0.01;
		break;
        case "5":
            round_val = 0.00;
        break;
        case "6":
            round_val = -0.01;
        break;
        case "7":
            round_val = -0.02;
        break;
        case "8":
            round_val = 0.02;
        break;
        case "9":
            round_val = 0.01;
        break;
		default:
    }

	return round_val;
}


</script>
<!-- FUELFULLTANK_WALLET END -->
<?php /**PATH /home/emmanuel/oceania/trunk/oceania/resources/views/fuel_fulltank/fulltank_wallet.blade.php ENDPATH**/ ?>