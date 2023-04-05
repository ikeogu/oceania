<script>
	var fulltank_payment = {};
	var isClickProcessEnterFT = false;
	var fulltank_pump_data = {};
	var fulltank_reset = {};
    var fulltank_authorized_data = {};
	var ft_terminal_sync = false;
	//var sp = 2;
	for (i = 1; i <= {{ env('MAX_PUMPS') }}; i++) {
	    fulltank_payment['pump' + i] = {
	        payment_type: '',
			dis_cash: ''
	    };
	}
	//full tank pump data
	for (i = 1; i <= {{ env('MAX_PUMPS') }}; i++) {
        fulltank_reset['pump' + i] = {
            status: '',
            reset: false,
        };
    }

    for (i = 1; i <= {{ env('MAX_PUMPS') }}; i++) {
        fulltank_pump_data['pump' + i] = {
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
            receipt_id: '',
            preset_type: 'amount',
            paymentStatus: 'Not Paid',
            payment_type: 'Postpaid',
            isNozzleUp: false,
            isAuth: false,
            is_slave: false
        };
    }

    for (i = 1; i <= {{ env('MAX_PUMPS') }}; i++) {

        fulltank_authorized_data['pump' + i] = {
            transactionid: '',
            volume: "0.00",
            amount: '0.00',
            price: '0.00',
            nozzle: '',
            product: '',
            product_thumbnail: '',
            paymentStatus: 'Not Paid'
        };
    }


	//full tank pump data
	//ATM MONEY
	@for ($i = 1; $i <= env('MAX_PUMPS'); $i++)
		ft_filter_price("#input-cash-fulltank-{{ $i }}","#buffer-input-cash-ft{{ $i }}");
		$("#input-cash-fulltank-{{ $i }}").on("keyup", (e) => {
	        fulltank_payment['pump'+{{$i}}].dis_cash =
			$('#buffer-input-cash-ft' + {{$i}}).val() != '' ? $('#buffer-input-cash-ft' + {{$i}}).val():0;
			ft_calculate_change({{$i}});
	        fulltank_enable_enter({{$i}});
		});
	@endfor

	//store transaction id for fulltank

	function fulltank_store_txid(sp, resp, dose) {
        if (resp != null && typeof resp != 'undefined') {
            resp = resp.data;
            if (typeof resp.response != 'undefined' &&
                resp.response != null) {
                var response = resp.response;
                if ((typeof response.Packets != 'undefined') &&
                    (response.Packets != null) &&
                    (typeof response.Packets[0] != 'undefined') &&
                    (response.Packets[0] != null)) {
                    var packet = response.Packets[0];
                    var my_pump = packet.Data.Pump;
                    var transactionid = parseInt(packet.Data.Transaction);

                    if (transactionid != '' &&
                        transactionid != null &&
                        transactionid > 0) {
                        fulltank_authorized_data['pump' + sp].transactionid = transactionid;
                        fulltank_authorized_data['pump' + sp].amount = dose;
                    }
                }
            }
        }
    }

	//store transaction id for fulltank


	function ft_filter_price(target_field, buffer_in) {
        $(target_field).off();
        $(target_field).on("keydown", function(event) {
            event.preventDefault()
            if (event.keyCode == 8) {
                $(buffer_in).val('')
                $(target_field).val('')
                return null
            }
            if (isNaN(event.key) ||
                $.inArray(event.keyCode, [13, 38, 40, 37, 39]) !== -1 ||
                event.keyCode == 13) {
                if ($(buffer_in).val() != '') {
                    $(target_field).val(ft_atm_money(parseInt($(buffer_in).val())))
                } else {
                    $(target_field).val('')
                }
                return null;
            }

            const input = event.key;
            old_val = $(buffer_in).val()
            if (old_val === '0.00') {
                $(buffer_in).val('')
                $(target_field).val('')
                old_val = ''
            }
            $(buffer_in).val('' + old_val + input)
            $(target_field).val(ft_atm_money(parseInt($(buffer_in).val())))
        });
	}


	function ft_atm_money(num) {
        if (num.toString().length == 1) {
            return '0.0' + num.toString()
        } else if (num.toString().length == 2) {
            return '0.' + num.toString()
        } else if (num.toString().length == 3) {
            return num.toString()[0] + '.' + num.toString()[1] +
                num.toString()[2];
        } else if (num.toString().length >= 4) {
            return num.toString().slice(0, (num.toString().length - 2)) +
                '.' + num.toString()[(num.toString().length - 2)] +
                num.toString()[(num.toString().length - 1)];
        }
	}


	function ft_calculate_change(sp) {
		//console.log('ft selected pump : ' + sp);
        dis_cash_ = (parseFloat(fulltank_payment['pump' + sp].dis_cash) / 100).toFixed(2);
		//console.log("dis cash for fulltank authorized pump: " + dis_cash_);
        var amount_total = fulltank_pump_data['pump' + sp].amount;
        amount_total = ((5 * Math.round((amount_total * 100) / 5)) / 100).toFixed(2);
        var change_amount = dis_cash_ - amount_total;

        if (change_amount >= 0) {
            $('#fulltank-finish-' + sp).removeClass('poa-finish-button-disabled');
            $('#fulltank-finish-' + sp).addClass('opos-topup-button');
        } else {
            $('#fulltank-finish-' + sp).removeClass('opos-topup-button');
            $('#fulltank-finish-' + sp).addClass('poa-finish-button-disabled');
        }

	}


	//ATM MONEY
	function fulltank_authorize(my_pump) {

		var type = "FullTank";
		var dose = "0.00";
		$("#buffer-input-cash-ft" + my_pump).val("");
		$("#input-cash-fulltank-" + my_pump).val("");
		$('#total-final-filled-fulltank-' + my_pump).text("0.00");
		$('#total-final-litre-fulltank-' + my_pump).text("0.00");
		$('#payment-status-fulltank-' + my_pump).text("Not Paid");
		$('#payment-status-fulltank-' + my_pump).html("Not Paid");

		$('#product-fulltank-' + my_pump).html("&nbsp;");

		payment_button_fulltank(my_pump, false);
		cancel_button_fulltank(my_pump, true);
		$('#fulltank-next-' + my_pump).addClass('poa-finish-button-disabled');
		$('#fulltank-next-' + my_pump).removeClass('ft-next-button');
		$('#ft-overlay-sa-' + my_pump).attr("hidden", false);
		fulltank_pump_data['pump' + my_pump].dose = "0.00";
		fulltank_pump_data['pump' + my_pump].amount = "0.00";
		fulltank_pump_data['pump' + my_pump].volume = "0.00";
		fulltank_pump_data['pump' + my_pump].price = "0.00";
		fulltank_pump_data['pump' + my_pump].product = "";
		fulltank_pump_data['pump' + my_pump].paymentStatus = "Not Paid";
		fulltank_pump_data['pump' + my_pump].isAuth = true;
		fulltank_payment['pump' + my_pump].dis_cash = "";
		$("#input-cash-fulltank-" + my_pump).text("");

    	var ipaddr = "{{env('PTS_IPADDR')}}";
		//console.log(fulltank_authorized_data);
		$.ajax({
			url: '/pump-authorize-fulltank/' + my_pump + '/' + type +
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
					': ***** BG1 pump_authorize: SUCCESS from pump-authorize-fulltank*****');
				//console.log('full tank pump authorized..')
				var overx = document.getElementById('fulltank-overlay-' + my_pump);
				overx.hidden = false;
				localStorage.setItem('fulltank_authorized_pump_' + my_pump, 'yes');
				fulltank_store_txid(my_pump, response, dose);
				fulltankTerminalSyncData(my_pump);
				mainGetStatus();
			},
			error: function (response) {
				console.log(JSON.stringify(response));
				log2laravel('error', my_pump +
					': ***** Full Tank pump_authorize: ERROR: ' +
					JSON.stringify(response));
			}
		});
	}


	function fulltank_display_filled(my_pump) {
	}
	function mainGetStatusFulltank() {
		for (i = 1; i <= {{env('MAX_PUMPS')}}; i++) {
			var ipaddr = "{{env('PTS_IPADDR')}}";
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
							var volume = packet.Data.Volume;
							var price = packet.Data.Price;
							var amount = packet.Data.Amount;
							var nozzle = packet.Data.Nozzle;

							var LastTransaction = packet.Data.LastTransaction;
							var LastVolume  =	packet.Data.LastVolume;
							var LastPrice	=	packet.Data.LastPrice;
							var LastAmount	=	packet.Data.LastAmount;

							if(volume){
								fulltank_pump_data['pump'+pump_no].volume = volume;
							}

							if (amount) {
								fulltank_pump_data['pump'+pump_no].amount = amount;
							}

							if (price) {
								fulltank_pump_data['pump'+pump_no].price = price;
							}

							if (nozzle) {
								fulltank_pump_data['pump'+pump_no].nozzle = nozzle;
								//initFuelProduct(pump_no, nozzle)
							};
							updatePumpStatusFullTank(packet, pump_no);
						}
					}
				}},
				error: function (resp) {
					console.log('ERROR: ' + JSON.stringify(resp));
				}
			});
		}
	}
	function fulltankGetTerminalSyncData(){
		$.post('{{route('ft_get_sync_data')}}').done( (res) => {
			if (!res){
			for(i = 1; i <= {{env('MAX_PUMPS')}}; i++) {
					var ft_overx = $('#ft-overlay-' + i);
					ft_overx.attr('hidden', true);
					var ft_oversa = $('#ft-overlay-sa-' + i);
					ft_oversa.attr('hidden', true);
				}
				return;
			}
			terminal_id = {{$terminal->id}};
			for(i = 1; i <= {{env('MAX_PUMPS')}}; i++) {
				var ft_overx = $('#ft-overlay-' + i);
				var ft_over_sa = $('#ft-overlay-sa-' + i);
				var ft_overy = $('#fulltank-overlay-' + i);
				find_record = res.find( (d) => d.pump_no == i);
				//old_is_slave = fulltank_pump_data[`pump${i}`].is_slave;
				if(!find_record){
					if (fulltank_pump_data[`pump${i}`] !== undefined) {
						fulltank_pump_data[`pump${i}`].is_slave = false;
						if(ft_overx) ft_overx.attr("hidden", true);
						ft_over_sa.attr('hidden', true);
					}
				}
				if (find_record) {
					if (find_record.master_terminal_id == terminal_id){
						fulltank_pump_data[`pump${i}`].is_slave = false;
					} else{
						fulltank_pump_data[`pump${i}`].is_slave = true;
					}
				}
				if(find_record && fulltank_pump_data[`pump${i}`].is_slave == true) {
					if (parseInt(selected_pump) == i){
						ft_over_sa.attr('hidden', false);
					}
					ft_overx.attr("hidden", false);
					$('#fulltank-overlay-' + i).attr("hidden", true);
					pump_no = find_record.pump_no;
					localStorage.setItem('fulltankdataState', JSON.stringify(fulltank_pump_data));
				} else if(find_record && fulltank_pump_data[`pump${i}`].is_slave == false){
					$('#fulltank-overlay-' + i).attr("hidden", false);
					ft_overx.attr("hidden", true);
					if (parseInt(selected_pump) == i){
						ft_over_sa.attr("hidden", false);
					}

					pump_no = find_record.pump_no;
					localStorage.setItem('fulltankdataState', JSON.stringify(fulltank_pump_data));
				}
			}
		});
	}
	function cancel_nonauth(my_pump) {
		var pds = JSON.parse(localStorage.getItem('fulltankPumpDataState'));
		if (my_pump) {
			var pstat = pds['pump'+my_pump].paymentStatus;

			log2laravel('info', 'cancel_nonauth: XXX PumpTotals pstat=' +
				JSON.stringify(pstat));

			if (pstat != 'Paid') {
				fulltank_pump_data['pump'+my_pump].status = 'Idle';
				$('#pump-status-'+my_pump).text('Idle');
				$('#pump-button-'+my_pump).attr('class', '');
				$('#pump-button-'+my_pump).addClass('btn poa-button-pump-idle');
				$('#pump-status-main-'+pump_number_main).text("Idle");

				cancelAuthorizeFulltank(my_pump);
			}
		}
	}
	function cancelAuthorizeFulltank(pumpNo){
		var ipaddr = "{{ env('PTS_IPADDR') }}";
		$.ajax({
			url: "/pump-cancel-authorize/" + pumpNo + '/' + ipaddr,
			type: "GET",
			dataType: "JSON",
			success: function(data) {
				console.log('KK ***** cancelAuthorizeFulltank() *****');
				console.log('KK data:' + JSON.stringify(data));
				$('#pump-stop-button-' + pumpNo).show();
				$('#resume-stop-button-' + pumpNo).hide();
				fulltank_pump_data['pump' + pumpNo]['stop'] = true;
				//$("#product-select-pump-"+pumpNo).css('display','none');
			},
			error: function(e) {
				console.error('KK error:' + JSON.stringify(e));
			}
		});
	}
	var ftPumpStateInterval;
	function getPumpStatusFullTank(my_pump, insert_filled = true) {
		var ipaddr = get_hardwareip(my_pump, false);
		//console.log('my_pump='+my_pump+', ipaddr='+ipaddr);
		$.ajax({
		url: '/pump-get-status/' + my_pump + '/' + ipaddr,
		type: "GET",
		dataType: "JSON",
		success: function(resp) {
			var packet_type = null;
			if ((resp != null) && (typeof resp != 'undefined')) {
				resp = resp.data;
				if ((typeof resp.response != 'undefined') &&
					(resp.response != null)) {
					var response = resp.response;
					if ((typeof response.Packets != 'undefined') &&
						(response.Packets != null) &&
						(typeof response.Packets[0] != 'undefined') &&
						(response.Packets[0] != null)) {

						log2laravel('info',
							'getPumpStatusFullTank 3: ***** FT : Before End Transaction detection *****');

						var packet = response.Packets[0];
						var pump_no = packet.Data.Pump;

						switch (packet.Type) {
							case 'PumpTotals':
								var volume = packet.Data.Volume;
								var amount = packet.Data.Amount;
								log2laravel('info', 'FT : getPumpStatusFUllLTank 3.0: PumpTotals=' +
									JSON.stringify(packet));
								break;

							case 'PumpFillingStatus':
								var volume = packet.Data.Volume;
								var price = packet.Data.Price;
								var amount = packet.Data.Amount;
								log2laravel('info', 'FT : getPumpStatusFUllLTank 3.1: pump_no=' +
									pump_no + ', volume=' + volume +
									', price=' + price + ', amount=' + amount);

								break;

							case 'PumpOfflineStatus':
								break;

							case 'PumpIdleStatus':
							default:
								var last_transaction = packet.Data.LastTransaction;
								var last_volume = packet.Data.LastVolume;
								var last_price = packet.Data.LastPrice;
								var last_amount = packet.Data.LastAmount;
								var last_nozzle = packet.Data.LastNozzle;
								var tx_id = fulltank_authorized_data['pump' + my_pump].transactionid;
						}

						// Test whether transaction has ended
						if ((packet.Type == 'PumpIdleStatus') &&
                                (tx_id != "") &&
                                (tx_id != 0) &&
                                (typeof last_transaction != "undefined") &&
                                (last_transaction != 0) &&
                                (last_transaction == tx_id)) {
							fulltank_pump_data['pump' + my_pump].amount = last_amount;
							fulltank_pump_data['pump' + my_pump].volume = last_volume;
							fulltank_pump_data['pump' + my_pump].price = last_price;
							fulltank_pump_data['pump' + my_pump].nozzle = last_nozzle;
							//var last_amount_ft = packet.Data.LastAmount;
							var final_litre_ft = parseFloat(packet.Data.LastAmount) / parseFloat(packet.Data.LastPrice);
							/*$('#total-final-filled-fulltank-' + my_pump).text(last_amount.toFixed(2));
							$('#total-final-litre-fulltank-' + my_pump).text(final_litre_ft.toFixed(2));
							console.log("FT - getPumpStatusFulltank: type = ", type);
							cancel_button_fulltank(my_pump, false);		//disable cancel button
							payment_button_fulltank(my_pump, true); //enable payment buttons
							var last_product = get_product_fulltank(my_pump, last_nozzle);
							$('#total-final-filled-fulltank-' + my_pump).text(last_amount.toFixed(2));
							final_litre = parseFloat(last_amount) / parseFloat(fulltank_pump_data['pump' +
								my_pump].price);
							$('#total-final-litre-fulltank-' + my_pump).text(final_litre.toFixed(2));
						    console.log("FT - getPumpStatusFullTank : packet=" + JSON.stringify(packet));*/
						} else if ((packet.Type == 'PumpTotals')) {
							log2laravel('info', selected_pump +
								': getPumpStatusFUllLTank 5.1: packet=' +
								JSON.stringify(packet));

						} else if ((packet.Type == 'PumpEndOfTransactionStatus')) {
							log2laravel('info', selected_pump +
								': getPumpStatusFUllLTank 5.2: packet=' +
								JSON.stringify(packet));

						} else {
							log2laravel('info', selected_pump +
								': getPumpStatusFUllLTank 5.3: packet=' +
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
		}
		});
	}
	//set local storage data
	function fulltank_set_lt() {
		// console.log('called set lt');
        ftRestoreOldState();
        ftPumpStateInterval = setInterval(() => {
            localStorage.setItem('fulltankPumpDataState', JSON.stringify(fulltank_pump_data));
            localStorage.setItem('fulltankReset', JSON.stringify(fulltank_reset));
            localStorage.setItem('fulltank_authorized_data', JSON.stringify(fulltank_authorized_data));
        }, 250);
        isVisible = true;
    }


	function ftRestoreOldState(){
		//console.log("FT - OLDSTATE => init");
		if (localStorage.getItem('fulltankPumpDataState') != undefined) {
            var pd = JSON.parse(localStorage.getItem('fulltankPumpDataState'));
            fulltank_pump_data = JSON.parse(localStorage.getItem('fulltankPumpDataState'));
        }
        for (i = 1; i <= {{ env('MAX_PUMPS') }}; i++) {
            var old_unfinished_tx = JSON.parse(localStorage.getItem('fulltank_receipt_data_' + i));
			if(fulltank_pump_data['pump' + i] !== undefined){
				if(fulltank_pump_data['pump' + i].is_slave){
					$('#ft-overlay-' + i).attr('hidden', false);
				}
			}

			if(localStorage.getItem('fulltank_authorized_pump_' + i) == 'yes' && !fulltank_pump_data['pump' + i].is_slave){
				var fullTankOverlay = $('#fulltank-overlay-' + i);
				fullTankOverlay.attr("hidden", false);
			}
			if(fulltank_pump_data['pump' + i].isNozzleUp){
				payment_button_fulltank(i, false); //disable payment button disable
				cancel_button_fulltank(i, false); //disable cancel button
				$('#product-fulltank-' + i).text(fulltank_pump_data['pump' + i].product);
				//console.log(parseFloat(fulltank_pump_data['pump' + i].price).toFixed(2));
				$('#fuel-product-price-fulltank-' + i).text(parseFloat(fulltank_pump_data['pump' + i].price).toFixed(2));
				$('#fulltank-next-' + i).addClass('poa-finish-button-disabled');
				$('#fulltank-next-' + i).removeClass('ft-next-button');
			}

			var is_nozzle_down_ft = localStorage.getItem('isNozzleDownFt' + i);
			if(!fulltank_pump_data['pump' + i].isNozzleUp && fulltank_pump_data['pump' + i].isAuth){
				payment_button_fulltank(i, false); //disable payment button disable
				cancel_button_fulltank(i, true); //enable cancel button
			}
			//console.log('is Nozzle down: ', is_nozzle_down_ft);
			if(is_nozzle_down_ft == 'yes' && fulltank_pump_data['pump' + i].paymentStatus == 'Not Paid'){
				payment_button_fulltank(i, true); //enable payment button disable
				cancel_button_fulltank(i, false); //disable cancel button
				$('#fulltank-next-' + i).removeClass('poa-finish-button-disabled');
				$('#fulltank-next-' + i).addClass('ft-next-button');
				$('#total-final-filled-fulltank-' + i).text(parseFloat(fulltank_pump_data['pump' + i].amount).toFixed(2));
				$('#total-final-litre-fulltank-' + i).text(parseFloat(fulltank_pump_data['pump' + i].volume).toFixed(2));
				$('#payment-status-fulltank-' + i).text("Not Paid");
				$('#payment-status-fulltank-' + i).html("Not Paid");
				$('#product-fulltank-' + i).text(fulltank_pump_data['pump' + i].product);
				$('#fuel-product-price-fulltank-' + i).text(parseFloat(fulltank_pump_data['pump' + i].price).toFixed(2));
				$('#fulltank-next-' + i).addClass('poa-finish-button-disabled');
				$('#fulltank-next-' + i).removeClass('ft-next-button');
			}
            if(old_unfinished_tx != null || old_unfinished_tx != undefined){
                fulltank_pump_data['pump' + i].paymentStatus = "Paid";
                fulltank_pump_data['pump' + i].dose = old_unfinished_tx.dose;
				fulltank_pump_data['pump' + i].amount = old_unfinished_tx.amount;

				payment_button_fulltank(i, false); //disable payment button disable
				cancel_button_fulltank(i, false); //disable cancel button
				$('#fulltank-next-' + i).removeClass('poa-finish-button-disabled');
				$('#fulltank-next-' + i).addClass('ft-next-button');
				$('#total-final-filled-fulltank-' + i).text(fulltank_pump_data['pump' + i].amount);
				$('#total-final-litre-fulltank-' + i).text(parseFloat(fulltank_pump_data['pump' + i].volume).toFixed(2));
				$('#payment-status-fulltank-' + i).text("Paid");
				$('#payment-status-fulltank-' + i).html("Paid");
				$('#product-fulltank-' + i).text(fulltank_pump_data['pump' + i].product);


            }

        }
        localStorage.setItem('fulltankPumpDataState', JSON.stringify(fulltank_pump_data));

        if (localStorage.getItem('fulltankReset') != undefined)
            fulltank_reset = JSON.parse(localStorage.getItem('fulltankReset'));

        if (localStorage.getItem('fulltank_authorized_data') != undefined)
            fulltank_authorized_data = JSON.parse(localStorage.getItem('fulltank_authorized_data'));
	}
	//set local storage data

	//Cancel Button Disable / Enable and Payment Buttons Enable / Disable
	function cancel_button_fulltank(my_pump, enable_flag=false){
		if(!enable_flag){
			$('#cancel-pump-fulltank-' + my_pump).addClass('poa-button-number-payment-cancel-disabled');
    	        $('.give-me-left-' + my_pump).removeClass('left-7');
    	        $('#cancel-pump-fulltank-' + my_pump).removeClass('btn-size-70');
		}else{
			$('#cancel-pump-fulltank-' + my_pump).removeClass('poa-button-number-payment-cancel-disabled');
		}
	}


	function payment_button_fulltank(my_pump, enable_flag = true){
		if(enable_flag){
			$('#fulltank-cash-' + my_pump).removeClass('poa-button-cash-disabled');
			$('#fulltank-cash-' + my_pump).addClass('poa-button-cash');
			$('#fulltank-creditcard-' + my_pump).removeClass('poa-button-credit-card-disabled');
			$('#fulltank-creditcard-' + my_pump).addClass('poa-button-credit-card');
			$('#fulltank-wallet-' + my_pump).removeClass('opos-button-wallet-disabled');
			$('#fulltank-creditac-' + my_pump).removeClass('opos-button-credit-disabled');
			$('#fulltank-creditac-' + my_pump).addClass('opos-button-credit-ac');
			$(`#fulltank-input-cash${my_pump}`).show();
			log2laravel('DEBUG', 'Payment/CHeckUp mode ckeckUp=Payment mode Function Pump_no=' +
				JSON.stringify(my_pump));
		}else{
			$('#fulltank-cash-' + my_pump).addClass('poa-button-cash-disabled');
			$('#fulltank-cash-' + my_pump).removeClass('poa-button-cash');
			$('#fulltank-creditcard-' + my_pump).addClass('poa-button-credit-card-disabled');
			$('#fulltank-creditcard-' + my_pump).removeClass('poa-button-credit-card');
			$('#fulltank-wallet-' + my_pump).addClass('opos-button-wallet-disabled');
			$('#fulltank-creditac-' + my_pump).addClass('opos-button-credit-disabled');
			$('#fulltank-creditac-' + my_pump).removeClass('opos-button-credit-ac');
			$(`#fulltank-input-cash${my_pump}`).show();
			log2laravel('DEBUG', 'Payment/CHeckUp mode ckeckUp=Authorization mode Function Pump_no=' +
				JSON.stringify(my_pump));
		}
	}


	function get_product_fulltank(my_pump, nozzle){
		var return_data;
		$.ajax({
    	        url: "/pump-product-by-nozzle/" + my_pump + '/' + nozzle,
    	        type: "GET",
    	        dataType: "JSON",
    	        success: function(response) {
    	            var product = response.product;
    	            var productid = response.pid;
    	            var thumb = response.thumbnail;
    	            var price = response.price;
					$(`#product-fulltank-` + my_pump).text(product);
					$(`#fuel-product-price-fulltank-` + my_pump).text(price.toFixed(2));
    	            return_data = response;
				    //console.log(return_data);
    	            fulltank_pump_data['pump' + my_pump].product = product;
    	            fulltank_pump_data['pump' + my_pump].product_id = productid;
    	            fulltank_pump_data['pump' + my_pump].product_thumbnail = thumb;
    	            fulltank_pump_data['pump' + my_pump].price = price;

				},
				error: function(response) {
    	            console.log(JSON.stringify(response));
    	        }
    	    });
		return return_data;
	}


	function updatePumpStatusFullTank(packet, my_pump) {
		pump_number_main = parseInt(selected_pump);
		var isNozzleDownFt = false;
		var type = packet.Type;
		if (type === 'PumpFillingStatus') {
    	    if(fulltank_pump_data['pump'+my_pump].status == 'Idle'){
				fulltank_pump_data['pump'+my_pump].isNozzleUp = true;
				fulltank_pump_data['pump' + my_pump].isAuth = false;
				//cancel_button_fulltank(my_pump, true);
			}
    	    fulltank_pump_data['pump'+my_pump].status = 'Delivering';
			var last_product = get_product_fulltank(my_pump, packet.Data.Nozzle);
			cancel_button_fulltank(my_pump); //disable cancel button
			payment_button_fulltank(my_pump, false); //disable payment button
			$('#pump-status-'+my_pump).text('Delivering');
			$('#pump-button-'+my_pump).attr('class', '');
			$('#pump-button-'+my_pump).addClass('btn poa-button-pump-delivering');
			$('#pump-status-main-'+pump_number_main).text("Delivering");
			var product_price = fulltank_pump_data['pump'+my_pump].price;
		} else if (type === 'PumpIdleStatus') {
    	    if(fulltank_pump_data['pump'+my_pump].status == 'Delivering') {
				var type = getPumpStatusFullTank(my_pump);
				log2laravel('info', 'FT - updatePumpStatusFulltank: type =' + type);
				if (type == 'PumpTotals') getPumpStatusFullTank(my_pump);
				fulltank_pump_data['pump'+my_pump].isNozzleUp = false;
				fulltank_pump_data['pump' + my_pump].isAuth = false;
				//deleteTerminalSyncData(my_pump);
				isNozzleDownFt = true;
				localStorage.setItem("isNozzleDownFt"+my_pump, 'yes');
				//deleteTerminalSyncData(my_pump);
				var last_amount_ft = packet.Data.LastAmount;
				var final_litre_ft = parseFloat(packet.Data.LastAmount) / parseFloat(packet.Data.LastPrice);
				$('#total-final-filled-fulltank-' + my_pump).text(last_amount_ft.toFixed(2));
				$('#total-final-litre-fulltank-' + my_pump).text(final_litre_ft.toFixed(2));
				fulltank_pump_data['pump' + my_pump].dose = last_amount_ft;
				fulltank_pump_data['pump' + my_pump].amount = last_amount_ft;
				fulltank_pump_data['pump' + my_pump].volume = final_litre_ft;
				//console.log("FT - updatePumpStatusFulltank: type = " + type + " : prev status = " + fulltank_pump_data['pump' + my_pump].status);
				cancel_button_fulltank(my_pump, false);		//disable cancel button
				payment_button_fulltank(my_pump, true); //enable payment buttons
			}
			//cancel_button_fulltank(my_pump, false);
			//payment_button_fulltank(my_pump, true);
    	    fulltank_pump_data['pump'+my_pump].status = 'Idle';
			/*cancel_button_fulltank(my_pump, false);
			payment_button_fulltank(my_pump, true);*/
			$('#pump-status-'+my_pump).text('Idle');
			$('#pump-button-'+my_pump).attr('class', '');
			$('#pump-button-'+my_pump).addClass('btn poa-button-pump-idle');
			$('#pump-button-'+my_pump).removeClass('poa-button-pump-offline');
			// $('#fulltank-button-'+my_pump).removeClass('fulltank-button-disabled');
    	   	$('#pump-status-main-'+pump_number_main).text("Idle");
			var product_price = fulltank_pump_data['pump'+my_pump].price;
		} else if (type === 'PumpOfflineStatus') {
    	    fulltank_pump_data['pump'+my_pump].status = 'Offline';
			$('#pump-status-'+my_pump).text('Offline');
			$('#pump-button-'+my_pump).attr('class', '');
			$('#pump-button-'+my_pump).addClass('btn poa-button-pump-offline');
			$('#pump-status-main-'+my_pump).text("Offline");
			$('#product-select-pump-'+my_pump+' > span > img').addClass('product-disable-offline');
		} else if (type === 'PumpTotals') {
			log2laravel('info', 'updatePumpStatus: XXX PumpTotals packet=' +
				JSON.stringify(packet));
			cancel_nonauth(my_pump);
		} else {
			log2laravel('info', 'updatePumpStatus: XXX type=' + packet.Type +
				', packet=' + JSON.stringify(packet));
			cancel_nonauth(my_pump);
		}
	}


	function fullTankCancel(my_pump){
		cancelAuthorizeFulltank(my_pump);

		// Old debounce trigger
		//fulltankDeleteTerminalSyncData(my_pump);

		// New debounce trigger
		const fulltankDeleteTerminalSyncData = debounce(deteTermSyncData(my_pump),300);

    	fulltank_reset['pump' + my_pump].reset = true;
		fulltank_pump_data['pump' + my_pump].isAuth = false;
		localStorage.setItem('fulltankPumpDataState', JSON.stringify(fulltank_pump_data));
		localStorage.removeItem('fulltank_authorized_pump_' + my_pump);
		//console.log("***********Full tank local storage entry deleted***********");
		var overx = $('#fulltank-overlay-' + my_pump);
		overx.attr("hidden", true);
		$('#ft-overlay-sa-' + my_pump).attr("hidden", true);
	}


	function nozzleUp(i){
		log2laravel('DEBUG', 'Payment/CHeckUp mode ckeckUp=NozzleUp Function' +
				JSON.stringify(i));
		fulltank_pump_data['pump'+i].isNozzleUp = true;
		fulltank_pump_data['pump'+i].status == 'Idle';
		cancel_button_fulltank(i);
		payment_button_fulltank(i, false);
		fuel_product = get_product_fulltank(i, 2);

	}

	function nozzleDown(i){
		log2laravel('DEBUG', 'Payment/CHeckUp mode ckeckUp=NozzleDown Function' +
				JSON.stringify(i));
		fulltank_pump_data['pump'+i].isNozzleUp = false;
		fulltank_pump_data['pump'+i].amount = '42.34';
		fulltank_pump_data['pump'+i].volume = '12.1212';
		$('#total-final-filled-fulltank-' + i).text('42.34');
		$('#total-final-litre-fulltank-' + i).text('12.12');
		localStorage.setItem('isNozzleDownFt' + i, 'yes');
		payment_button_fulltank(i, true);
		cancel_button_fulltank(i);


		//$(`#product-fulltank-` + i).text(fuel_product.product);
		//console.log(fuel_product);
	}
	fulltank_set_lt();

	$(document).ready(function(){
		for (i = 1; i <= {{ env('MAX_PUMPS') }}; i++) {
			if(fulltank_pump_data['pump' + i].status == 'idle'){
				$('#fulltank-button-'+i).removeClass('fulltank-button-disabled');
			}
			/*if(localStorage.getItem('fulltank_authorized_pump_' + i) == 'yes'){
				var fullTankOverlay = document.getElementById('fulltank-overlay-' + i);
				fullTankOverlay.hidden = false;
			}*/
		}
		var sInterval = null;
	});



	function fulltank_enable_enter(sp) {
        if (fulltank_payment['pump' + sp].payment_type == "cash") {
            payed_in_cash = (parseFloat(fulltank_payment['pump' + sp].dis_cash) / 100).toFixed(2);
            var amount_total = fulltank_pump_data['pump' + sp].amount;
            amount_total = ((5 * Math.round((amount_total * 100) / 5)) / 100).toFixed(2);
            if (payed_in_cash >= parseFloat(amount_total)) {
                if (fulltank_pump_data['pump' + sp].product) {
                    $('#fulltank-finish-' + sp).removeClass('poa-finish-button-disabled');
                    $('#fulltank-finish-' + sp).addClass('opos-topup-button');
                    $(`#input-cash-fulltank-${sp}`).attr('disabled', true);
                }
            }
        } else if (fulltank_payment['pump' + sp].payment_type == "card" ||
            fulltank_payment['pump' + sp].payment_type == "wallet" ||
            fulltank_payment['pump' + sp].payment_type == "creditac") {
            if (fulltank_pump_data['pump' + sp].product) {
                $('#fulltank-finish-' + sp).addClass('opos-topup-button');
		        $('#fulltank-finish-' + sp).removeClass('poa-finish-button-disabled');
               // $('#fulltank-finish-' + sp).addClass('poa-button-number-payment-enter');
    	    }
    	}
	}

	function ft_select_wallet(sp) {
		if (sp != 0) {
			$.ajax({
			method: "post",
			url: "{{ route('creditaccount.listMerchantActive') }}",
			}).done((data) => {
				let dataList = data.data;
				console.log(data.data);

				$("#call").html("");
				for (let i = 0; i < dataList.length; i++) {
					$("#call").append('<div onclick="selectac(' + dataList[i]["company_id"] + ')" class="col-md-12 pl-3 productselect" style="scrollbar-width: thin;cursor:pointer;line-height:1.2;margin:5px;font-size:20px;padding-top:0;text-align: left;">' + dataList[i]["name_company"] + '</div>');
				}
				$("#cal").modal("show");
			}).fail((data) => {
				console.log("data", data)
			});
			/* Here you pop up the modal */
			pop_up_wallet_instruction();
			/* Here you scan QR code */
			scan_wallet_qrcode();
			/* Here you parse which wallet has been scanned */
			$('#fulltank-cash-' + sp).removeClass('selected_preset_button');
			$('#fulltank-finish-' + sp).removeClass('poa-finish-button-disabled');
			$('#fulltank-finish-' + sp).addClass('opos-topup-button');
			$(`#input-cash-fulltank-${sp}`).val('');
			$(`#input-cash-fulltank-${sp}`).hide();
			fulltank_payment['pump' + sp].dis_cash = "";
			fulltank_payment['pump' + sp].payment_type = "wallet";
			fulltank_enable_enter(sp)
		}
	}


	function ft_next(sp){
		fulltank_reset['pump' + sp].reset = true;
		fulltank_pump_data['pump' + sp].dose = "0.00";
		fulltank_pump_data['pump' + sp].amount = "0.00";
		fulltank_pump_data['pump' + sp].volume = "0.00";
		fulltank_pump_data['pump' + sp].price = "0.00";
		fulltank_pump_data['pump' + sp].product = "";
		fulltank_pump_data['pump' + sp].product_id = "";
		fulltank_pump_data['pump' + sp].receipt_id = "";

		fulltank_pump_data['pump' + sp].paymentStatus = "Not Paid";
		localStorage.setItem('fulltankPumpDataState', JSON.stringify(fulltank_pump_data));
		$('#total-final-filled-fulltank-' + i).text('0.00');
		$('#total-final-litre-fulltank-' + i).text('0.00');
		$(`#product-fulltank-` + i).html("&nbsp;");
		$('#fuel-product-price-fulltank-' + i).text('0.00');
		$('#payment-status-fulltank-' + i).text("Not Paid");
		$('#payment-status-fulltank-' + i).html('Not Paid');
		var x = $('#total-final-filled-fulltank-' + i).text();
		//console.log("total final filled : ", x);
		var overx = document.getElementById('fulltank-overlay-' + sp);
		overx.hidden = true;
		localStorage.removeItem('fulltank_authorized_pump_' + sp);
		fulltank_pump_data['pump' + sp].payment_status = "Not Paid";
		localStorage.removeItem("fulltank_receipt_info");
		localStorage.removeItem("fulltank_receipt_data_" + sp);
		localStorage.removeItem("fulltank_receipt_id_" + sp);
		localStorage.removeItem('isNozzleDownFt' + sp);

		// Old debounce trigger
		//fulltankDeleteTerminalSyncData(sp);

		// New debounce trigger
		const fulltankDeleteTerminalSyncData = debounce(deteTermSyncData(sp),300);

		//window.location.reload();
	}


	function ft_select_credit_card(sp) {
		if (sp != 0) {
			$('#fulltank-cash-' + sp).removeClass('selected_preset_button');
			$('#fulltank-finish-' + sp).removeClass('poa-finish-button-disabled');
			$('#fulltank-finish-' + sp).addClass('opos-topup-button');
			$(`#input-cash-fulltank-${sp}`).hide();
			rev_cash = parseFloat(fulltank_pump_data['pump' + sp].dose)
			$(`#input-cash-fulltank-${sp}`).val('');
			fulltank_payment['pump' + sp].dis_cash = "";
			fulltank_payment['pump' + sp].payment_type = "card";
			//$('#fulltank-finish-' + sp).removeClass('poa-button-number-payment-enter');
			//$('#fulltank-finish-' + sp).addClass('poa-button-number-payment-enter-disabled');

			fulltank_enable_enter(sp);
		}
	}


	function ft_select_cash(sp) {
    	if (sp != 0) {
			$('#fulltank-cash-' + sp).addClass('selected_preset_button');
			$('#fulltank-finish-' + sp).removeClass('opos-topup-button');
			$('#fulltank-finish-' + sp).addClass('poa-finish-button-disabled');
			$('#input-cash-fulltank-' + sp).show();
			$(`#input-cash-fulltank-${sp}`).removeAttr('disabled');
			fulltank_payment['pump' + sp].payment_type = "cash";
			fulltank_payment['pump' + sp].dis_cash = "";
			//console.log('select_cash for pump: ' + sp);
			$(`#input-cash-fulltank-${sp}`).val('');
			fulltank_payment['pump' + sp].dis_cash = "";
			$("#buffer-input-cash-ft" + sp).val('');
			$('#fulltank-finish-' + sp).removeClass('opos-topup-button');
			$('#fulltank-finish-' + sp).addClass('poa-finish-button-disabled');
			$('#input-cash-fulltank-' + sp).focus();
			$('#input-cash-fulltank-' + sp).click();
		}
	}


	function ft_select_credit_ac(sp) {
			if (sp != 0) {
				$.ajax({
				method: "post",
				url: "{{ route('creditaccount.listMerchantActive') }}",
			}).done((data) => {
				let dataList = data.data;
				console.log(data.data);

				$("#call").html("");
				for (let i = 0; i < dataList.length; i++) {
					if(dataList[i]['status'] == "active"){
						$("#call").append('<div onclick="selectac(' + dataList[i]["company_id"] + ')" class="col-md-12 pl-3 productselect" style="scrollbar-width: thin;cursor:pointer;line-height:1.2;margin:5px;font-size:20px;padding-top:0;text-align: left;">' + dataList[i]["name_company"] + '</div>');
					}
				}
				$("#cal").modal({
					backdrop: 'static',
					keyboard: false
				});
			}).fail((data) => {
				console.log("data", data)
			});

    	    $('#fulltank-cash-' + sp).removeClass('selected_preset_button');
    	    $('#fulltank-finish-' + sp).removeClass('poa-finish-button-disabled');
    	    $('#fulltank-finish-' + sp).addClass('opos-topup-button');
    	    $(`#input-cash-fulltank-${sp}`).val('');
    	    $(`#input-cash-fulltank-${sp}`).hide();
    	    fulltank_payment['pump' + sp].dis_cash = "";
    	    fulltank_payment['pump' + sp].payment_type = "creditac";
    	    fulltank_enable_enter(sp)
		}
	}


	function process_receipt_ft(sp){
		console.log('process_receipt('+sp+')');
		if (isClickProcessEnterFT == true)
            return
        isClickProcessEnterFT = true;
		fulltank_pump_data['pump' + sp].paymentStatus = "Paid";
		cancel_button_fulltank(sp); //disable cancel button
		payment_button_fulltank(sp, false); //disable payment button

		$('#fulltank-finish-' + sp).removeClass('opos-topup-button');
		$('#fulltank-finish-' + sp).addClass('poa-finish-button-disabled');

		dis_cash_ = (parseFloat(fulltank_payment['pump' + sp].dis_cash) / 100).toFixed(2);
		//console.log("Dis cash : ", dis_cash_);
		var amount_total1 = fulltank_pump_data['pump' + sp].amount;
		var dose = amount_total1;
        amount_total1 = ((5 * Math.round((amount_total1 * 100) / 5)) / 100).toFixed(2);
		var change_amount = dis_cash_ - amount_total1;

		//var filled = parseFloat(fulltank_pump_data['pump' + sp].amount);

		fulltank_reset['pump' + sp].reset = true;

		var filled = parseFloat(fulltank_pump_data['pump' + sp].amount);
		var qty = parseFloat(fulltank_pump_data['pump' + sp].volume);
		var price = parseFloat(fulltank_pump_data['pump' + sp].price);
		var payment_type = fulltank_payment['pump' + sp].payment_type;
		var creditcard_no = "";
		var cash_received = 0.00;
		var product_id = fulltank_pump_data['pump' + sp].product_id;
		var product = fulltank_pump_data['pump' + sp].product;

		if (payment_type == "card") {
			creditcard_no =fulltank_payment['pump' + sp].dis_cash;
		} else {
			cash_received = (parseFloat(fulltank_payment['pump' + sp].dis_cash) / 100).toFixed(2);
			//console.log(cash_received);
		}

		/*if(payment_type == "cash"){
			$("#input-cash-fulltank-" + sp).text("Cash Received");
			$("#input-cash-fulltank-" + sp).val("Cash Received");
		}*/


		qty = 0;
		if (price > 0) {
			qty = parseFloat(fulltank_pump_data['pump' + sp].amount) / price;
			qty = qty.toFixed(2);
		}

		sum_of_raw_amount = parseFloat(fulltank_pump_data['pump' + sp].amount);
		sst = 0.00;
		item_amount = 0.00;

		var amount_total = ((5 * Math.round((parseFloat(sum_of_raw_amount) * 100) / 5)) / 100);
		sst = parseFloat(sst) + parseFloat((sum_of_raw_amount) - ((sum_of_raw_amount) / (1 + (
			{{ $terminal->tax_percent }} / 100))));
		item_amount = parseFloat(sum_of_raw_amount) - parseFloat(((sum_of_raw_amount) - ((
			sum_of_raw_amount) / (1 +
			(
				{{ $terminal->tax_percent }} / 100)))));
		rounding = parseFloat(amount_total) - parseFloat(sum_of_raw_amount);
		rounding = parseFloat(rounding.toFixed(2));
		console.log("Rounding ", rounding);
		item_amount = item_amount.toFixed(2);

		/* MYR, ITEM AMOUNT, SST CALCULATION*/
		// console.log("qty x price", qty * price);
		var myr = parseFloat(dose).toFixed(2);
		console.log("MYR ", myr);
		var it_amt = parseFloat((myr / (1 + ({{ $terminal->tax_percent }} / 100)))).toFixed(2);
		var sst_calc = parseFloat((myr - it_amt)).toFixed(2);
		/* MYR, ITEM AMOUNT, SST CALCULATION*/

		var data = {
			"pump_no": sp,
			"dose": myr,
			"filled": myr,
			"cash_received": cash_received,
			"change_amount": change_amount,
			"payment_type": payment_type,
			"terminal_id": {!! $terminal->id ?? "''" !!},
			"tax_percent": {!! $terminal->tax_percent ?? "''" !!},
			"creditcard_no": creditcard_no,
			"company_id": {!! $company->id ?? "''" !!},
			"currency": "{{ $company->currency->code ?? '' }}",
			"mode": "{{ $terminal->mode ?? '' }}",
			"product_id": product_id,
			"product": product,
			"sst": sst_calc,
			"qty": qty,
			"price": price,
			"receipt_id": 0,
			"item_amount": it_amt,
			"cal_rounding": rounding,
		};

		'use strict';
		let _this = this;
		$.ajax({
			url: "{{ route('ft-create-fuel-list') }}",
			type: 'post',
			headers: {
				'X-CSRF-TOKEN': '{{ csrf_token() }}'
			},
			data: data,
			dataType: 'json',
			success: function(response) {
				console.log('PR fulltank.receipt.create:');
				console.log('PR ***** SUCCESS *****');
				console.log('response=' + JSON.stringify(response));

				//my ESCPOS printing function
				data.receipt_id = response;
				//console.log('data=' + JSON.stringify(data));

				receipt_id = data.receipt_id;
				console.log(receipt_id);
				if (payment_type == "creditac") {
					ft_credictact_update(receipt_id, myr, rounding, 'fulltank');
				}

				// // Save receipt_id in fulltank_pump_data[]
				fulltank_pump_data['pump' + sp].receipt_id = receipt_id;
				$(`#payment-status-fulltank-${sp}`).html('Paid');
				$('#payment-status-fulltank-' + sp).text('Paid');
				localStorage.setItem("reload_for_lc", "yes");
				localStorage.removeItem('reload_for_lc');
				localStorage.removeItem("fulltank_receipt_info");
				localStorage.removeItem("fulltank_receipt_data_" + sp);
				localStorage.removeItem("isNozzleDownFt" + sp);

				var newData = {
					"price": price,
					"dose": dose,
					"amount": dose,
					"receipt_id": receipt_id
				};

				localStorage.setItem("fulltank_receipt_info", sp);

				localStorage.setItem("fulltank_receipt_data_" + sp, JSON.stringify(
					newData));

				log2laravel('fulltank_receipt_data_' + sp + '  -> ' + JSON.stringify(
					newData));

					// Clearing of the table
				/* Need to have Qz.io running, otherwise print_receipt()
					* will bomb out and will not execute lines after it. We
					* trap the error so that we can still run even if Qz is
					* NOT running!! */
				try {
					//console.log("printing fulltank receipt with ID: ", response);
					localStorage.setItem("fulltank_receipt_id_" + sp, response);
					// Output receipt via thermal printer
					// DON'T COMMENT THIS!!! IT IS BEING USED IN PRODUCTION!!
					print_receipt_ft(response);
					// Open cash drawer
					// DON'T COMMENT THIS!!! IT IS BEING USED IN PRODUCTION!!
					open_cashdrawer();

				} catch (error) {
					/* This will catch if Qz.io is not being run!! */
					//alert('ERROR! print_receipt(). Check Qz!!');
					//alert("ERROR print_receipt(): " + JSON.stringify(error));
					console.error("ERROR: " + JSON.stringify(error));
				}
			},
			error: function(response) {
				console.log('PR FT.fulltank receipt.create:');
				console.log('PR ***** ERROR *****');
				console.log(JSON.stringify(response));
			}
		});
		$("#buffer-input-cash-ft" + sp).val("");
		$("#buffer-input-cash-ft" + sp).text("");
		$("#input-cash-fulltank-" + sp).val("");
		fulltank_reset['pump' + sp].reset = true;
		$('#fulltank-next-' + sp).removeClass('poa-finish-button-disabled');
		$('#fulltank-next-' + sp).addClass('ft-next-button');
		// if(parseInt(localStorage("pump_no")) == sp){
		// $('#ft-overlay-sa-' + sp).attr("hidden", true);

		$(`#input-cash-fulltank-${sp}`).hide();
		localStorage.setItem("reload_for_fm_sales", "yes");
		localStorage.removeItem('reload_for_fm_sales');

		localStorage.setItem("reload_ledger", "yes");
		localStorage.removeItem('reload_ledger');

		// }

		//Old debaunce trigger - Worng way
		//fulltankDeleteTerminalSyncData(sp);

		// New debounce trigger
		const fulltankDeleteTerminalSyncData = debounce(deteTermSyncData(sp),300);

        isClickProcessEnterFT = false;
	}

	// New line -- Update creditact with amount and id
    function ft_credictact_update(receipt_id, amount, rounding,source) {

	   $.ajax({
		   url: "{{route('creditaccount.receiptCreditActionUpdate')}}",
		   headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').
			   attr('content')},
		   type: 'post',
		   data: {
			   receipt_id:receipt_id,
			   credit_ac:amount,
			   creditact_id: localStorage.getItem('creditac_id'),
			   source: source,
			   rounding: rounding,
		   },
		   success: function (response) {
			   console.log('CA '+JSON.stringify(response));
			   localStorage.removeItem('creditac_id')
		   },
		   error: function (e) {
			   console.log('CA '+JSON.stringify(e));
		   }
	   });

   }


	function print_receipt_ft(receipt_id) {
		console.log('PR FT - print_receipt_ft()');
		console.log('PR FT - receipt_id=' + JSON.stringify(receipt_id));
		$.ajax({
			url: "/ft-print-receipt",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			type: 'post',
			data: {
				'receipt_id': receipt_id,
			},
			success: function(response) {
				var error1 = false,
					error2 = false;
				console.log('PR FT RECEIPT ' + JSON.stringify(response));
				try {
					eval(response);
				} catch (exc) {
					error1 = true;
					console.error('ERROR eval(): ' + exc);
				}

				if (!error1) {
					try {
						escpos_print_template();
					} catch (exc) {
						error2 = true;
						console.error('ERROR escpos_print_template(): ' + exc);
					}
				}
			},
			error: function(e) {
				console.log('PR ' + JSON.stringify(e));
			}
		});
	}
	function fulltankTerminalSyncData(pump_no) {
		if (isVisible == false)
			return;
		data = {};
		//console.log(pumpData[`pump${pump_no}`]);
		ft_terminal_sync = true;
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
			url: "{{ route('sync-data-ft') }}",
			type: 'post',
			headers: {
				'X-CSRF-TOKEN': '{{ csrf_token() }}'
			},
			data: data,
			dataType: 'json',
			success: function(response) {
				console.log('PR FT Sync DATA');
				ft_terminal_sync = false
			},
			error: function(e){
				console.log('PR ' + JSON.stringify(e));
			}
		});
		/*$.post('{{ route('sync-data-ft') }}', data).
		done(() => ft_terminal_sync = false).
		fail((e) => console.log(e));*/
	}

	//Old debounce trigger -- Its was being triggered the wrong way
	/* var fulltankDeleteTerminalSyncData = debounce(function(pump_no) {
        console.log("Fulltank - deleteTerminalSyncData for Pump : ", pump_no);
        $.post('{{ route('ft_delete_sync_data') }}', {
            pump_no: pump_no
        }).
        fail((e) => console.log(e));
    }, 300);*/

	//New debounce trigger -- Its was being triggered the wrong way
	function deteTermSyncData(pump_no){
		console.log("Fulltank - deleteTerminalSyncData for Pump : ", pump_no);
        $.post('{{ route('ft_delete_sync_data') }}', {
            pump_no: pump_no
        }).
		fail((e) => console.log(e));
	}
</script>
