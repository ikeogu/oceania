<!-- START OPOS Prepaid -->

<script>
// Fetch a pump's record in mtermsync, if any
function match_mtermsync(my_pump) {
	log2laravel('info', my_pump +
		': ***** BG1 match_mtermsync: my_pump=' + my_pump);
	$.ajax({
		url: '/get_sync_pumpdata',
		type: "POST",
		dataType: "JSON",
		data: { my_pump: my_pump },
		success: function(resp) {
		if (resp && resp[0]) {
			log2laravel('info', my_pump +
				': ***** BG1 match_mtermsync: 1. resp=' + JSON.stringify(resp));

			// Get the transactionid
			var tid = resp[0].transactionid;
			var ipaddr = "{{ env('PTS_IPADDR') }}";

			get_last_transaction(tid, my_pump);


		} else {
			// When we don't have any pumps in sync mode
			log2laravel('info', my_pump +
				': ***** BG1 match_mtermsync: 4. resp=' +
				JSON.stringify(resp));

			get_last_transaction(0, my_pump);

		}},
		error: function(resp) {
			console.log('ERROR: ' + JSON.stringify(resp));
			log2laravel('error', my_pump +
				': ***** BG1 match_mtermsync: resp=' + JSON.stringify(resp));
		}
	});
}


// Search and destroy: scover_grey_{my_pump}
function remove_grey_cover(my_pump) {
	log2laravel('info', 
		'***** BG1 remove_grey_cover: my_pump=' + my_pump);

	// Attempt to remove grey cover
	try {
		if (localStorage.getItem('scover_grey_' + my_pump)) {
			localStorage.removeItem('scover_grey_' + my_pump);

			log2laravel('info', my_pump +
				': ***** FIX remove_grey_cover: ' +
				'REMOVED GREY COVER: AFTER removeItem()');
		}

	} catch(ex) {
		// Nope! Either we can't or it does not exist!!
		console.error('ERROR: ' + JSON.stringify(ex));
		log2laravel('error', my_pump +
			': ***** BG1 remove_grey_cover: ex=' +
			JSON.stringify(ex));
	}
}


function get_last_transaction(tid, my_pump) {
	log2laravel('info', 
		'***** BG1 get_last_transaction: tid=' + tid +
		', my_pump=' + my_pump);

	var ipaddr = "{{ env('PTS_IPADDR') }}";

	if (tid > 0) {
		my_url = '/pump-transaction-information/' + my_pump + '/' +
			tid + '/' + ipaddr;
		
	} else {
		// tid == 0
		my_url = '/pump-transaction-information/' + my_pump + '/0/' +
			ipaddr;
	}

	$.ajax({
		url: my_url,
		type: "GET",
		dataType: "JSON",
		success: function(resp) {

			log2laravel('info', my_pump +
				': ***** BG1 get_last_transaction: 1. resp=' +
				JSON.stringify(resp));

			var pkt    = resp.data.response.Packets[0];
			var type   = pkt.Type;
			var data   = pkt.Data;
			var pump   = data.Pump;
			var state  = data.State;
			var trans  = data.Transaction;
			var nozzle = data.Nozzle;
			var volume = data.Volume;
			var amount = data.Amount;
			var price  = data.Price;

			if (state == 'Finished') {
				// Rematch transaction and fetch filled amount
				process_transaction(pump, amount, volume,
				price, nozzle, trans);

				log2laravel('info', my_pump +
					': ***** BG1 get_last_transaction: 2. state=' + state +
					', trans=' + trans);

				// Remove sync data
				delete_syncdata(trans, my_pump);

				// Remove grey cover if any
				remove_grey_cover(my_pump);
			}
		},
		error: function(resp) {
			console.log('ERROR: ' + JSON.stringify(resp));
		}
	});
}


function delete_syncdata(trans, my_pump) {

	log2laravel('info', my_pump +
		': ***** BG1 delete_syncdata: trans=' + trans);

	$.ajax({
		url: '/delete_sync_data_trans_id',
		type: "POST",
		dataType: "JSON",
		data: { transactionid: trans },
		success: function(resp) {

			log2laravel('info', my_pump +
				': ***** BG1 delete_syncdata: resp=' + JSON.stringify(resp));

		},
		error: function(resp) {
			console.log(resp);
			log2laravel('error', my_pump +
				': ***** BG1 delete_syncdata: resp=' + JSON.stringify(resp));
		}
	});
}

// This function provides reload processing:
// Block to update Fuel, Filled, Litre and Price upon reload
$(document).ready(function() {
	for (var i = 1; i <= {{ env('MAX_PUMPS') }}; i++) {
		var my_pump = i;
		var pump_has_been_selected = false;
		var pump_receipt = localStorage.getItem("pump_receipt_info");
		var deliveredPump = localStorage.getItem("pump_receipt_data_" + my_pump);

		if (pump_receipt != undefined &&
			pump_receipt != '' &&
			pump_has_been_selected === false) {

			if (pump_receipt == my_pump) {
				//pump_selected(my_pump, false, false);
				pump_has_been_selected = true;
			}
		}

		if (deliveredPump != undefined && deliveredPump != '') {
			pump_receipt = my_pump;

			log2laravel('info', my_pump +
				': ***** BG1 prepaid: 0. deliveredPump=' + deliveredPump);
		}

		if (deliveredPump != undefined &&
			deliveredPump != '' &&
			pump_receipt != '' &&
			pump_receipt != undefined &&
			pump_receipt > 0) {

			// We try match if we found any of our pump is stuck in sync
			match_mtermsync(my_pump);

			log2laravel('info', my_pump +
				': ***** BG1 prepaid: 1. deliveredPump=' + deliveredPump);

			deliveredPump = JSON.parse(deliveredPump);

			var product_price = deliveredPump.price;


			// FP Fuel is being updated here
			localStorage.setItem('pump_data_fuel_' + my_pump, deliveredPump.dose)
			var pdata = localStorage.getItem('pump_data_fuel_' + my_pump);
			$('#total-fuel-pump-' + my_pump).text(pdata);

			log2laravel('info', my_pump +
				': ***** BG1 prepaid: total-fuel-pump-' +
				my_pump + '=' + $('#total-fuel-pump-' + my_pump).text());

			var last_amt = deliveredPump.amount;

			log2laravel('info', my_pump +
				': ***** BG1 prepaid: last_amt=' + last_amt);

			var final_litre = parseFloat(last_amt) / parseFloat(deliveredPump.price);

			log2laravel('info', my_pump +
				': ***** BG1 prepaid: final_litre=' + final_litre.toFixed(2));

			if (final_litre == 'NaN') {
				final_litre = 0;
			}

			if (last_amt == undefined) {
				last_amt = 0.00;
			}

			$('#fuel-product-price-' + my_pump).text(product_price.toFixed(2));

			log2laravel('info', my_pump +
				': ***** BG1 prepaid: fuel-product-price-' +
				my_pump + '=' + $('#fuel-product-price-' + my_pump).text());


			var isNozzleDown = localStorage.getItem("isNozzleDown" + my_pump);

			if (isNozzleDown != undefined && isNozzleDown === 'yes') {
				$('#total-final-litre-' + my_pump).text(final_litre.toFixed(2));
				$('#total-final-filled-' + my_pump).text(last_amt);

				localStorage.setItem("pump_reset_data_" + my_pump, 'yes');
			}

			$('#payment-status-' + my_pump).text('Paid');
		}
	}
});

</script>
<!-- END OPOS Prepaid -->
