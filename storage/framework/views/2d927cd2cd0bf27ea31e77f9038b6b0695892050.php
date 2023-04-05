<!-- PUMPING LOOP BEGIN -->
<script>


function pump_authorize(pump_no, product_id, ogfuel_id) {

	$('#product-select-pump-'+pump_no+' > span > img').addClass('product-disable-offline');

	log2laravel('info', pump_no+': ***** BG1 pump_authorize: ' +
		pump_no + ', product_id=' + product_id + ', ogfuel_id=' + ogfuel_id);

	// Insurance against ogfuel_id being undefined
	if (ogfuel_id == undefined || ogfuel_id == null) {
		switch(product_id) {
			case 1:  // Diesel B20
			ogfuel_id=1;
			break;

			case 2:  // Ron95
			ogfuel_id=2;
			break;

			case 11: // Ron97
			ogfuel_id=3;
			break;

			case 12: // Diesel B7
			ogfuel_id=4;
			break;

			default: // Default to Ron95
			//product_id=2;
			//ogfuel_id=2;
		}

		log2laravel('info', pump_no+': ***** FIXED BG1 pump_authorize: ' +
			pump_no + ', product_id=' + product_id + ', ogfuel_id=' + ogfuel_id);
	}

	var nozzle = getNozzleNo(pump_no, product_id, true);

	// This should be ogfuel_id
	var fuel_grade_id = getFuelGradeId(ogfuel_id);

	log2laravel('info', pump_no+': BG1 pump_authorize: nozzle=' +
		nozzle[0].nozzle_no + ', fuel_grade_id=' + fuel_grade_id +
		', ogfuel_id='+ogfuel_id);

	if (nozzle && fuel_grade_id) {
		var type = "Amount";
		var dose = pumpData['pump'+pump_no].dose;
    	var ipaddr = "<?php echo e(env('PTS_IPADDR')); ?>";

		log2laravel('info', 'BG1 pump_no  = ' + pump_no);
		log2laravel('info', 'BG1 type     = ' + type);
		log2laravel('info', 'BG1 dose     = ' + dose);
		log2laravel('info', 'BG1 ipaddr   = ' + ipaddr);
		log2laravel('info', 'BG1 nozzle   = ' + nozzle[0].nozzle_no);

		$.ajax({
			url: '/pump-authorize-fuel-grade/' + pump_no + '/' + type +
				'/' + dose + '/' + ipaddr + '/' + nozzle[0].nozzle_no,
			type: "GET",
			dataType: "JSON",
			success: function (response) {

				log2laravel('info', pump_no +
					': ***** BG1 pump_authorize: SUCCESS from pump-authorize-fuel-grade*****');

				store_txid(response, dose);

				pumpData['pump'+pump_no].amount = "0.00";
				isClickPumpAuth = false;
				//console.log('authorized..')
				terminalSyncData(pump_no);

				/* Squidster: WARNING */
				/* When no FCC, this needs to be disabled */
				mainGetStatus();
			},
			error: function (response) {
				console.log(JSON.stringify(response));
				log2laravel('error', pump_no +
					': ***** pump_authorize: ERROR: ' +
					JSON.stringify(response));
			}
		});

	} else {
		log2laravel('error', pump_no +
			': ***** pump_authorize: ERROR: ' +
			'nozzle='+nozzle+', fuel_grade_id='+fuel_grade_id);
	}
}

/*
	OPT Terminal Sync Data
*/

function getOptTerminalSyncData(){
	console.log("GET OPT TERM DATA")
	$.post('<?php echo e(route('opt.term.data')); ?>').done( (res) => {
	 	if (!res){
	 		for(i = 1; i <= <?php echo e(env('MAX_PUMPS')); ?>; i++) {
	 			var overx = $('#opt-overlay-' + i);
	 			var overlay2 = $('#opt-overlay-sa-' + i);
	 			overx.attr('hidden', true);
	 			overlay2.attr('hidden', true);
	 		}
	 		return;
	 	}
	 	for(i = 1; i <= <?php echo e(env('MAX_PUMPS')); ?>; i++) {
	 		var overx = $('#opt-overlay-' + i);
	 		var overlay2 = $('#opt-overlay-sa-' + i);
	 		find_record = res.find( (d) => d.pump_no == i);
 		if(!find_record){
	 			if(overx) overx.attr('hidden', true);
	 			if (parseInt(selected_pump) == i){
	 				overlay2.attr('hidden', true);
	 			}
	 		}
	 		if(find_record) {
	 			pumpData[`pump${i}`].is_slave = true;
	 			overx.attr('hidden', false);
	 			if (parseInt(selected_pump) == i){
	 				overlay2.attr('hidden', false);
	 			}

		        console.log('getOptTerminalSyncData: pumpData=',pumpData)
	 			localStorage.setItem('pumpDataState', JSON.stringify(pumpData));
	 		}
	 	}
	});
}


/*Get opt terminal sync data */
function getTerminalSyncData(){

	$.post('<?php echo e(route('get_sync_data')); ?>').done( (res) => {
		if (!res){
			for(i = 1; i <= <?php echo e(env('MAX_PUMPS')); ?>; i++) {
				var overx = document.getElementById('overlay-' + i);
				var overlay2 = document.getElementById('overlayover');
				overx.hidden = true;
			}
			return;
		}

		terminal_id = <?php echo e($terminal->id); ?>;

		for(i = 1; i <= <?php echo e(env('MAX_PUMPS')); ?>; i++) {
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
					// overlay2.hidden = true;
				}
			}
			if (find_record) {
				if (find_record.master_terminal_id == terminal_id){
					pumpData[`pump${i}`].is_slave = false;

				}
				else{
					$(`#prd-overlay-${i}`).attr('hidden', 'hidden');
					console.log("getTerminalSyncData: pump " + i +
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
		        console.log('getTerminalSyncData: pumpData=',pumpData)
				// localStorage.setItem('pumpDataState', JSON.stringify(pumpData));
				// console.log(localStorage.get_magic_quotes_runtime)
			}
			// if (overlay2.hidden == false) {
			// 	detect_reserved_pump(i)
			// }
		}
	});
}

function mainGetStatus() {
	//console.log('**** FUEL: mainGetStatus ****');

	for (i = 1; i <= <?php echo e(env('MAX_PUMPS')); ?>; i++) {
		var ipaddr = "<?php echo e(env('PTS_IPADDR')); ?>";
    	//var ipaddr = get_hardwareip(i, false);
		$.ajax({
			url: '/pump-get-status/' + i + '/' + ipaddr,
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

						switch (packet.Type) {
							case 'PumpTotals':
								var volume = packet.Data.Volume;
								var amount = packet.Data.Amount;
								break;

							case 'PumpFillingStatus':
								var volume = packet.Data.Volume;
								var amount = packet.Data.Amount;
								var price = packet.Data.Price;
								var nozzle = packet.Data.Nozzle;
								break;

							case 'PumpOfflineStatus':
								break;

							case 'PumpIdleStatus':
								var LastTransaction = packet.Data.LastTransaction;
								var LastVolume  =	packet.Data.LastVolume;
								var LastPrice	=	packet.Data.LastPrice;
								var LastAmount	=	packet.Data.LastAmount;
								var NozzleUp	=	packet.Data.NozzleUp;
								break;

							default:
						}

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

						/* Squidster: This is for Nozzle Up detection */
						if (NozzleUp) {
							log2laravel('info','***** Pump: '+pump_no+
							', NozzleUp:'+NozzleUp+' *****');
						};

						$if_ft_authorized = localStorage.getItem('fulltank_authorized_pump_' + pump_no);
						if($if_ft_authorized == 'yes'){
							updatePumpStatusFullTank(packet, pump_no);
						}
						updatePumpStatus(packet, pump_no);
					}
				}
			}},
			error: function (resp) {
				console.log('ERROR: ' + JSON.stringify(resp));
			}
		});
	}
}


function updatePumpStatus(packet, my_pump) {
	pump_number_main = parseInt(selected_pump);
	var isNozzleDown = false;
	var type = packet.Type;

	if (type === 'PumpFillingStatus') {
        if(pumpData['pump'+my_pump].status == 'Idle'){
			//nozzle up
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
			//$('.cancel-pump-number-'+my_pump).addClass('poa-button-number-payment-cancel-disabled');
			$('.give-me-left-'+my_pump).removeClass('left-7');
			$('.cancel-pump-number-'+my_pump).removeClass('btn-size-70');

		}
		//UI variables only
		// Keep the grey cover up
		localStorage.setItem('scover_grey_' + my_pump, my_pump)

		// Remove the cancel button from grey cover
		$('#scover-grey-btn-' + my_pump).attr('hidden', true);

		// Remove cancel button Validator
		localStorage.removeItem('scover_grey_cancel_' + my_pump)
		$('#scover-grey-' + my_pump).css({"opacity": "0.8"});

		// Setting product price at FP pump widget
		//$('#fuel-product-price-'+my_pump).text(product_price.toFixed(2));
		$('#payment-status-'+my_pump).text('Paid');
		pumpData['pump' + my_pump].paymentStatus = "Paid";
		// console.log(product_price);

	} else if (type === 'PumpIdleStatus') {

        if(pumpData['pump'+my_pump].status == 'Delivering') {
			terminal_id = <?php echo e($terminal->id); ?>;

			// Squidster: Only run getPumpStatus if we are the master
			if (!pumpData['pump'+my_pump].is_slave) {
				
				log2laravel('info', 'REMATCH updatePumpStatus: my_pump=' +
					my_pump + ', terminal_id='+terminal_id);
				getPumpStatus(my_pump);
			}

			pumpData['pump'+my_pump].isNozzleUp = false;
			deleteTerminalSyncData(my_pump);
			//console.log('deleteSyncData => pump: ' , my_pump);
			isNozzleDown = true;
			localStorage.setItem("isNozzleDown"+my_pump, 'yes');
			pumpData['pump' + my_pump].paymentStatus = "Not Paid";
			 // Have to switch from FC to LocalStorage

			// more functions to update the UI after nozzle is down
			update_ui_on_nozzle_down(my_pump)

			$('payment-status-' + my_pump).text("Not Paid");
			$('payment-status-'+ my_pump).html("Not Paid");

			deleteTerminalSyncData(my_pump);
		}

		var pump_receipt = localStorage.getItem("pump_receipt_info");

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

		pump_idle_status_additions(my_pump)


	} else if (type === 'PumpOfflineStatus') {
        pumpData['pump'+my_pump].status = 'Offline';
		$('#pump-status-'+my_pump).text('Offline');

		$('#pump-button-'+my_pump).attr('class', '');
		$('#pump-button-'+my_pump).addClass('btn poa-button-pump-offline');

		$('#pump-status-main-'+my_pump).text("Offline");

		$('#product-select-pump-'+my_pump+' > span > img').
			addClass('product-disable-offline');

		pump_offline_status_additions(my_pump)

	} else if (type === 'PumpTotals') {
		log2laravel('info', 'updatePumpStatus: XXX PumpTotals packet=' +
			JSON.stringify(packet));

		// We need to test if the pump has been authorized, if yes we avoid
		cancel_nonauth(my_pump);

	} else {
		log2laravel('info', 'updatePumpStatus: XXX type=' + packet.Type +
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
	}

	if (pumpData['pump'+my_pump].is_slave == true)

		$(`#pump-auth-warn-${my_pump}`).css('display','block');
	else
		$(`#pump-auth-warn-${my_pump}`).css('display','none');

}


// We need to test if payment had been made, if not we Cancel
function cancel_nonauth(my_pump) {

	var pds = JSON.parse(localStorage.getItem('pumpDataState'));
	/*
	log2laravel('info', 'cancel_nonauth: XXX PumpTotals pds=' +
		JSON.stringify(pds));
	*/

	if (my_pump) {
		var pstat = pds['pump'+my_pump].paymentStatus;

		log2laravel('info', 'cancel_nonauth: XXX PumpTotals pstat=' +
			JSON.stringify(pstat));

		if (pstat != 'Paid') {

			pumpData['pump'+my_pump].status = 'Idle';
			$('#pump-status-'+my_pump).text('Idle');
			$('#pump-button-'+my_pump).attr('class', '');
			$('#pump-button-'+my_pump).addClass('btn poa-button-pump-idle');
			$('#pump-status-main-'+pump_number_main).text("Idle");

			cancelAuthorize(my_pump);
		}
	}
}


function payment_button(my_pump, enable_flag = true){
	if(enable_flag){
		$('#button-cash-payment' + my_pump).removeClass('poa-button-cash-disabled');
		$('#button-cash-payment' + my_pump).addClass('poa-button-cash');
		$('#button-card-payment' + my_pump).removeClass('poa-button-credit-card-disabled');
		$('#button-card-payment' + my_pump).addClass('poa-button-credit-card');
		$('#button-cash-card-payment').removeClass('poa-button-cash-card-disabled');
		$('#button-cash-card-payment').addClass('poa-button-cash-card');
		$(`#button-wallet${my_pump}`).removeClass('opos-button-wallet-disabled')
		$('#button-credit-ac' + my_pump).removeClass('opos-button-credit-disabled');
		$('#button-credit-ac' + my_pump).addClass('opos-button-credit-ac');
		//$(`#input-cash${my_pump}`).show();
	}
}


function preset_button(my_pump, enable_flag = true){
	if(enable_flag){
		$('#set-default-preset-button-' + my_pump + '-eighthund').removeClass('poa-button-cash-selected-disabled');
		$('#set-default-preset-button-' + my_pump + '-onefifty').removeClass('poa-button-cash-selected-disabled');
		$('#set-default-preset-button-' + my_pump + '-hund').removeClass('poa-button-cash-selected-disabled');
		$('#set-default-preset-button-' + my_pump + '-fifty').removeClass('poa-button-cash-selected-disabled');
		$('#set-default-preset-button-' + my_pump + '-twenty').removeClass('poa-button-cash-selected-disabled');
		$('#set-default-preset-button-' + my_pump + '-ten').removeClass('poa-button-cash-selected-disabled');
		$('#set-default-preset-button-' + my_pump + '-five').removeClass('poa-button-cash-selected-disabled');
		$('#set-default-preset-button-' + my_pump + '-two').removeClass('poa-button-cash-selected-disabled');
		$('#set-default-preset-button-' + my_pump + '-eighthund').removeClass('poa-button-number-disabled');
		$('#set-default-preset-button-' + my_pump + '-onefifty').removeClass('poa-button-number-disabled');
		$('#set-default-preset-button-' + my_pump + '-hund').removeClass('poa-button-number-disabled');
		$('#set-default-preset-button-' + my_pump + '-fifty').removeClass('poa-button-number-disabled');
		$('#set-default-preset-button-' + my_pump + '-twenty').removeClass('poa-button-number-disabled');
		$('#set-default-preset-button-' + my_pump + '-ten').removeClass('poa-button-number-disabled');
		$('#set-default-preset-button-' + my_pump + '-five').removeClass('poa-button-number-disabled');
		$('#set-default-preset-button-' + my_pump + '-two').removeClass('poa-button-number-disabled');
		$('#set-default-preset-button-' + my_pump + '-two').addClass('poa-button-number');
		$('#set-default-preset-button-' + my_pump + '-five').addClass('poa-button-number');
		$('#set-default-preset-button-' + my_pump + '-ten').addClass('poa-button-number');
		$('#set-default-preset-button-' + my_pump + '-twenty').addClass('poa-button-number');
		$('#set-default-preset-button-' + my_pump + '-fifty').addClass('poa-button-number');
		$('#set-default-preset-button-' + my_pump + '-hund').addClass('poa-button-number');
		$('#set-default-preset-button-' + my_pump + '-onefifty').addClass('poa-button-number');
		$('#set-default-preset-button-' + my_pump + '-eighthund').addClass('poa-button-number');
	}
}


function cancel_button(my_pump, enable_flag = true){
	if(!enable_flag){
		$('.cancel-pump-number-' + my_pump).addClass('poa-button-number-payment-cancel-disabled');
            $('.give-me-left-' + my_pump).removeClass('left-7');
            $('.cancel-pump-number-' + my_pump).removeClass('btn-size-70');
	}
}

$(document).ready(function(){
	var sInterval = null;
	sInterval = setInterval(function(){
		getTerminalSyncData();
		fulltankGetTerminalSyncData();
		getOptTerminalSyncData();

		/* Squidster: WARNING */
		/* When no FCC, his needs to be disabled- */
		mainGetStatus();
	}, 2000);
});
</script>
<!-- PUMPING LOOP END -->
<?php /**PATH /home/emmanuel/oceania/trunk/oceania/resources/views/landing/opos_pumpingloop.blade.php ENDPATH**/ ?>