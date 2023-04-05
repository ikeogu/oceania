<!-- H2 PRODUCT LOGIC BEGIN -->
<script>

var h2_pump_data = {};
var pumpData = {};

var access_code = "";
var usersystemid = "";
var username = "";
var keys = [];
var index = 0;
var pumpData = {};
var puno = localStorage.getItem('h2_pump_no');
var pri = localStorage.getItem('h2_pump_receipt_info');
var selected_pump = 0;

var payment_type = "";
var dis_cash = {};
var reset = {};
var receipt_id = 0;

for (i = 1; i <= {{ env('H2_MAX_PUMPS') }}; i++) {
    pumpData['pump' + i] = {
        status: 'Offline',
        volume: "0.00",
        amount: '0.00',
        price: '0.00',
        price_liter: '0.00',
        dose: '0.00',
        nozzle: '',
        product: '',
        product_id: '',
        product_thumbnail: '',
        h2_receipt_id: '',
        preset_type: 'amount',
        paymentStatus: 'Not Paid',
        payment_type: 'Prepaid',
        isNozzleUp: false,
        isAuth: false,
        is_slave: false
    };
}
// sessionStorage.removeItem("payment_type")
$(document).ready(function(){
  var sInterval = null;
	sInterval = setInterval(function(){
    log2laravel('Document Ready');
		getTerminalSyncData();
		getH2TerminalSyncData();

		/* Squidster: WARNING */
		/* When no FCC, his needs to be disabled */
		mainH2GetStatus();
	}, 2000);
});
/**
*=======================================================
**/
/*Get opt terminal sync data */
function getTerminalSyncData(){
console.log('***** H2: getTerminalSyncData()***** ')
log2laravel('***** H2: getTerminalSyncData()***** ');

	$.post('{{route('h2_get_sync_data')}}').done( (res) => {
    log2laravel('***** H2: h2_get_sync_data***** ',  res)
		if (!res){
			for(i = 1; i <= {{env('H2_MAX_PUMPS')}}; i++) {
				var overx = document.getElementById('overlay-' + i);
				var overlay2 = document.getElementById('overlayover');
				overx.hidden = true;
				overlay2.hidden = true;
			}
			return;
		}

		terminal_id = {{$terminal->id}};

		for(i = 1; i <= {{env('H2_MAX_PUMPS')}}; i++) {

			var overx = document.getElementById('overlay-' + i);
			var overlay2 = document.getElementById('overlayover-' + i);
			//var overlayxr = document.getElementById('overlayover-xr')
			find_record = res.find( (d) => d.pump_no == i);

			old_is_slave = pumpData[`pump${i}`].is_slave

			// Slave detection
			if(!find_record){

				pumpData[`pump${i}`].is_slave = false;
				//pumpData[`pump${i}`].paymentStatus = "Not Paid";
				if(overx) overx.hidden = true;

				/*if(overlay2.hidden){
					overlayxr.hidden = true;
				}*/
				if (parseInt(selected_pump) == i){
					overlay2.hidden = true;
				}
			}
			if (find_record) {
				if (find_record.master_terminal_id == terminal_id){
					pumpData[`pump${i}`].is_slave = false;
				}
				else{
					log2laravel("getTerminalSyncData: pump " + i +
						".is_slave => " + true)
					pumpData[`pump${i}`].is_slave = true;
				}
			}
			if(find_record && pumpData[`pump${i}`].is_slave == true) {
				overx.hidden = false;
				var wrapper_div = document.getElementById('cli');
				if (parseInt(selected_pump) == i){
					overlay2.hidden = false;
					wrapper_div.setAttribute('style','');
					wrapper_div.style.paddingLeft = '15px';
				}
				dose = find_record.dose;
				price = find_record.price;
				litre = find_record.litre;
				pump_no = find_record.pump_no;

				if (litre == 1) {
					pumpData[`pump${pump_no}`].preset_type = "Litre";
					if (old_is_slave == false) {
						$(`#total_amount-main-${pump_no}`).text( (price * dose).toFixed(2));
					}
					$('#total_volume-main-'+pump_no).text(dose.toFixed(2));


					display_litre_preset(true,pump_no)
				} else {
					pumpData[`pump${pump_no}`].preset_type = "amount";
					$(`#total_amount-main-${pump_no}`).text( dose.toFixed(2) )

					display_litre_preset(false,pump_no)
				}

				if (old_is_slave == false) {
					/*
					$("#amount-myr-"+pump_no).sevenSeg("destroy");
					$("#volume-liter-"+pump_no).sevenSeg("destroy");
					$("#price-meter-"+pump_no).sevenSeg("destroy");
					*/

					image = `/images/product/${find_record.psystemid}/thumb/${find_record.thumbnail_1}`;
					pumpData[`pump${pump_no}`].price 		= price.toFixed(2);
					pumpData[`pump${pump_no}`].price_liter	= price.toFixed(2);
					pumpData[`pump${pump_no}`].dose 		= dose.toFixed(2);
					pumpData[`pump${pump_no}`].product_thumbnail = image;
					pumpData[`pump${pump_no}`].product_id = find_record.product_id;
					pumpData[`pump${pump_no}`].product = find_record.pname;

				}
				pumpData[`pump${pump_no}`].paymentStatus = find_record.payment_status;
				localStorage.setItem('pumpDataState', JSON.stringify(pumpData));
				// console.log(localStorage.get_magic_quotes_runtime)
			}

		}
	});
}

function getH2TerminalSyncData(){
  log2laravel('******  getH2TerminalSyncData() *******');
  $.post('{{route('h2_get_sync_data')}}').done( (res) => {
    if (!res){
    for(i = 1; i <= {{env('H2_MAX_PUMPS')}}; i++) {
        var h2_overx = $('#h2-overlay-' + i);
        h2_overx.attr('hidden', true);
        var h2_oversa = $('#h2-overlay-sa-' + i);
        h2_oversa.attr('hidden', true);
      }
      return;
    }
    terminal_id = {{$terminal->id}};
    for(i = 1; i <= {{env('H2_MAX_PUMPS')}}; i++) {
      var h2_overx = $('#h2-overlay-' + i);
      var h2_over_sa = $('#h2-overlay-sa-' + i);
      var h2_overy = $('#hydrogen-overlay-' + i);
      find_record = res.find( (d) => d.pump_no == i);
      //old_is_slave = fulltank_pump_data[`pump${i}`].is_slave;
      if(!find_record){
        if (h2_pump_data[`pump${i}`] !== undefined) {
          h2_pump_data[`pump${i}`].is_slave = false;
          if(h2_overx) h2_overx.attr("hidden", true);
          h2_over_sa.attr('hidden', true);
        }
      }
      if (find_record) {
        if (find_record.master_terminal_id == terminal_id){
          h2_pump_data[`pump${i}`].is_slave = false;
        } else{
          h2_pump_data[`pump${i}`].is_slave = true;
        }
      }
      if(find_record && h2_pump_data[`pump${i}`].is_slave == true) {
        if (parseInt(selected_pump) == i){
          h2_over_sa.attr('hidden', false);
        }
        h2_overx.attr("hidden", false);
        $('#h2-overlay-' + i).attr("hidden", true);
        pump_no = find_record.pump_no;
        localStorage.setItem('h2dataState', JSON.stringify(h2_pump_data));
      } else if(find_record && h2_pump_data[`pump${i}`].is_slave == false){
        $('#h2-overlay-' + i).attr("hidden", false);
        h2_overx.attr("hidden", true);
        if (parseInt(selected_pump) == i){
          h2_over_sa.attr("hidden", false);
        }

        pump_no = find_record.pump_no;
        localStorage.setItem('h2dataState', JSON.stringify(h2_pump_data));
      }
    }
  });
}

function h2_authorize(my_pump) {
log2laravel('**** h2_authorize() *****');
  var type = "Hydrogen";
  var dose = "0.00";
  // $("#buffer-input-cash-h2" + my_pump).val("");
  // $("#input-cash-h2-" + my_pump).val("");
  // $('#total-final-filled-h2-' + my_pump).text("0.00");
  // $('#total-final-litre-h2-' + my_pump).text("0.00");
  // $('#payment-status-h2-' + my_pump).text("Not Paid");
  // $('#payment-status-h2-' + my_pump).html("Not Paid");

  $('#product-h2-' + my_pump).html("&nbsp;");

  payment_button_fulltank(my_pump, false);
  cancel_button_h2(my_pump, true);
  $('#h2-next-' + my_pump).addClass('poa-finish-button-disabled');
  $('#h2-next-' + my_pump).removeClass('h2-next-button');
  $('#h2-overlay-sa-' + my_pump).attr("hidden", false);
  h2_pump_data['pump' + my_pump].dose = "0.00";
  h2_pump_data['pump' + my_pump].amount = "0.00";
  h2_pump_data['pump' + my_pump].volume = "0.00";
  h2_pump_data['pump' + my_pump].price = "0.00";
  h2_pump_data['pump' + my_pump].product = "";
  h2_pump_data['pump' + my_pump].paymentStatus = "Not Paid";
  h2_pump_data['pump' + my_pump].isAuth = true;
  h2_payment['pump' + my_pump].dis_cash = "";
  $("#input-cash-h2-" + my_pump).text("");

    var ipaddr = "{{env('PTS_IPADDR')}}";
  //console.log(fulltank_authorized_data);
  $.ajax({
    url: '/pump-authorize-h2/' + my_pump + '/' + type +
      '/' + dose + '/' + ipaddr,
    type: "GET",
    dataType: "JSON",
    success: function (response) {

      var eventList = ["keydown", "keyup", "keypress"];
      eventList.forEach(function(ev) {
        window.addEventListener(ev, disableInput, false);
      });

      function disableInput(e) {
        e.preventDefault();
      }

      log2laravel('info', my_pump +
        ': ***** BG1 pump_authorize: SUCCESS from pump-authorize-h2*****');
      //console.log('full tank pump authorized..')
      var overx = document.getElementById('h2-overlay-' + my_pump);
      overx.hidden = false;
      localStorage.setItem('h2_authorized_pump_' + my_pump, 'yes');
      h2_store_txid(my_pump, response, dose);
      h2TerminalSyncData(my_pump);
      mainH2GetStatus();
    },
    error: function (response) {
      console.log(JSON.stringify(response));
      log2laravel('error', my_pump +
        ': ***** Hydrogen pump_authorize: ERROR: ' +
        JSON.stringify(response));
    }
  });
}

function h2TerminalSyncData(pump_no) {
  log2laravel('**** h2TerminalSyncData() ****');
  var isVisible = true
  if (isVisible == false)
    return;
  data = {};
  //console.log(pumpData[`pump${pump_no}`]);
  h2_terminal_sync = true;
  data['pump_no'] = pump_no;
  /*
  data['payment_status'] = "Not Paid";
  data['dose'] = 0.00;
  data['price'] = 0.00;
  data['receipt_id'] = fulltank_pump_data[`pump${pump_no}`].receipt_id;
  data['name'] = fulltank_pump_data[`pump${pump_no}`].product;
  data['product_thumbnail'] = fulltank_pump_data[`pump${pump_no}`].product_thumbnail;
  */
  $.ajax({
    url: "{{ route('sync-data-h2') }}",
    type: 'post',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    data: data,
    dataType: 'json',
    success: function(response) {
      console.log('PR H2 Sync DATA');
      log2laravel('PR H2 Sync DATA');
      h2_terminal_sync = false
    },
    error: function(e){
      console.log('PR ' + JSON.stringify(e));
      log2laravel(' ERROR: PR H2 Sync DATA' + JSON.stringify(e));
    }
  });
  /*$.post('{{ route('sync-data-ft') }}', data).
  done(() => ft_terminal_sync = false).
  fail((e) => console.log(e));*/
}


function updatePumpStatusH2(packet, my_pump) {
	pump_number_main = parseInt(selected_pump);
	var isNozzleDown = false;
	var type = packet.Type;

	if (type === 'PumpFillingStatus') {
        if(pumpData['pump'+my_pump].status == 'Idle'){
			//nozzle up
			/*
			$("#amount-myr-"  +my_pump).sevenSeg("destroy");
			$("#volume-liter-"+my_pump).sevenSeg("destroy");
			$("#price-meter-" +my_pump).sevenSeg("destroy");
			*/

			pumpData['pump'+my_pump].isNozzleUp = true;
		}

        pumpData['pump'+my_pump].status = 'Delivering';
		$('#pump-status-'+my_pump).text('Delivering');

		$('#pump-button-'+my_pump).attr('class', '');
		$('#pump-button-'+my_pump).addClass('btn poa-button-pump-delivering');
		$('#pump-status-main-'+pump_number_main).text("Delivering");

		var product_price = pumpData['pump'+my_pump].price;

		// $('.cancel-pump-number-'+my_pump).attr('disabled', 'disabled');

		if (!$('.cancel-pump-number-'+my_pump).hasClass('poa-button-number-payment-cancel-disabled')) {
			$('.cancel-pump-number-'+my_pump).addClass('poa-button-number-payment-cancel-disabled');
			$('.give-me-left-'+my_pump).removeClass('left-7');
			$('.cancel-pump-number-'+my_pump).removeClass('btn-size-70');
		}

		// Setting product price at FP pump widget
		//$('#fuel-product-price-'+my_pump).text(product_price.toFixed(2));
		$('#payment-status-'+my_pump).text('Paid');
		pumpData['pump' + my_pump].paymentStatus = "Paid";
		// console.log(product_price);

	} else if (type === 'PumpIdleStatus') {
        if(pumpData['pump'+my_pump].status == 'Delivering') {
			//nozzle down
			//enable_payment_btns();
			var type = getH2PumpStatus(my_pump);
			log2laravel('info', 'updatePumpStatusH2: type =' + type);

			if (type == 'PumpTotals') getH2PumpStatus(my_pump);

			pumpData['pump'+my_pump].isNozzleUp = false;
			deleteTerminalSyncData(my_pump);
			//console.log('deleteSyncData => pump: ' , my_pump);
			isNozzleDown = true;
			localStorage.setItem("isNozzleDown"+my_pump, 'yes');
			pumpData['pump' + my_pump].paymentStatus = "Not Paid";
			 // Have to switch from FC to LocalStorage

			$('payment-status-' + my_pump).text("Not Paid");
			$('payment-status-'+ my_pump).html("Not Paid");
			check_for_fulltak_ps = pumpData['pump' + my_pump].paymentStatus;

        	/*if(check_for_fulltank_ps != "Paid"){
            	console.log("Not Paid fulltank");
            	$('#fulltank-button-' + my_pump).removeClass('fulltank-button-disabled');
        	}*/
			/* ON nozzle down storage change - 2 things must happen. delete the mterm sync data and also enable buttons.*/
			deleteTerminalSyncData(my_pump);
		    /*$('.cancel-pump-number-' + my_pump).addClass('poa-button-number-payment-cancel-disabled');
            $('.give-me-left-' + my_pump).removeClass('left-7');
            $('.cancel-pump-number-' + my_pump).removeClass('btn-size-70');
            $('button.poa-button-number').removeClass('poa-button-cash-selected-disabled');
            $('.button-number-amount').removeClass('poa-button-number-disabled');
            $('.button-number-amount').removeClass('poa-button-cash-selected-disabled');
            $('.button-number-amount').addClass('poa-button-number');*/

			/*disable only the cancel of the nozzled down pump*/

			cancel_button(my_pump, false);

			/*disable only the cancel of the nozzled down pump*/


			/* remove the disabled class of the actual pump that is being nozzled down*/
			preset_button(my_pump);
			/* add poa button number of the actual pump that is being nozzled down*/

			/* enable the nozzled down pump's payment buttons*/
			payment_button(my_pump, true);
			/* enable the nozzled down pump's payment buttons*/
            // enable_payment_btns();
		}

		var pump_receipt = localStorage.getItem("pump_receipt_info");

		/*if (pump_receipt == my_pump) {
			$('#payment-status-'+my_pump).text('Paid');
			pumpData['pump' + my_pump].paymentStatus = "Paid";
		}*/

		// $('.cancel-pump-number-'+my_pump).removeAttr('disabled', 'disabled');

        pumpData['pump'+my_pump].status = 'Idle';
		$('#pump-status-'+my_pump).text('Idle');
		$('#pump-button-'+my_pump).attr('class', '');
		$('#pump-button-'+my_pump).addClass('btn poa-button-pump-idle');
		$('#pump-button-'+my_pump).removeClass('poa-button-pump-offline');
       	$('#pump-status-main-'+pump_number_main).text("Idle");

		var product_price = pumpData['pump'+my_pump].price;

		if (product_price) {
			//$('#fuel-product-price-'+my_pump).text(product_price.toFixed(2));
		}

	} else if (type === 'PumpOfflineStatus') {
        pumpData['pump'+my_pump].status = 'Offline';
		$('#pump-status-'+my_pump).text('Offline');

		$('#pump-button-'+my_pump).attr('class', '');
		$('#pump-button-'+my_pump).addClass('btn poa-button-pump-offline');

		$('#pump-status-main-'+my_pump).text("Offline");

		$('#product-select-pump-'+my_pump+' > span > img').addClass('product-disable-offline');

	} else if (type === 'PumpTotals') {
		log2laravel('info', 'updatePumpStatusH2: XXX PumpTotals packet=' +
			JSON.stringify(packet));

		// We need to test if the pump has been authorized, if yes we avoid
		cancel_nonauth(my_pump);

	} else {
		log2laravel('info', 'updatePumpStatusH2: XXX type=' + packet.Type +
			', packet=' + JSON.stringify(packet));

		// We need to test if the pump has been authorized, if yes we avoid
		cancel_nonauth(my_pump);

	}

	if(my_pump == pump_number_main){
		// confirm pump should not be delivering
		var getPaidPump = $('#paid-pump').val();
		var newPaymentisAuthorized = true;
		var newPaymentOnTriggerAuthorized = true;
		var pumpIsActive = false;

		if (getPaidPump.length > 0) {
			if (getPaidPump.includes(',')) {
				var splited_paidPumps = getPaidPump.split(',');

				if (splited_paidPumps.includes(my_pump.toString())) {
					if(newPaymentOnTriggerAuthorized == true) {
						newPaymentisAuthorized = false;
					}
					pumpIsActive = true;
				}
			} else {
				if (getPaidPump == my_pump) {
					if(newPaymentOnTriggerAuthorized == true) {
						newPaymentisAuthorized = false;
					}
					pumpIsActive = true;
				}
			}
		}
	}

	if (pumpData['pump'+my_pump].is_slave == true)
		$(`#pump-auth-warn-${my_pump}`).css('display','block');
	else
		$(`#pump-auth-warn-${my_pump}`).css('display','none');
}


    function voidPumpAndDeAuthorize(pump_no) {
        // void_receipt(pump_no);
        cancelAuthorize(pump_no);
        deleteTerminalSyncData(pump_no);

        reset['pump' + pump_no].reset = true;
        /*setTimeout(() => {
            clear_local_storage();
        }, 900);*/

        var deliveredPump = localStorage.getItem("pump_receipt_data_" + pump_no);
        if (deliveredPump != undefined && deliveredPump != '') {
            localStorage.removeItem("pump_receipt_data_" + pump_no);
            localStorage.removeItem("pump_receipt_info");
        }
        check_for_fulltank_ps = pumpData['pump' + pump_no].paymentStatus;
        var ft_authorized = localStorage.getItem('fulltank_authorized_pump_' + pump_no);
        if(check_for_fulltank_ps != "Paid" &&
            !fulltank_pump_data['pump' + pump_no].is_slave
                && (ft_authorized == null || ft_authorized == undefined || ft_authorized == '')){
        	$('#fulltank-button-' + pump_no).removeClass('fulltank-button-disabled');
        }
        enable_payment_btns();
        $('.button-number-amount').removeClass('poa-button-number-disabled');
        $('.button-number-amount').removeClass('poa-button-cash-selected-disabled');
        $('.button-number-amount').addClass('poa-button-number');
        $('#payment-status-' + pump_no).text('Not Paid');
        pumpData['pump' + pump_no].paymentStatus = "Not Paid";

        // FP Fuel is being updated here
        localStorage.setItem('pump_data_fuel_' + pump_no, '50.00');
        var pdata = localStorage.getItem('pump_data_fuel_' + pump_no);
        $('#total-fuel-pump-' + pump_no).text(pdata);

        $('#total-final-filled-' + pump_no).text('0.00');
        $('#total-final-litre-' + pump_no).text('0.00');


        log2laravel('info', pump_no +
            ': voidPumpAndDeAuthorize: #total-fuel-pump-' +
            pump_no + '=' + $('#total-fuel-pump-' + pump_no).text());


        var getAllPaidPump = $('#paid-pump').val();

        if (getAllPaidPump.length > 0) {
            if (getAllPaidPump.includes(',')) {
                var splited_paidPumps = getAllPaidPump.split(',');
                var dis_pump_id = pump_no.toString();

                if (splited_paidPumps.includes(dis_pump_id)) {
                    var removePump = splited_paidPumps.indexOf(dis_pump_id);
                    splited_paidPumps.splice(removePump, 1);

                    console.log(splited_paidPumps);

                    if (splited_paidPumps.length > 0) {
                        var writeToActivePump = splited_paidPumps.join(',');
                        $('#paid-pump').val(writeToActivePump);
                    } else {
                        $('#paid-pump').val('0');
                    }
                }
            } else {
                $('#paid-pump').val('0');
            }
        }

        var new_paid_pump = $('#paid-pump').val();

        log2laravel('Not_paid_reset Reset to -> Not Paid value->' + new_paid_pump);

        //if (!$('.cancel-pump-number-' + pump_no).hasClass('poa-button-number-payment-cancel-disabled')) {
            $('.cancel-pump-number-' + pump_no).addClass('poa-button-number-payment-cancel-disabled');
            $('.give-me-left-' + pump_no).removeClass('left-7');
            $('.cancel-pump-number-' + pump_no).removeClass('btn-size-70');
        //}

        log2laravel('void_pump_' + pump_no + ' Disable button after voidPump');
        // hideModal();
    }

    var deleteTerminalSyncData = debounce(function(pump_no) {
        console.log("deleteTerminalSyncData - H2");
        $.post('{{ route('h2_delete_sync_data') }}', {
            pump_no: pump_no
        }).
        fail((e) => {
          console.log("deleteTerminalSyncData - H2 - fails ", e)
          console.log(e)});
    }, 300);

    function debounce(func, wait, immediate) {
        var timeout;
        return function executedFunction() {
            var context = this;
            var args = arguments;

            var later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };

            var callNow = immediate && !timeout;

            clearTimeout(timeout);

            timeout = setTimeout(later, wait);

            if (callNow) func.apply(context, args);
        };
    };

        async function cancelAuthorize(pumpNo) {
    		//  After pressing Cancel, have to disable it
    		$('.cancel-pump-number-' + pumpNo).removeClass('poa-button-number-payment-cancel');
    		$('.cancel-pump-number-' + pumpNo).addClass('poa-button-number-payment-cancel-disabled');
            // Re-enable product buttons
            $('#product-select-pump-' + pumpNo + ' > span > img').removeClass('product-disable-offline');
            $('#product-select-pump-' + pumpNo + ' > span > img').addClass('cursor-pointer');
            $('#product-select-pump-' + pumpNo + ' > span > img').removeClass('noclick');


            var ipaddr = "{{ env('PTS_IPADDR') }}";
            $.ajax({
                // This is the real Emergency Stop
                url: "/h2-pump-cancel-authorize/" + pumpNo + '/' + ipaddr,
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    //console.log('KK ***** cancelAuthorize() *****');
                    //console.log('KK data:' + JSON.stringify(data));
                    $('#pump-stop-button-' + pumpNo).show();
                    $('#resume-stop-button-' + pumpNo).hide();
                    pumpData['pump' + pumpNo]['stop'] = true;
                    //$("#product-select-pump-"+pumpNo).css('display','none');
                },
                error: function(e) {
                    console.error('KK error:' + JSON.stringify(e));
                }
            });
        }


        function void_receipt(pump_no) {
            receipt_id = pumpData['pump' + pump_no].receipt_id;
            console.log("fromVoid: ", receipt_id)
            if (receipt_id == '') {
                receipt_id = localStorage.getItem("h2_pump_receipt_id_" + pump_no);
            }

            log2laravel('retrieved_receipt_id->', receipt_id);

            if (receipt_id == '') {
                log2laravel('info', pump_no +
                    ':  void_receipt: receipt_id IS BLANK! ABORTING!');
                console.log('no receipt_id');
                return;
            }
            $.ajax({
                url: "{{ route('fuel.voidReceipt') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'post',
                data: {
                    'receipt_id': receipt_id,
                },
                success: function(response) {
                    localStorage.removeItem('pump_data_fuel_' + pump_no);
                    localStorage.removeItem('pump_no');
                    // localStorage.removeItem('update-screen-e-landing');
                    localStorage.removeItem('pump_receipt_data_' + pump_no);
                    //window.location.reload();
                    //clear_local_storage();
                },
                error: function(e) {
                    console.log('VR ' + JSON.stringify(e));
                }
            });


        }


    function getH2PumpStatus(my_pump, insert_filled = true) {
        var ipaddr = get_hardwareip(my_pump, false);
        //console.log('my_pump='+my_pump+', ipaddr='+ipaddr);

        $.ajax({
            url: '/h2-pump-get-status/' + my_pump + '/' + ipaddr,
            type: "GET",
            dataType: "JSON",
            success: function(resp) {

                var packet_type = null;

                log2laravel('info', 'getH2PumpStatus 0: ' + JSON.stringify(resp));

                if ((resp != null) && (typeof resp != 'undefined')) {
                    resp = resp.data;

                    log2laravel('info', 'getH2PumpStatus 1: ' + JSON.stringify(resp));
                    log2laravel('info', 'getH2PumpStatus 1.1: ' + JSON.stringify(resp.response));


                    if ((typeof resp.response != 'undefined') &&
                        (resp.response != null)) {
                        var response = resp.response;

                        log2laravel('info', 'getH2PumpStatus 2: ' + JSON.stringify(response));
                        log2laravel('info', 'getH2PumpStatus 2.1: ' + JSON.stringify(response.Packets));
                        log2laravel('info', 'getH2PumpStatus 2.2: ' + JSON.stringify(response.Packets[0]));
                        log2laravel('info', 'getH2PumpStatus 2.3: ' + (typeof response.Packets));
                        log2laravel('info', 'getH2PumpStatus 2.4: ' + (typeof response.Packets[0]));
                        log2laravel('info', 'getH2PumpStatus 2.5: ' + JSON.stringify(response.Packets));
                        log2laravel('info', 'getH2PumpStatus 2.6: ' + JSON.stringify(response.Packets[0]));

                        if ((typeof response.Packets != 'undefined') &&
                            (response.Packets != null) &&
                            (typeof response.Packets[0] != 'undefined') &&
                            (response.Packets[0] != null)) {

                            log2laravel('info',
                                'getH2PumpStatus 3: ***** Before End Transaction detection *****');

                            var packet = response.Packets[0];
                            var pump_no = packet.Data.Pump;


                            switch (packet.Type) {
                                case 'PumpTotals':
                                    var volume = packet.Data.Volume;
                                    var amount = packet.Data.Amount;

                                    log2laravel('info', 'getH2PumpStatus 3.0: PumpTotals=' +
                                        JSON.stringify(packet));
                                    break;

                                case 'PumpFillingStatus':
                                    var volume = packet.Data.Volume;
                                    var price = packet.Data.Price;
                                    var amount = packet.Data.Amount;

                                    log2laravel('info', 'getH2PumpStatus 3.1: pump_no=' +
                                        pump_no + ', volume=' + volume +
                                        ', price=' + price + ', amount=' + amount);

                                    break;

                                case 'PumpOfflineStatus':
                                    break;

                                case 'PumpIdleStatus':
                                default:
                                    var LastTransaction = packet.Data.LastTransaction;
                                    var LastVolume = packet.Data.LastVolume;
                                    var LastPrice = packet.Data.LastPrice;
                                    var LastAmount = packet.Data.LastAmount;
                                    var LastNozzle = packet.Data.LastNozzle;
                                    var tx_id = authorizeData['pump' + my_pump].transactionid;

                                    log2laravel('info', 'getH2PumpStatus 3.2: ***** BEFORE ' +
                                        'Transaction end detection *****');

                                    log2laravel('info', 'getH2PumpStatus 3.3: tx_id=' + tx_id +
                                        ', LastTransaction=' + LastTransaction);

                                    log2laravel('info', 'getH2PumpStatus 3.4: LastAmount=' +
                                        LastAmount + ', LastVolume=' + LastVolume +
                                        ', LastPrice=' + LastPrice + ', LastNozzle=' + LastNozzle +
                                        ', LastTransaction=' + LastTransaction);

                                    log2laravel('info',
                                        'getH2PumpStatus 3.5: packet.Type=' + packet.Type);
                                    log2laravel('info',
                                        'getH2PumpStatus 3.6: tx_id=' + tx_id);
                                    log2laravel('info',
                                        'getH2PumpStatus 3.7: LastTransaction=' +
                                        LastTransaction);
                            }

                            // Test whether transaction has ended
                            if ((packet.Type == 'PumpIdleStatus') &&
                                (tx_id != "") &&
                                (tx_id != 0) &&
                                (typeof LastTransaction != "undefined") &&
                                (LastTransaction != 0) &&
                                (LastTransaction == tx_id)) {

                                pumpData['pump' + my_pump].amount = LastAmount;
                                pumpData['pump' + my_pump].volume = LastVolume;
                                pumpData['pump' + my_pump].price = LastPrice;
                                pumpData['pump' + my_pump].nozzle = LastNozzle;

                                log2laravel('info', my_pump +
                                    ': getH2PumpStatus 4.0 ***** DETECTED Transaction End *****');

                                log2laravel('info', my_pump +
                                    ': getH2PumpStatus 4.1: dose=' +
                                    pumpData['pump' + my_pump].dose);

                                log2laravel('info', my_pump +
                                    ': getH2PumpStatus 4.2: LastAmount=' +
                                    LastAmount + ', LastVolume=' + LastVolume +
                                    ', LastPrice=' + LastPrice + ', LastNozzle=' +
                                    LastNozzle + ', LastTransaction=' +
                                    LastTransaction + ' insert_filled=' + insert_filled);

                                /* if ($('button.poa-button-number').hasClass(
                                         'poa-button-cash-selected-disabled')) {
                                     $('button.poa-button-number').removeClass(
                                         'poa-button-cash-selected-disabled');
                                 }*/
                                /*disable only the cancel of the nozzled down pump*/

			                    cancel_button(my_pump, false);
                                /*disable only the cancel of the nozzled down pump*/
                                /* remove the disabled class of the actual pump that is being nozzled down*/
                                preset_button(my_pump);
                                /* add poa button number of the actual pump that is being nozzled down*/
                                /* enable the nozzled down pump's payment buttons*/
                                payment_button(my_pump, true);
                                /* enable the nozzled down pump's payment buttons*/
                                var deliveredPump = localStorage.getItem("h2_pump_receipt_data_" + my_pump);

                                if (deliveredPump != undefined && deliveredPump != '') {
                                    deliveredPump = JSON.parse(deliveredPump);
                                    dose = deliveredPump.dose;
                                    price = deliveredPump.price;
                                    // Update database filled and refund columns
                                    updateFilled(deliveredPump.receipt_id,
                                        LastAmount, dose - LastAmount);
                                    var newData = {
                                        "price": price,
                                        "dose": dose,
                                        "amount": parseFloat(LastAmount).toFixed(2),
                                        "receipt_id": pumpData['pump' + my_pump].receipt_id
                                    };
                                    localStorage.removeItem("h2_pump_receipt_data_" + my_pump);
                                    localStorage.setItem("h2_pump_receipt_data_" + my_pump,
                                        JSON.stringify(newData));
                                    localStorage.setItem("pump_data_fuel_" + my_pump,
                                        dose);
                                    localStorage.removeItem('reload_receipt_list')
                                    localStorage.setItem("reload_receipt_list", "yes");
                                }

                                if (insert_filled == true) {

                                    // Have to switch from FC to LocalStorage
                                    $('#total-final-filled-' + my_pump).text(LastAmount.toFixed(2));

                                    final_litre = parseFloat(LastAmount) / parseFloat(pumpData['pump' +
                                        my_pump].price);
                                    $('#total-final-litre-' + my_pump).text(final_litre.toFixed(2));


                                    // Re-enable product buttons
                                    $('#product-select-pump-' + my_pump + ' > span > img').removeClass(
                                        'product-disable-offline');
                                    $('#product-select-pump-' + my_pump + ' > span > img').addClass(
                                        'cursor-pointer');
                                    $('#product-select-pump-' + my_pump + ' > span > img').removeClass(
                                        'noclick');


                                    var getAllPaidPump = $('#paid-pump').val();

                                    if (getAllPaidPump.length > 0) {
                                        if (getAllPaidPump.includes(',')) {
                                            var splited_paidPumps = getAllPaidPump.split(',');
                                            var dis_pump_id = my_pump.toString();

                                            if (splited_paidPumps.includes(dis_pump_id)) {
                                                var removePump = splited_paidPumps.indexOf(dis_pump_id);
                                                splited_paidPumps.splice(removePump, 1);

                                                console.log(splited_paidPumps);

                                                if (splited_paidPumps.length) {
                                                    var writeToActivePump = splited_paidPumps.join(',');
                                                    $('#paid-pump').val(writeToActivePump);
                                                } else {
                                                    $('#paid-pump').val('0');
                                                }
                                            }
                                        } else {
                                            $('#paid-pump').val('0');
                                        }
                                    }
                                }

                            } else if ((packet.Type == 'PumpTotals')) {
                                log2laravel('info', selected_pump +
                                    ': getH2PumpStatus 5.1: packet=' +
                                    JSON.stringify(packet));

                            } else if ((packet.Type == 'PumpEndOfTransactionStatus')) {
                                log2laravel('info', selected_pump +
                                    ': getH2PumpStatus 5.2: packet=' +
                                    JSON.stringify(packet));

                            } else {
                                log2laravel('info', selected_pump +
                                    ': getH2PumpStatus 5.3: packet=' +
                                    JSON.stringify(packet));
                            }
                            packet_type = packet.Type;
                        }
                    }
                    return packet_type;
                }
            },
            error: function(resp) {
                console.log('ERROR: ' + JSON.stringify(resp));
                log2laravel('ERROR: ' + JSON.stringify(resp));
            }
        });
    }

const pumpHardwareIp = Object.values({!! json_encode($pump_hardware->toArray(), true) !!});

    function get_hardwareip(pumpNo, modal_display) {
        pumpNo = parseInt(pumpNo);
        find_pumpIp = pumpHardwareIp.find(({
            pump_no
        }) => pump_no === pumpNo);

        if (find_pumpIp == undefined) {
            if (modal_display == true) {
                messageModal("No IP defined for this pump");
            }
            return undefined;
        } else {
            //if IP found
        }

        return find_pumpIp.ipaddress;
    }

/**
 *====================================================================
*/

//
var isType = 0;
function presetInput(num){
    isType=1
    if(num==='0.0' || num==='0.00')
        isType=0

    var newnum = parseInt(num.replace(".", ""));
    txt=atm_money(newnum)
    $(".presetInput").val(txt)

    if(isType==1) {
        // alert("custom_kg_btn")
        $(".btnrecpt").removeClass("custom-preset-disable")
        $(".btnrecpt").removeAttr("disabled")
        $(".btnrecpt").addClass("presetmyr btn-success btn-size-70")
        $(".btnrecpt").addClass("btn-success")

    }else {
        $(".btnrecpt").addClass("custom-preset-disable")
        $(".btnrecpt").removeClass("presetmyr btn-size-70")
        $(".btnrecpt").removeClass("btn-success")
        // $(".btnrecpt").attr("disabled","disabled")
    }
}


function process_finish(){
    var active_pump =sessionStorage.getItem("selected_pump");

    $(".zero-btn").addClass("poa-button-number-payment-zero-disabled")
    $(".zero-btn").removeClass("poa-button-number-payment-zero")

    var description     = $("#table-PRODUCT-"+active_pump).html()
    var price           = $("#table-PRICE-"+active_pump).html()
    var qty             = $("#table-QTY-"+active_pump).html()
    var myr             = $("#table-MYR-"+active_pump).html()
    var item_amount     = $("#item-amount-calculated-"+active_pump).html()
    var sst             = $("#sst-val-calculated-"+active_pump).html()
    var rounding        = $("#rounding-val-calculated-"+active_pump).html()
    var total           = $("#grand-total-val-calculated-"+active_pump).html()
    var change          = $("#change-val-calculated-"+active_pump).html()
    var filled          = $("#total-final-filled-"+active_pump).html()
    var cash_received   = $("#input-cash"+active_pump).val()
    var total_fuel      = $("#total-fuel-pump-"+active_pump).html()


    var data = {
        "name":description,
        "price":price,
        "quantity":qty,
        "filled":filled,
        "qty":qty,
        "currency":myr,
        "item_amount":item_amount,
        "sst":sst,
        "cal_rounding":rounding,
        "total":total,
        "change_amount":change,
        "cash_received":cash_received,
        "product_id":sessionStorage.getItem("product_id"),
        "payment_type":	localStorage.getItem("payment_type"),
        "dose":total_fuel,
        "pump_no":active_pump
    }


    $.ajax({
        url: "{{ url('h2-receipt-list-store') }}",
        type: "POST",
        data:data,
        'headers': {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function(response) {
          deleteTerminalSyncData(data["pump_no"])
            console.log(response);

        },
        error: function(resp) {
          log2laravel('error', resp );
        }
    });



    // setTimeout(() => {
        clear()

        $(".enter-btn").addClass("poa-button-number-payment-zero-disabled")
        $(".enter-btn").removeClass("poa-button-number-payment-zero")


        $(".btnrecpt").addClass("custom-preset-disable")
        $(".btnrecpt").removeClass("presetmyr btn-size-70")
        $(".btnrecpt").removeClass("btn-success")
        $(".presetInput").attr("disabled","disabled")

        $("#table-PRICE-"+active_pump).html("")
        $("#grand-total-val-calculated-"+active_pump).html("0.00")
        $("#table-MYR-"+active_pump).html("0.00")
        $("#table-QTY-"+active_pump).html("")

        $("#table-PRODUCT-"+active_pump).html("")
        $("#rounding-val-calculated-"+active_pump).html("0.00")
        $("#sst-val-calculated-"+active_pump).html("0.00")
        $("#item-amount-calculated-"+active_pump).html("0.00")

        $("#input-cash"+active_pump).val("")
        // $("#input-cash"+active_pump).addClass("0.00")
        $("#input-cash"+active_pump).removeAttr("readonly")
        $("#input-cash"+active_pump).attr("disabled","disabled")
        $("#input-cash"+active_pump).attr("readonly","readonly")

        $(".poa-button-cash").addClass("poa-button-cash-disabled")
        $(".poa-button-credit-card").addClass("poa-button-credit-card-disabled")
        $(".opos-button-wallet").addClass("opos-button-wallet-disabled")

        $("#change-val-calculated-"+active_pump).html("0.00")


    // }, 3000);
}

function select_custom_preset(){


    var active_pump =sessionStorage.getItem("selected_pump");

    $(".enter-btn").addClass("poa-button-number-payment-zero-disabled")
    $(".enter-btn").removeClass("poa-button-number-payment-zero")


	// myr is coming from user input
    var myr = parseFloat($(".presetInput").val());
    $("#table-MYR-"+active_pump).html(myr.toFixed(2));
    $("#total-fuel-pump-"+active_pump).html(myr.toFixed(2));

	// myR is coming from the UI
    var myR = $("#table-MYR-"+active_pump).text();

	// price coming from UI
    var price = parseFloat($("#table-PRICE-"+active_pump).html());
	var qty = null;
	if (!isNaN(price)) {
		qty = (myR / price).toFixed(2);
	}

 	var item_amount = parseFloat(myR / (1 + ({{$terminal->tax_percent}}/100))).toFixed(2);
	if (item_amount == null) {
		item_amount = parseFloat(0).toFixed(2);
	}
    //var myR = $(".presetInput").val()
    var rounding = calc_rounding(myR)

    var sst = (myR - item_amount).toFixed(2)
    var total = (parseFloat(item_amount)+parseFloat(sst)+(rounding.rounding)).toFixed(2);

    $("#table-QTY-"+active_pump).html(qty);
    $("#sst-val-calculated-"+active_pump).html(sst);
    $("#item-amount-calculated-"+active_pump).html(item_amount);
    $("#grand-total-val-calculated-"+active_pump).html(total);


    // console.log(calc_rounding(sessionStorage.getItem("priceMYR")),"::::::")
    $("#rounding-val-calculated-"+active_pump).html(rounding.rounding.toFixed(2))
    // $("#grand-total-val-calculated-"+active_pump).html(parseFloat(calc_rounding(active_pump)).toFixed(2))
}


function calc_rounding(val){
    var roundValue=""
    lastDigit =val[val.length-1]

    switch(lastDigit){
        case "0":
            roundValue = 0.00;
        break;
        case "1":
            roundValue = -0.01;
        break;
        case "2":
            roundValue = -0.02;
        break;
        case "3":
            roundValue = 0.02;
		break;
        case "4":
            roundValue = 0.01;
		break;
        case "5":
            roundValue = 0.00;
        break;
        case "6":
            roundValue = -0.01;
        break;
        case "7":
            roundValue = -0.02;
        break;
        case "8":
            roundValue = 0.02;
        break;
        case "9":
            roundValue = 0.01;
        break;
		default:
    }

	return {"rounding":roundValue,
		"total":(parseFloat(val)+(roundValue)).toFixed(2),
		"op":""};
}


function clear(){
    var active_pump =sessionStorage.getItem("selected_pump");

    $(".presetInput").val("");

    $(".btnrecpt").addClass("custom-preset-disable");
	$(".btnrecpt").removeClass("presetmyr btn-size-70");

    // $("#grand-total-val-calculated-"+active_pump).text("")
    // $("#table-QTY-"+active_pump).html(qty)
}

function atm_money(num) {
	if (num.toString().length == 1) {
		return '0.0' + num.toString();
	} else if (num.toString().length == 2) {
		return '0.' + num.toString();
	} else if (num.toString().length == 3) {
		return num.toString()[0] + '.' + num.toString()[1] +
			num.toString()[2];
	} else if (num.toString().length >= 4) {
		return num.toString().slice(0, (num.toString().length - 2)) +
			'.' + num.toString()[(num.toString().length - 2)] +
			num.toString()[(num.toString().length - 1)];
	}
}

// alert(rounding())


function select_pump(selected_pump) {

    clear()
    localStorage.removeItem("is_value")
    $(".presetInput").removeAttr("disabled")

    $(".enter-btn").removeClass("poa-button-number-payment-zero-disabled")
    $(".enter-btn").removeClass("poa-button-cash")
    $(".enter-btn").addClass("poa-button-number-payment-zero-disabled")

    $("#input-cash"+selected_pump).removeAttr("readonly")
    $("#input-cash"+selected_pump).val("")

    // presetInput(0)
    sessionStorage.setItem("selected_pump",selected_pump)
    // alert("::"+select_pump)

    console.log("Selected Pump = "+selected_pump);

    $('#selected-pump-id').text(selected_pump);

    // console.log("This is Selected now =", $('#selected-pump-id').text());

    $("#pump-number").show();
    $("#active-pump").text(selected_pump);
    $(".pump-main-block").hide();
    $("#pump-main-block-"+selected_pump).show();

    $('.fuel-product-image').addClass('product-disable-offline noclick').removeClass('cursor-pointer');
    $('#product-select-pump-' + selected_pump + ' > span > img').removeClass('product-disable-offline');
    $('#product-select-pump-' + selected_pump + ' > span > img').addClass('cursor-pointer');
    $('#product-select-pump-' + selected_pump + ' > span > img').removeClass('noclick');

    $.ajax({
        url: "{{ route('h2-pump-info') }}",
        type: "POST",
        data: {
            selected_pump: selected_pump,
        },
        'headers': {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function(response) {
          h2TerminalSyncData(selected_pump)
          mainH2GetStatus();
            console.log(response);
            pump_data = response;
            setFirst(pump_data,selected_pump)
        },
        error: function(resp) {}
    });



}

function setFirst(pump_data,pump_no){

    // var product_id      = parseInt($("#product-first").html());

    selected_pump = parseInt($("#selected-pump-"+pump_no).val())
    var product_id   = parseInt($("#product-first-"+selected_pump).html());
    sessionStorage.setItem("product_id",product_id)
    $(".poa-button-cash").removeClass("poa-button-cash-disabled")
    $(".poa-button-credit-card").removeClass("poa-button-credit-card-disabled")
    $(".opos-button-wallet").removeClass("opos-button-wallet-disabled")



    $(".active-product").addClass("p-product")
    $(".active-product").addClass("active-product")
    $(".active-product").removeClass("active-product")


    $(".product-row-"+selected_pump).removeClass("p-product")
    $(".product-row-"+selected_pump).removeClass("active-product")
    $(".product-row-"+selected_pump).addClass("active-product")


    var price = parseFloat(pump_data[product_id]['display_price']);


    $("#fuel-product-price-"+pump_no).text(pump_data[product_id].display_price);
    $("#table-PRODUCT-"+pump_no).text(pump_data[product_id]['product_name']);
    $("#table-PRICE-"+pump_no).text(pump_data[product_id]['display_price']);

    var total_fuel_pump = $("#total-fuel-pump-"+pump_no).html();

    var myR= total_fuel_pump
    var qty = null;
    if (!isNaN(price)) {
        qty = (myR / price).toFixed(2);
    }

    var item_amount = parseFloat(myR / (1 + ({{$terminal->tax_percent}}/100))).toFixed(2);
    if (item_amount == null) {
        item_amount = parseFloat(0).toFixed(2);
    }
    //var myR = $(".presetInput").val()
    var rounding = calc_rounding(myR)

    var sst = (myR - item_amount).toFixed(2)

    var total = (parseFloat(item_amount)+parseFloat(sst)+(rounding.rounding)).toFixed(2);

    $("#table-QTY-"+pump_no).html(qty);
    $("#sst-val-calculated-"+pump_no).html(sst);
    $("#item-amount-calculated-"+pump_no).html(item_amount);
    $("#grand-total-val-calculated-"+pump_no).html(total);


    $("#table-MYR-"+pump_no).text(total_fuel_pump);

}

function selectProduct(pump_no,product_id,product_name,product_thumb) {

    // console.log(pump_data[product_id]);
    $(".poa-button-cash").removeClass("poa-button-cash-disabled")
    $(".poa-button-credit-card").removeClass("poa-button-credit-card-disabled")
    $(".opos-button-wallet").removeClass("opos-button-wallet-disabled")

    // $(".enter-btn").removeClass("poa-button-number-payment-zero-disabled")
    // $(".enter-btn").addClass("poa-button-number-payment-zero-disabled")

    $(".enter-btn").removeClass("poa-button-cash")
    $(".enter-btn").addClass("poa-button-cash-disabled")
    $(".enter-btn").addClass("poa-button-number-payment-zero-disabled")

    var total_fuel_pump = $("#total-fuel-pump-"+pump_no).html();


    sessionStorage.setItem("product_id",product_id)

    selected_pump = parseInt($("#selected-pump-"+pump_no).val())


    $(".active-product").addClass("p-product")
    $(".active-product").addClass("active-product")
    $(".active-product").removeClass("active-product")

    $(".product-item-"+product_id+pump_no).removeClass("p-product")
    $(".product-item-"+product_id+pump_no).removeClass("active-product")
    $(".product-item-"+product_id+pump_no).addClass("active-product")




    $("#fuel-product-price-"+pump_no).text(pump_data[product_id]['display_price']);
    $("#table-PRODUCT-"+pump_no).text(pump_data[product_id]['product_name']);
    $("#table-PRICE-"+pump_no).text(pump_data[product_id]['display_price']);

    $(".presetInput").removeAttr("disabled")

    var price = parseFloat(pump_data[product_id]['display_price']);

    var myR= total_fuel_pump
    var qty = null;
	if (!isNaN(price)) {
		qty = (myR / price).toFixed(2);
	}

 	var item_amount = parseFloat(myR / (1 + ({{$terminal->tax_percent}}/100))).toFixed(2);
	if (item_amount == null) {
		item_amount = parseFloat(0).toFixed(2);
	}
    //var myR = $(".presetInput").val()
    var rounding = calc_rounding(myR)

    var sst = (myR - item_amount).toFixed(2)

    var total = (parseFloat(item_amount)+parseFloat(sst)+(rounding.rounding)).toFixed(2);

    $("#table-QTY-"+pump_no).html(qty);
    $("#sst-val-calculated-"+pump_no).html(sst);
    $("#item-amount-calculated-"+pump_no).html(item_amount);
    $("#grand-total-val-calculated-"+pump_no).html(total);


    $("#table-MYR-"+pump_no).text(total_fuel_pump);



}



function select_credit_card(){
    // $("#payment-div-cash").addClass("input-div-0")
    // $(".input-div-0").css("min-height", "250px!important");

	localStorage.setItem("payment_type","card")

    // $("#payment-div-cash-card" + selected_pump).hide();
    cashReceived()

    $(".enter-btn").removeClass("poa-button-cash-disabled")
    $(".enter-btn").removeClass("poa-button-number-payment-zero-disabled")
    $(".enter-btn").removeClass("poa-button-cash")
    $(".enter-btn").addClass("poa-button-cash")


}

function cashReceived(){
    var selected_pump = sessionStorage.getItem("selected_pump")
    $("#input-cash0").hide();
    $("#input-cash"+selected_pump).hide();

    $(".record").removeClass("input-div-0")
    $(".record1").removeClass("input-div-0")
    $(".record").addClass("input-div-0")
    $(".record1").addClass("input-div-0")

}
function select_wallet_card(){
    $(".enter-btn").removeClass("poa-button-number-payment-zero-disabled")
    $(".enter-btn").addClass("poa-button-cash")

    cashReceived()

}


// <!-- CASH -->



function select_cash(){

    var pump_no =  sessionStorage.getItem("selected_pump")

    $("#input-cash0").show();
    $("#input-cash"+pump_no).show();

    localStorage.setItem("payment_type","cash")

    $("#input-cash"+pump_no).removeAttr("disabled")
    $("#input-cash"+pump_no).removeAttr("readonly")
    $("#input-cash"+pump_no).focus();

    $(".zero-btn").removeClass("poa-button-number-payment-zero-disabled")
    $(".zero-btn").addClass("poa-button-number-payment-zero")
    $(".enter-btn").removeClass("poa-button-cash")
    $(".enter-btn").addClass("poa-button-cash-disabled")
    $(".enter-btn").addClass("poa-button-number-payment-zero-disabled")
    // $(".enter-btn").addClass("poa-button-cash")

}



var isType = 0;
var is_value = ""
var realTime = ""
var greater = 0;


function cashInput(event){

    var active_pump =  sessionStorage.getItem("selected_pump")
    var num = event.value//$("#input-cash"+active_pump).val()
    // alert(num)

    var _cash= $("#cash-input-cash"+active_pump).val()

    var newnum = parseInt(num.replace(".", ""));

    txt=atm_money(newnum)
    $("#input-cash"+active_pump).val(txt)

        var grand_total = ($("#grand-total-val-calculated-"+active_pump).html());

        change          = (event.value-grand_total).toFixed(2);

        if(parseFloat(txt)>=parseFloat(grand_total))
        {
            $("#input-cash"+active_pump).attr("readonly","readonly")
        }

        $change = parseFloat(txt)-parseFloat(grand_total)

        // alert(txt+" : "+grand_total)

        // {

            $("#change-val-calculated-"+active_pump).html($change.toFixed(2))
        //     $("#cash-input-cash"+active_pump).val(txt)
        //     // $("#cash-input-cash"+active_pump).val(txt)
        //     // $("#input-cash"+active_pump).val()
        //     event.value = "";
        //         console.log("greater",$("#cash-input-cash"+active_pump).val())
        // }
        //else
        // $("#change-val-calculated-"+active_pump).html(change)
    // },100)

    if($change>=0)
    {
        // $(".enter-btn").removeClass("poa-button-number-payment-zero-disabled")
        // $(".enter-btn").addClass("poa-button-number-payment-zero")

        $(".enter-btn").addClass("poa-button-cash")
        $(".enter-btn").removeClass("poa-button-cash-disabled")
        $(".enter-btn").removeClass("poa-button-number-payment-zero-disabled")

    }

    if(num==="0.0"){
        $(".enter-btn").addClass("poa-button-number-payment-zero-disabled")

        $(".enter-btn").removeClass("poa-button-number-payment-zero")

        $("#change-val-calculated-"+active_pump).text("0.00")
        clearInterval(realTime)
    }
}


function set_cash(amount) {

    var payment_type = localStorage.getItem("payment_type")
    var active_pump =  sessionStorage.getItem("selected_pump")
	if (amount == "zero") {
		if (payment_type === "cash") {
            // alert("set_cash :"+payment_type)

			// $('.enter-btn').addClass('poa-button-number-payment-zero-disabled');
            // $(".enter-btn").addClass("poa-button-cash")
            // $(".enter-btn").removeClass("poa-button-cash-disabled")
            $(".enter-btn").removeClass("poa-button-cash")
            $(".enter-btn").addClass("poa-button-number-payment-zero-disabled")

            // alert("helow")
			// $('.enter-btn').addClass('poa-button-number-payment');

			// $(".payment-div-cash div").addClass("justify-content-center");
			// $("#payment-value").css("color", "#a0a0a0");

        	$(".cashInput").val("");

			// $("#payment-type-message").html("");
			$("#change-val-calculated-"+active_pump).html("0.00");
			// $('.numpad-enter-payment').removeClass('poa-button-number-payment-enter');
			// $('.numpad-enter-payment').addClass('poa-button-number-payment-enter-disabled');
		}
        else if (payment_type == "card") {
			dis_cash = "";
			$('.numpad-number-payment').removeClass('poa-button-number-payment-disabled');
			$('.numpad-number-payment').addClass('poa-button-number-payment');
			$("#payment-value-card").html("");
			$('.numpad-enter-payment').removeClass('poa-button-number-payment-enter');
			$('.numpad-enter-payment').addClass('poa-button-number-payment-enter-disabled');
		}

//		clear_all_discounts()
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

function mainH2GetStatus() {
	console.log('**** H2: mainH2GetStatus ****');

	for (i = 1; i <= {{env('H2_MAX_PUMPS')}}; i++) {
		var ipaddr = "{{env('PTS_IPADDR')}}";
    	//var ipaddr = get_hardwareip(i, false);
		$.ajax({
			url: '/h2-pump-get-status/' + i + '/' + ipaddr,
			type: "GET",
			dataType: "JSON",
			success: function (resp) {

			if (resp != null && typeof resp != 'undefined') {
				resp = resp.data;
				if (typeof resp.response != 'undefined' &&
					resp.response != null) {
					var response = resp.response;

					if (response.hasOwnProperty('Packets') &&
						typeof response.Packets[0] != 'undefined') {
						var packet = response.Packets[0];

						var pump_no = packet.Data.Pump;
						var volume = packet.Data.Volume;
						var price = packet.Data.Price;
						var amount = packet.Data.Amount;
						var nozzle = packet.Data.Nozzle;

						var LastTransaction = packet.Data.LastTransaction;
						var LastVolume  =	packet.Data.LastVolume;
						var LastPrice	=	packet.Data.LastPrice;
						var LastAmount	=	packet.Data.LastAmount;

						if(volume){
							pumpData['pump'+pump_no].volume = volume;
						}

						if (amount) {
							pumpData['pump'+pump_no].amount = amount;
						}

						if (price) {
							pumpData['pump'+pump_no].price = price;
						}

						if (nozzle) {
							pumpData['pump'+pump_no].nozzle = nozzle;
							//initFuelProduct(pump_no, nozzle)
						};
						$if_ft_authorized = localStorage.getItem('h2_authorized_pump_' + pump_no);
						if($if_ft_authorized == 'yes'){
							updatePumpStatusH2(packet, pump_no);
						}
						updatePumpStatusH2(packet, pump_no);
					}
				}
			}},
			error: function (resp) {
				console.log('ERROR: ' + JSON.stringify(resp));
			}
		});
	}
}


</script>
<!--  H2 PRODUCT LOGIC END -->
