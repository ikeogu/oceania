<!-- FUEL PAGE CODE BEGIN -->
<script>
    var access_code = "";
    var usersystemid = "";
    var username = "";
    var keys = [];
    var index = 0;
    var pumpData = {};
    var puno = localStorage.getItem('pump_no');
    var pri = localStorage.getItem('pump_receipt_info');
    var selected_pump = 0;

    var payment_type = "";
    var dis_cash = {};
    var reset = {};
	var deliveredPump = {};
    var receipt_id = 0;
    window.addEventListener('beforeunload', function(e) {
		disable_payment_btns();
        $('.button-number-amount').addClass('poa-button-number-disabled');
        $('.button-number-amount').removeClass('poa-button-number');
    });

    for (i = 1; i <= {{ env('MAX_PUMPS') }}; i++) {
        reset['pump' + i] = {
            status: '',
            reset: false,
        };
    }

    for (i = 1; i <= {{ env('MAX_PUMPS') }}; i++) {
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
            ogfuel_id: '',
            product_thumbnail: '',
            receipt_id: '',
            preset_type: 'amount',
            paymentStatus: 'Not Paid',
            payment_type: 'Prepaid',
            isNozzleUp: false,
            isAuth: false,
            is_slave: false
        };

		deliveredPump['pump' + i] = {
			price: '0.00',
			dose: '0.00',
			amount: '0.00',
			receipt_id: ''
		};
    }

    for (i = 1; i <= {{ env('MAX_PUMPS') }}; i++) {
        dis_cash['pump' + i] = {
            dis_cash: '',
            payment_type: '',
        };
    }

    var authorizeData = {}; {
        for (i = 1; i <= {{ env('MAX_PUMPS') }}; i++) {
            authorizeData['pump' + i] = {
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
    }


    function messageModal(msg) {
        $('#modalMessage').modal('show');
        $('#statusModalLabelMsg').html(msg);
        setTimeout(function() {
            $('#modalMessage').modal('hide');
        }, 2500);
    }


    function login_form_toggle() {
        disp = $("#login_form").css('display');

        if (disp == 'block')
            $("#login_form").css('display', 'none');
        else
            $("#login_form").css('display', 'block');
    }


    function login_me() {
        hosting = $("#hosting").val();
        email = $("#email").val();
        password = $("#password").val();
        $.ajax({
            url: "{{ route('uPLogin') }}",
            type: "POST",
            data: {
                hosting: hosting,
                email: email,
                @if (!empty($ONLY_ONE_HOST))
                    ONLY_ONE_HOST:'ONLY_ONE_HOST',
                @endif
                password: password
            },
            'headers': {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log('response', response);
                if (response.landing != undefined) {
                    window.location.href = response.landing;
                } else {
                    $(".login_error").html(response.login_error);
                    setInterval(function() {
                        $(".login_error").html('');
                    }, 6000);
                }
            },
            error: function(resp) {
                // Refresh page to get fresh csrf token
                window.location.reload();
            }
        });
    }


    $(document).ready(function() {
        // enterbtn.disabled = false;
        //selectbtn.hidden = true;

        var sInterval = null;
        //localStorage.setItem('pump_data_fuel_' + selectedPump, )

        //$('button.poa-button-number').removeClass('poa-button-cash-selected-disabled');
        //enable_payment_btns();

        @if (auth()->check())
            $('#userEditModal').modal('hide');
            $('#container-blur').css('opacity','1');

            $('#pump-main-block-0').show();
            usersystemid = "{{ Auth::user()->systemid }}";
            username = "{{ Auth::user()->fullname }}";

            // Only activate punp-get-status AFTER logging in
            @if (env('PUMP_HARDWARE') != null && !empty(Auth::user()))
                sInterval = setInterval(function(){
                    getTerminalSyncData();
		            fulltankGetTerminalSyncData();
		            //getOptTerminalSyncData();
                    mainGetStatus();
                }, 3000);
                /*
                // Getting master detection results
                log2laravel('info', selected_pump +
                ': getTerminalSyncData: isAuth =' +
                pumpData[`pump${selected_pump}`].isAuth);

                // Getting slave detection results
                log2laravel('info', selected_pump +
                ': getTerminalSyncData: is_slave=' +
                pumpData[`pump${selected_pump}`].is_slave);
                */

            @endif

        @else
            clearInterval(sInterval);
            $('#userEditModal').modal({backdrop: 'static', keyboard: false})
            $('#userEditModal').modal('show');
            $('#container-blur').css('opacity',' 0.25');
        @endif
    });


    var colorScheme = {
        colorOff: "#f0f0f0",
        colorOn: "#505050",
        colorBackground: "#f0f0f0",
        decimalPlaces: 2,
        slant: 5
    };


    function back() {
        var teamp = 0;
        for (i = 1; i <= {{ env('MAX_PUMPS') }}; i++) {
            if (pumpData['pump' + i].status == "Delivering") {
                teamp = 1;
            }
        }
        if (teamp == 0) {
            access_code = "";
            usersystemid = "";
            username = "";
            //$('#driverfuelledger').modal('hide');
            $('#login-message').html('');
            $('#userEditModal').modal({
                backdrop: 'static',
                keyboard: false
            })
            $('#userEditModal').modal('show');
            $('#container-blur').css('opacity', ' 0.25');
            keys.length = 0
            index = 0;
            var ipaddr = "{{ env('PTS_IPADDR') }}";
            for (i = 1; i <= {{ env('MAX_PUMPS') }}; i++) {
                pumpData['pump' + i] = {
                    status: 'Offline',
                    amount: '0.00',
                    dose: '0.00',
                    price: '0.00',
                };
                //$("#volume-liter-" + i).sevenSeg("destroy");
                $.ajax({
                    url: '/pump-cancel-authorize/' + i + '/' + ipaddr,
                    type: "GET",
                    dataType: "JSON",
                    success: function(resp) {},
                    error: function(resp) {
                        console.log('ERROR: ' + JSON.stringify(resp));
                    }
                });
            }

            $('#pump-main-block-' + selected_pump).hide();
            $('#pump-main-block-0').show();
            selected_pump = 0;
            $('#authorize-button').attr('class', '');
            $('#authorize-button').addClass('btn poa-authorize-disabled');
            $('.button-number-amount').addClass('poa-button-number-disabled');
            $('.button-number-amount').removeClass('poa-button-number');
        } else {
            messageModal("Delivering is in process, log out is not successful.");
        }
    }

    var isClickProcessEnter = false;

    function process_finish(mypump) {
        disable_payment_btns()
        log2laravel('info', '***** process_finish() *****');
        log2laravel('info', 'process_finish: mypump='+mypump);
        log2laravel('info', 'process_finish: selected_pump='+selected_pump);

        log2laravel('info', 'process_finish: pumpData_1='+
			JSON.stringify(pumpData['pump' + selected_pump]));


        // Start validating for reload and block any new transactions
        // if required
        // CASE 1: "Paid", filled==0, nozzle_down

        var spn = localStorage.getItem('pump_no');

        //var sppayStat = $('#payment-status-' + spn).text();
        if(spn == null){
            localStorage.setItem('pump_no', selected_pump);
            spn = selected_pump;
        }
        var sp = JSON.parse(localStorage.getItem('pumpDataState'))['pump' + spn];
        var sppayStat = sp.paymentStatus;
        var spNozzleDown = !sp.isNozzleUp;
        var spNozzleUp = sp.isNozzleUp;
		var ogfuel_id = sp.ogfuel_id;
        var spfilledStat = "";	// selected pump filled status

        //$('#pump-button-' + spn).addClass("poa-button-pump-delivering")
        if ($('#pump-button-' + spn).hasClass("poa-button-pump-delivering")) {
            spNozzleDown = false;
            spNozzleUp = true;
        }

        var spreceipt = JSON.parse(localStorage.getItem('pump_receipt_data_' + spn));
        if (localStorage.getItem("pump_receipt_data_" + spn) === null) {
            spfilledStat = 0;
            // sppayStat = "Not Paid";
        } else {
            spfilledStat = spreceipt.amount;
        }

        localStorage.setItem('scover_grey_' + spn, spn)

        //set pump to grey
        pump_delivering_status_additions(spn, true);

        log2laravel('info', 'process_finish: pumpData_2='+
			JSON.stringify(pumpData['pump' + selected_pump]));

        //case 1
        $(document).ready(function() {
            if (sppayStat === "Paid" && spNozzleDown == true &&
				spfilledStat === "0.00") {

                log2laravel('info', 'process_finish: pumpData_2a='+
					JSON.stringify(pumpData['pump' + selected_pump]));

                $('#cancelModal').modal('show');
                setTimeout(function() {
                    $("#cancelModal").blur();
                }, 5000);

                $('#cancelTxn').click(function() {
                    $('#cancelModal').modal('hide');

                    voidPumpAndDeAuthorize(spn);
                });

                $("#cancelModal").on("blur", function() {
                    $('#cancelModal').modal('hide')

                    //case two
                    // CASE 2: "Paid", filled==0, nozzle_up
                    if (isClickProcessEnter == true)
                        return
                    isClickProcessEnter = true;
                    console.log('new')
                    numpad_disable();
                    disable_payment_btns();
                    $('button.poa-button-number').addClass('poa-button-cash-selected-disabled');

                    if ($('.cancel-pump-number-' + selected_pump).hasClass(
                            'poa-button-number-payment-cancel-disabled')) {
                        $('.cancel-pump-number-' + selected_pump).removeClass(
                            'poa-button-number-payment-cancel-disabled');
                        $('.give-me-left-' + selected_pump).addClass('left-7');
                        $('.cancel-pump-number-' + selected_pump).addClass('btn-size-70');
                    }
                    $("#input-cash" + selected_pump).val('');
                    $("#payment-type-paid-right" + selected_pump).html("");
                    $("#payment-type-amount" + selected_pump).html("");
                    $("#payment-amount-card-amount" + selected_pump).html("");
                    $("#payment-type-amount" + selected_pump).html("");
                    $("#payment-type-message" + selected_pump).html('');
                    $("#payment-type-amount" + selected_pump).html('');
                    $(`#input-cash` + selected_pump).val('');
                    //$(`#input-cash` + selected_pump).show();----

                    $('.numpad-cancel-payment' + selected_pump).removeClass(
                        'poa-button-number-payment-cancel-disabled');
                    $('.numpad-cancel-payment' + selected_pump).addClass(
                        'poa-button-number-payment-cancel');

                    pumpData['pump' + selected_pump].paymentStatus = "Paid";

                    // pumpData['pump' + selected_pump].product = "";
                    if (dis_cash['pump' + selected_pump].payment_type == "cash") {
                        $("#payment-value" + selected_pump).css("color", "#a0a0a0");
                        $("#payment-value" + selected_pump).html("Cash Received");
                        //$("#payment-div-cash"+selected_pump+" div").addClass("justify-content-center");
                    } else if (dis_cash['pump' + selected_pump].payment_type == "card") {
                        $(".payment-div-card" + selected_pump).hide();
                        $("#payment-div-cash" + selected_pump).show();
                        $("#payment-value" + selected_pump).css("color", "#a0a0a0");
                        $("#payment-value" + selected_pump).html("Cash Received");
                        //$("#payment-div-cash"+selected_pump+" div").addClass("justify-content-center");
                        $("#payment-value-card" + selected_pump).html("");
                    }
                    dis_cash_ = (parseFloat(dis_cash['pump' + selected_pump].dis_cash) / 100).toFixed(
                        2);

                    if (pumpData['pump' + selected_pump].payment_type == "Postpaid") {
                        var amount_total = pumpData['pump' + selected_pump].amount;
                        // amount_total = ((5 * Math.round((amount_total*100) / 5))/100).toFixed(2);
                        var dose = amount_total;
                        var change_amount = dis_cash_ - amount_total;
                        amount_total = ((5 * Math.round((amount_total * 100) / 5)) / 100).toFixed(2);
                        pumpData['pump' + selected_pump].isAuth = false;

                    } else {
                        var dose = $(`#total_amount-main-${selected_pump}`).text().replace(/,/g, '');

                        if (dis_cash['pump' + selected_pump].payment_type == "cash") {
                            if (pumpData['pump' + selected_pump].preset_type == 'Litre') {
                                var change_amount =
									dis_cash_ - parseFloat($("#total_amount-main-" +
									selected_pump)
                                    .text()
                                    .replace(/,/g, ''));

                            } else {
                                var change_amount = dis_cash_ - pumpData['pump' + selected_pump].dose;
                            }
                        }
                    }

					log2laravel('debug','L446 pump_authorize()');
					log2laravel('debug','L446 sp='+ JSON.stringify(sp));

					/* We need sp.ogfuel_id to feed getFuelGradeId() */
                    pump_authorize(spn, sp.product_id, sp.ogfuel_id);
                    reset['pump' + selected_pump].reset = true;
                    isClickProcessEnter = false;
                });

            } else if (sppayStat === "Paid" && spNozzleUp == true && spfilledStat === "0.00") {
                $('#inProgressModal').modal('show');
                setTimeout(function() {
                    $("#inProgressModal").blur();
                }, 5000);
                //case two
                // CASE 2: "Paid", filled==0, nozzle_up
            } else {

				log2laravel('info', 'process_finish: pumpData_3='+
					JSON.stringify(pumpData['pump' + selected_pump]));

                if (isClickProcessEnter == true)
                    return

                isClickProcessEnter = true;
                console.log('new')
                numpad_disable();
                disable_payment_btns();
                // $('span').removeClass('selectedimag');
                $('button.poa-button-number').addClass('poa-button-cash-selected-disabled');
                if ($('.cancel-pump-number-' + selected_pump).hasClass(
                        'poa-button-number-payment-cancel-disabled')) {

                    $('.cancel-pump-number-' + selected_pump).removeClass(
                        'poa-button-number-payment-cancel-disabled');
                    $('.give-me-left-' + selected_pump).addClass('left-7');
                    $('.cancel-pump-number-' + selected_pump).addClass('btn-size-70');
                }

                $("#input-cash" + selected_pump).val('');
                $("#payment-type-paid-right" + selected_pump).html("");
                $("#payment-type-amount" + selected_pump).html("");
                $("#payment-amount-card-amount" + selected_pump).html("");
                $("#payment-type-amount" + selected_pump).html("");
                $("#payment-type-message" + selected_pump).html('');
                $("#payment-type-amount" + selected_pump).html('');
                $(`#input-cash` + selected_pump).val('');
                //$(`#input-cash` + selected_pump).show();

				// This is obsoleted!!
                $('.numpad-cancel-payment' + selected_pump).removeClass(
                    'poa-button-number-payment-cancel-disabled');
                $('.numpad-cancel-payment' + selected_pump).addClass('poa-button-number-payment-cancel');

                pumpData['pump' + selected_pump].paymentStatus = "Paid";
                // pumpData['pump' + selected_pump].product = "";
                if (dis_cash['pump' + selected_pump].payment_type == "cash") {
                    $("#payment-value" + selected_pump).css("color", "#a0a0a0");
                    $("#payment-value" + selected_pump).html("Cash Received");
                    //$("#payment-div-cash"+selected_pump+" div").addClass("justify-content-center");
                } else if (dis_cash['pump' + selected_pump].payment_type == "card") {
                    $(".payment-div-card" + selected_pump).hide();
                    $("#payment-div-cash" + selected_pump).show();
                    $("#payment-value" + selected_pump).css("color", "#a0a0a0");
                    $("#payment-value" + selected_pump).html("Cash Received");
                    //$("#payment-div-cash"+selected_pump+" div").addClass("justify-content-center");
                    $("#payment-value-card" + selected_pump).html("");
                }
                dis_cash_ = (parseFloat(dis_cash['pump' + selected_pump].dis_cash) / 100).toFixed(2);

                if (pumpData['pump' + selected_pump].payment_type == "Postpaid") {
                    var amount_total = pumpData['pump' + selected_pump].amount;
                    // amount_total = ((5 * Math.round((amount_total*100) / 5))/100).toFixed(2);
                    var dose = amount_total;
                    var change_amount = dis_cash_ - amount_total;
                    amount_total = ((5 * Math.round((amount_total * 100) / 5)) / 100).toFixed(2);
                    pumpData['pump' + selected_pump].isAuth = false;

                } else {
                    var dose = $(`#total_amount-main-${selected_pump}`).text().replace(/,/g, '');

					log2laravel('info', 'process_finish: pumpData_4='+
						JSON.stringify(pumpData['pump' + selected_pump]));


                    if (dis_cash['pump' + selected_pump].payment_type == "cash") {
                        if (pumpData['pump' + selected_pump].preset_type == 'Litre') {
                            var change_amount = dis_cash_ - parseFloat($("#total_amount-main-" + selected_pump)
                                .text()
                                .replace(/,/g, ''));
                        } else {
                            var change_amount = dis_cash_ - pumpData['pump' + selected_pump].dose;
                        }
                    }
                }

                var filled = parseFloat(pumpData['pump' + selected_pump].amount);
                if (filled == dose) {
                    reset['pump' + selected_pump].reset = true;
                }

				log2laravel('info', 'process_finish: pumpData_5='+
					JSON.stringify(pumpData['pump' + selected_pump]));


                var qty = pumpData['pump' + selected_pump].volume;
                var price = pumpData['pump' + selected_pump].price;
                var payment_type = dis_cash['pump' + selected_pump].payment_type;
                var creditcard_no = "";
                var cash_received = 0.00;
                var product_id = pumpData['pump' + selected_pump].product_id;
                var product = pumpData['pump' + selected_pump].product;

				log2laravel('info', 'process_finish: pumpData_6='+
					JSON.stringify(pumpData['pump' + selected_pump]));

                if (payment_type == "card") {
                    creditcard_no = dis_cash['pump' + selected_pump].dis_cash;
                } else {
                    cash_received = (parseFloat(dis_cash['pump' + selected_pump].dis_cash) / 100).toFixed(2);
                }

                qty = 0;
                if (price > 0) {
                    qty = parseFloat(pumpData['pump' + selected_pump].dose) / price;
                    qty = qty.toFixed(2);
                }

                sum_of_raw_amount = parseFloat(pumpData['pump' + selected_pump].dose);

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
                item_amount = item_amount.toFixed(2);

                $('#table-PRODUCT-' + selected_pump).text('');
                $('#table-PRICE-' + selected_pump).text('');
                $('#table-QTY-' + selected_pump).text('');
                $('#table-MYR-' + selected_pump).text('0.00');
                $('#item-amount-calculated-' + selected_pump).text('0.00');
                $('#sst-val-calculated-' + selected_pump).text('0.00');
                $('#rounding-val-calculated-' + selected_pump).text('0.00');
                $('#grand-total-val-calculated-' + selected_pump).text('0.00');
                $('#change-val-calculated-' + selected_pump).text('0.00');

                log2laravel();
                var data = {
                    "pump_no": selected_pump,
                    "dose": dose,
                    "filled": '0.00',
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
                    "sst": sst.toFixed(2),
                    "qty": qty,
                    "price": price,
                    "receipt_id": 0,
                    "item_amount": item_amount,
                    "cal_rounding": rounding,
                };

                // Display screen E flash
                localStorage.removeItem('show-screen-e-fuel-modal')
                localStorage.setItem('show-screen-e-fuel-modal', JSON.stringify(data));

                'use strict';
                let _this = this;
                $.ajax({
                    url: "{{ route('create-fuel-list') }}",
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: data,
                    dataType: 'json',
                    success: function(response) {
                        log2laravel('info','process_finish: PR fuel.receipt.create:');
                        console.log('PR ***** SUCCESS *****');
                        console.log('response=' + JSON.stringify(response));

                        //my ESCPOS printing function
                        data.receipt_id = response;
                        console.log('data=' + JSON.stringify(data));

                        receipt_id = data.receipt_id;

                        // New line -- Update creditact with amount and id
                        creditact_update(receipt_id,'fuel');

                        // Save receipt_id in pumpData[]
                        pumpData['pump' + selected_pump].receipt_id = receipt_id;
                        $(`#payment-status-${selected_pump}`).html('Paid');
                        $('#payment-status-' + selected_pump).text('Paid');
                        localStorage.setItem("reload_for_lc", "yes");
		                localStorage.removeItem('reload_for_lc');
                        localStorage.removeItem("pump_receipt_info");

                        localStorage.removeItem("pump_receipt_data_" + selected_pump);
                        log2laravel('info',
							'removed pump_receipt_data_'+
							selected_pump);

                        localStorage.removeItem("isNozzleDown" + selected_pump);

                        var newData = {
                            "price": price,
                            "dose": dose,
                            "amount": '0.00',
                            "receipt_id": receipt_id
                        };

                        localStorage.setItem("pump_receipt_info", selected_pump);

                        localStorage.setItem("pump_receipt_data_" +
							selected_pump, JSON.stringify(newData));

                        log2laravel('info', 'pump_receipt_data_' +
							selected_pump + '  -> ' + JSON.stringify(newData));

                        var pump_paid_for = $('#paid-pump').val();

                        if (pump_paid_for.length > 0) {
                            if (pump_paid_for.includes(',')) {
                                var splited_pumps = pump_paid_for.split(',');
                                splited_pumps.push(selected_pump);

                                var final_active_pump = splited_pumps.join(',');

                                $('#paid-pump').val(final_active_pump);
                            } else {
                                $('#paid-pump').val(pump_paid_for + ',' + selected_pump);
                            }
                        } else {
                            $('#paid-pump').val(selected_pump);
                        }
                        /*
                        $(`#payment-status-${selected_pump}`).css('color','#43d24d');
                        $(`#payment-status-${selected_pump}`).css('font-weight','bold');
                        */

                        $('#total-final-litre-' + selected_pump).text('0.00');
                        $('#total-final-filled-' + selected_pump).text('0.00');

                        if ($('.cancel-pump-number-' + selected_pump).hasClass(
                                'poa-button-number-payment-cancel-disabled')) {

                            $('.cancel-pump-number-' + selected_pump).removeClass(
                                'poa-button-number-payment-cancel-disabled');
                            $('.give-me-left-' + selected_pump).addClass('left-7');
                            $('.cancel-pump-number-' + selected_pump).addClass('btn-size-70');
                        }

                        $('#product-select-pump-' + selected_pump + ' > span > img').addClass(
                            'product-disable-offline');

                        $('#fulltank-button-' + selected_pump).addClass('fulltank-button fulltank-button-disabled');
                        $('#product-select-pump-' + selected_pump + ' > span > img').removeClass(
                            'cursor-pointer');
                        $('#product-select-pump-' + selected_pump + ' > span > img').addClass(
                            'noclick');

                        // Clearing of the table
                        /* Need to have Qz.io running, otherwise print_receipt()
                         * will bomb out and will not execute lines after it. We
                         * trap the error so that we can still run even if Qz is
                         * NOT running!! */
                        try {
                            localStorage.setItem("pump_receipt_id_" + selected_pump, response);
                            // Output receipt via thermal printer
                            // DON'T COMMENT THIS!!! IT IS BEING USED IN PRODUCTION!!
                            //print_receipt(response);

                            // Open cash drawer
                            // DON'T COMMENT THIS!!! IT IS BEING USED IN PRODUCTION!!
                            //open_cashdrawer();

							// Generate PDF copy of receipt
                            fuel_generate_pdf(response);

                        } catch (error) {
                            /* This will catch if Qz.io is not being run!! */
                            //alert('ERROR! print_receipt(). Check Qz!!');
                            //alert("ERROR print_receipt(): " + JSON.stringify(error));
                            console.error("ERROR: " + JSON.stringify(error));
                        }
                    },
                    error: function(response) {
                        console.log('PR fuel.receipt.create:');
                        console.log('PR ***** ERROR *****');
                        console.log(JSON.stringify(response));
                    }
                });

				// Authorize only after generating receipt
				log2laravel('debug','L740 pump_authorize()');
				log2laravel('debug','L740 pumpData=' +
					JSON.stringify(pumpData['pump' + selected_pump]));

				/* We need ogfuel_id to feed getFuelGradeId() */
				pump_authorize(selected_pump, product_id, ogfuel_id);

				reset['pump' + selected_pump].reset = true;
				isClickProcessEnter = false;
            }
        });
    }

    // New line -- Update creditact with amount and id
    function creditact_update(receipt_id, source) {
        $.ajax({
            url: "{{route('creditaccount.receiptCreditActionUpdate')}}",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').
                attr('content')},
            type: 'post',
            data: {
                receipt_id:receipt_id,
                credit_ac:localStorage.getItem('pump_data_fuel_' + selected_pump),
                creditact_id: localStorage.getItem('creditac_id'),
                source: source
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

    function pump_data_update(pump_no, amount = false) {

        // Source of Dose
        sum_of_raw_amount = parseFloat(pumpData['pump' + pump_no].dose);

        /*
        var pdata = localStorage.getItem('pump_data_fuel_'+pump_no);
        sum_of_raw_amount = parseFloat(pdata);
        */

        if (amount != false)
            sum_of_raw_amount = parseFloat(amount);

        sst = 0.00;
        item_amount = 0.00;
        //sum_of_raw_amount = 0.00;

        var amount_total = ((5 * Math.round((parseFloat(sum_of_raw_amount) * 100) / 5)) / 100);
        sst = parseFloat(sst) + parseFloat((sum_of_raw_amount) - ((sum_of_raw_amount) / (1 + (
            {{ $terminal->tax_percent }} / 100))));
        item_amount = parseFloat(sum_of_raw_amount) - parseFloat(((sum_of_raw_amount) - ((sum_of_raw_amount) / (1 + (
            {{ $terminal->tax_percent }} / 100)))));
        rounding = parseFloat(amount_total) - parseFloat(sum_of_raw_amount);
        rounding = parseFloat(rounding.toFixed(2));

        $(`#table-MYR-${pump_no}`).text('0.00');

        //$(`#table-PRODUCT-${pump_no}`).text(pumpData['pump' + pump_no].product);
        price = parseFloat(pumpData['pump' + pump_no].price);
        //$(`#table-PRICE-${pump_no}`).text(price.toFixed(2))
        //console.log(sum_of_raw_amount);
        if (price > 0) {
            qty = parseFloat(sum_of_raw_amount) / price;
            qty = qty.toFixed(2);
            //$(`#table-QTY-${pump_no}`).text(qty)
        } else {
            //$(`#table-QTY-${pump_no}`).text('0.00')
        }
    }

    function updateFilled(receipt_id, filled, refund) {
        $.ajax({
			method: "post",
			url: "{{ route('update.filled') }}",
			data: {
				id: receipt_id,
				filled: filled,
				refund: refund
			},
		}).done((data) => {
			//table.ajax.reload();
		})
		.fail((data) => {
			log2laravel('error','ERROR: updateFilled: data='+
				JSON.stringify(data));
			//console.log("data", data)
		});
    }

    function update_payment_table(pump_no, amount = false) {

        sum_of_raw_amount = parseFloat(pumpData['pump' + pump_no].dose);

        /*
        var pdata = localStorage.getItem('pump_data_fuel_'+pump_no);
        sum_of_raw_amount = parseFloat(pdata);
        */

        if (amount != false) {
            sum_of_raw_amount = parseFloat(amount);
        }

        sst = 0.00;
        item_amount = 0.00;

        var amount_total = ((5 * Math.round((parseFloat(sum_of_raw_amount) * 100) / 5)) / 100);
        sst = parseFloat(sst) + parseFloat((sum_of_raw_amount) - ((sum_of_raw_amount) / (1 + (
            {{ $terminal->tax_percent }} / 100))));
        item_amount = parseFloat(sum_of_raw_amount) - parseFloat(((sum_of_raw_amount) - ((sum_of_raw_amount) / (1 + (
            {{ $terminal->tax_percent }} / 100)))));
        rounding = parseFloat(amount_total) - parseFloat(sum_of_raw_amount);
        rounding = parseFloat(rounding.toFixed(2));

        $(`#table-MYR-${pump_no}`).text(sum_of_raw_amount.toFixed(2))
        $(`#item-amount-calculated-${pump_no}`).text(item_amount.toFixed(2));
        $(`#sst-val-calculated-${pump_no}`).text(sst.toFixed(2));
        $(`#rounding-val-calculated-${pump_no}`).text(rounding.toFixed(2));
        $(`#grand-total-val-calculated-${pump_no}`).text((sum_of_raw_amount + parseFloat(rounding)).toFixed(2));


        $(`#table-PRODUCT-${pump_no}`).text(pumpData['pump' + pump_no].product);
        price = parseFloat(pumpData['pump' + pump_no].price);
        $(`#table-PRICE-${pump_no}`).text(price.toFixed(2))

        $('#prd-myr-' + pump_no).text(''+sum_of_raw_amount.toFixed(2));
        $('#prd-myr-' + pump_no + '-price').text(price.toFixed(2));

        if (price > 0) {
            qty = parseFloat(sum_of_raw_amount) / price;
            qty = qty.toFixed(2);
            $(`#table-QTY-${pump_no}`).text(qty)
        } else {
            $(`#table-QTY-${pump_no}`).text('0.00')
        }

        // Disable Enter
		$('.finish-button-' + pump_no).addClass('poa-finish-button-disabled');
    }


    function getPumpStatus(my_pump, insert_filled = true) {
        var ipaddr = get_hardwareip(my_pump, false);
        //console.log('my_pump='+my_pump+', ipaddr='+ipaddr);

        $.ajax({
		url: '/pump-get-status/' + my_pump + '/' + ipaddr,
		type: "GET",
		dataType: "JSON",
		success: function(resp) {

			var packet_type = null;

			log2laravel('info', 'getPumpStatus 0: ' + JSON.stringify(resp));

			if ((resp != null) && (typeof resp != 'undefined')) {
				resp = resp.data;

				log2laravel('info', 'getPumpStatus 1: ' + JSON.stringify(resp));
				log2laravel('info', 'getPumpStatus 1.1: ' + JSON.stringify(resp.response));

				if ((typeof resp.response != 'undefined') &&
					(resp.response != null)) {
					var response = resp.response;

					log2laravel('info', 'getPumpStatus 2: ' + JSON.stringify(response));
					log2laravel('info', 'getPumpStatus 2.1: ' + JSON.stringify(response.Packets));
					log2laravel('info', 'getPumpStatus 2.2: ' + JSON.stringify(response.Packets[0]));
					log2laravel('info', 'getPumpStatus 2.3: ' + (typeof response.Packets));
					log2laravel('info', 'getPumpStatus 2.4: ' + (typeof response.Packets[0]));
					log2laravel('info', 'getPumpStatus 2.5: ' + JSON.stringify(response.Packets));
					log2laravel('info', 'getPumpStatus 2.6: ' + JSON.stringify(response.Packets[0]));

					if ((typeof response.Packets != 'undefined') &&
						(response.Packets != null) &&
						(typeof response.Packets[0] != 'undefined') &&
						(response.Packets[0] != null)) {

						log2laravel('info',
							'getPumpStatus 3: ***** Before End Transaction detection *****');

						var packet = response.Packets[0];
						var pump_no = packet.Data.Pump;


						switch (packet.Type) {
						case 'PumpTotals':
							var volume = packet.Data.Volume;
							var amount = packet.Data.Amount;

							log2laravel('info', 'getPumpStatus 3.0: PumpTotals=' +
								JSON.stringify(packet));
							break;

						case 'PumpFillingStatus':
							var volume = packet.Data.Volume;
							var price = packet.Data.Price;
							var amount = packet.Data.Amount;

							log2laravel('info', 'getPumpStatus 3.1: pump_no=' +
								pump_no + ', volume=' + volume +
								', price=' + price + ', amount=' + amount);
							break;

						case 'PumpOfflineStatus':
							break;

						case 'PumpIdleStatus':
						default:
							var LastTransaction = packet.Data.LastTransaction;
							var NozzleUp = packet.Data.NozzleUp;
							var LastVolume = packet.Data.LastVolume;
							var LastPrice = packet.Data.LastPrice;
							var LastAmount = packet.Data.LastAmount;
							var LastNozzle = packet.Data.LastNozzle;
							var tx_id = authorizeData['pump' + my_pump].transactionid;

							log2laravel('info', 'getPumpStatus 3.2: ***** BEFORE ' +
								'Transaction end detection *****');

							log2laravel('info', 'getPumpStatus 3.3: tx_id=' + tx_id +
								', LastTransaction=' + LastTransaction);

							log2laravel('info', 'getPumpStatus 3.4: LastAmount=' +
								LastAmount + ', LastVolume=' + LastVolume +
								', LastPrice=' + LastPrice + ', LastNozzle=' + LastNozzle +
								', LastTransaction=' + LastTransaction);

							log2laravel('info',
								'getPumpStatus 3.5: packet.Type=' + packet.Type);
							log2laravel('info',
								'getPumpStatus 3.6: tx_id=' + tx_id);
							log2laravel('info',
								'getPumpStatus 3.7: LastTransaction=' +
								LastTransaction);
							log2laravel('info',
								'getPumpStatus 3.8: NozzleUp=' +
								NozzleUp);
						}

						// Test whether transaction has ended
						if ((packet.Type == 'PumpIdleStatus') &&
							(tx_id != "") &&
							(tx_id != 0) &&
							(typeof LastTransaction != "undefined") &&
							(LastTransaction != 0) &&
							(LastTransaction == tx_id)) {


							// Process transaction on Nozzle Down event with
							// the latest transaction
							process_transaction(my_pump, LastAmount, LastVolume,
								LastPrice, LastNozzle, LastTransaction);


						} else if ((packet.Type == 'PumpTotals')) {
							log2laravel('info', my_pump +
								': getPumpStatus 5.1: packet=' +
								JSON.stringify(packet));

						} else if ((packet.Type == 'PumpEndOfTransactionStatus')) {
							log2laravel('info', my_pump +
								': getPumpStatus 5.2: packet=' +
								JSON.stringify(packet));

						} else {
							var tid = {{ $terminal->id }};

							// Only REMATCH if NOT slave (i.e. is master)
							if (!pumpData['pump' + my_pump].is_slave) {

							log2laravel('info', my_pump + '(' + tid + ')' +
								': REMATCH getPumpStatus 5.3: ERROR! ' +
								'DID NOT MATCH RETURN DUE TO: tx_id='+
								tx_id+' AND LastTransaction='+
								LastTransaction);

							log2laravel('info', my_pump + '(' + tid + ')' +
								': REMATCH getPumpStatus 5.4: packet=' +
								JSON.stringify(packet));

							// Where the rematch magic happens!
							rematch_transaction(my_pump);
							}
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


	// This functions will attempt to rematch transaction with PTS when
	// first attempt with transaction_id failed to be matched
	function rematch_transaction(my_pump) {
		log2laravel('info', 'REMATCH rematch_transaction: my_pump='+ my_pump);
		var ipaddr = "{{ env('PTS_IPADDR') }}";

		$.ajax({
			url: '/pump-transaction-information/' + my_pump + '/0/' + ipaddr,
			type: "GET",
			dataType: "JSON",
			success: function(resp) {

				log2laravel('info', 'REMATCH rematch_transaction: resp='+
					JSON.stringify(resp));

				var pkt = resp.data.response.Packets[0];
				var type = pkt.Type;
				var data = pkt.Data;
				var pump = data.Pump;
				var trans = data.Transaction;
				var nozzle = data.Nozzle;
				var volume = data.Volume;
				var amount = data.Amount;
				var price = data.Price;

				log2laravel('info',
					'REMATCH rematch_transaction: type='+ type);
				log2laravel('info',
					'REMATCH rematch_transaction: pump='+ pump);
				log2laravel('info',
					'REMATCH rematch_transaction: trans='+ trans);
				log2laravel('info',
					'REMATCH rematch_transaction: nozzle='+ nozzle);
				log2laravel('info',
					'REMATCH rematch_transaction: volume='+ volume);
				log2laravel('info',
					'REMATCH rematch_transaction: amount='+ amount);
				log2laravel('info',
					'REMATCH rematch_transaction: price='+ price);

				process_transaction(my_pump, amount, volume,
					price, nozzle, trans);

				log2laravel('info',
					'REMATCH rematch_transaction: AFTER process_transaction');
			},
			error: function(resp) {
				console.log('ERROR: ' + JSON.stringify(resp));
			}
		});
	}


	// Process transaction upon detecting Nozzle Down event
	async function process_transaction(my_pump, LastAmount, LastVolume,
		LastPrice, LastNozzle, LastTransaction) {

		var tid = {{ $terminal->id }};

		// Always insert filled
		var insert_filled =true;

		pumpData['pump' + my_pump].amount = LastAmount;
		pumpData['pump' + my_pump].volume = LastVolume;
		pumpData['pump' + my_pump].price = LastPrice;
		pumpData['pump' + my_pump].nozzle = LastNozzle;

		log2laravel('info', my_pump + '(' + tid + ')' +
			': REMATCH process_transaction 1.0 ***** DETECTED Transaction End *****');

		log2laravel('info', my_pump + '(' + tid + ')' +
			': REMATCH process_transaction 1.1: dose=' +
			pumpData['pump' + my_pump].dose);

		log2laravel('info', my_pump + '(' + tid + ')' +
			': REMATCH process_transaction 1.2: LastAmount=' +
			LastAmount + ', LastVolume=' + LastVolume +
			', LastPrice=' + LastPrice + ', LastNozzle=' +
			LastNozzle + ', LastTransaction=' +
			LastTransaction + ' insert_filled=' + insert_filled);

		// Enable the nozzled down pump's payment buttons
		// Sometimes, deliveredPump is NULL!!
		// Also sometimes, pump_receipt_data_X.receipt_id is NULL!!
		deliveredPump['pump'+my_pump] =
			localStorage.getItem("pump_receipt_data_" + my_pump);


		log2laravel('info', my_pump + '(' + tid + ')' +
			': REMATCH process_transaction 1.3: deliveredPump[pump'+my_pump+
			']=' + deliveredPump['pump'+my_pump]);

		log2laravel('info', my_pump + '(' + tid + ')' +
			': REMATCH process_transaction 1.3.1: deliveredPump=' +
			JSON.stringify(deliveredPump));


		//----------------------------------------------
		//DANGER: HARDCODED. TESTING ONLY. REMOVE THIS
		//deliveredPump['pump'+my_pump] = '';
		//----------------------------------------------



		if (deliveredPump['pump'+my_pump] != undefined &&
			deliveredPump['pump'+my_pump] != '') {
			deliveredPump['pump'+my_pump] = JSON.parse(deliveredPump['pump'+my_pump]);
			dose =  deliveredPump['pump'+my_pump].dose;
			price = deliveredPump['pump'+my_pump].price;

			// Some receipt_id is NULL!!
			receipt_id = deliveredPump['pump'+my_pump].receipt_id;

			if (receipt_id == undefined ||
				receipt_id == 0 ||
				receipt_id == "") {

				// Recovering receipt_id from it's own localStorage
				receipt_id = localStorage.getItem("pump_receipt_id_" + my_pump);

				log2laravel('info', my_pump + '(' + tid + ')' +
				': FIX process_transaction 1.3a: RECOVERED receipt_id =' +
				receipt_id);
			}

		} else {
			// When deliveredPump is NULL. This happens 5-8% of all transactions!!
			dose = pumpData['pump' + my_pump].dose;
			price = LastPrice;

			log2laravel('info', my_pump + '(' + tid + ')' +
				': FIX process_transaction 1.3b: dose =' + dose +
				', price='+ LastPrice);

			// Recovering receipt_id from its own localStorage
			receipt_id = localStorage.getItem("pump_receipt_id_" + my_pump);


			//----------------------------------------------
			//DANGER: HARDCODED. TESTING ONLY. REMOVE THIS
			//receipt_id = null;
			//----------------------------------------------

			// Last resort to recover receipt_id
			if (receipt_id == null || receipt_id ==  '') {
				receipt_id = pumpData['pump' + my_pump].receipt_id;
			}

			log2laravel('info', my_pump + '(' + tid + ')' +
			': FIX process_transaction 1.3c: RECOVERED receipt_id =' +
			receipt_id);

			// This is where we try to query the DB for the pump's
			// latest transaction manually
			if (receipt_id == null || receipt_id == '') {
				receipt_id = await fetch_latest_receipt_id(my_pump);

				log2laravel('info',
					'REMATCH process_transaction 1.3d: receipt_id=' +
					JSON.stringify(receipt_id));
			}
		}

		log2laravel('info',
			'REMATCH process_transaction 1.3e: receipt_id=' +
			JSON.stringify(receipt_id));


		// Update database filled and refund columns
		updateFilled(receipt_id, LastAmount, dose - LastAmount);

		var newData = {
			"price": price,
			"dose": dose,
			"amount": parseFloat(LastAmount).toFixed(2),
			"receipt_id": receipt_id
			//"receipt_id": pumpData['pump' + my_pump].receipt_id
		};

		log2laravel('info', my_pump + '(' + tid + ')' +
			': REMATCH process_transaction 1.4: newData=' +
			JSON.stringify(newData));

		localStorage.removeItem("pump_receipt_data_" + my_pump);
		localStorage.setItem("pump_receipt_data_" + my_pump,
			JSON.stringify(newData));
		localStorage.setItem("pump_data_fuel_" + my_pump,
			dose);
		localStorage.removeItem('reload_receipt_list')
		localStorage.setItem("reload_receipt_list", "yes");


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


						log2laravel('info', my_pump + '(' + tid + ')' +
							': REMATCH process_transaction 1.5: splited_paidPumps =' +
							JSON.stringify(splited_paidPumps));

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
	}



	async function fetch_latest_receipt_id(my_pump) {
		let res;
        await $.ajax({
            url: "{{ route('fuel.fetch-latest-receipt-id') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'post',
            data: {
                'my_pump': my_pump,
            },
            success: function(response) {
				log2laravel('info','BLADE fetch_latest_receipt_id: response=' +
					response);
					res = response;
            },
            error: function(e) {
                console.log('VR ' + JSON.stringify(e));
            }
        });
		
		log2laravel('info','BLADE fetch_latest_receipt_id: res='+ res)
		return res;
	}


    function removeRefund() {
        $("#dCalc").css('display', 'none');
    }



    var process_refund = function(my_pump) {
        //SAMPLE OF filled less than dose
        if (pumpData['pump' + my_pump].preset_type == 'Litre') {
            re_dose = parseFloat($("#total_amount-main-" + my_pump).text())

            log2laravel('info', my_pump +
                ': process_refund: Litre re_dose=' + re_dose);

        } else {
            re_dose = parseFloat(pumpData['pump' + my_pump].dose)

            log2laravel('info', my_pump +
                ': process_refund: MYR re_dose=' + re_dose);
        }


        log2laravel('info', my_pump +
            ': process_refund: preset_type=' +
            pumpData['pump' + my_pump].preset_type);

        log2laravel('info', my_pump +
            ': process_refund: final re_dose=' +
            re_dose);



        // This is kaput. Sometimes gets mixed-up with price
        var dose = re_dose;

        //var filled = parseFloat(pumpData['pump'+my_pump].amount);
        //var refund = dose - filled;

        var filled = parseFloat(pumpData['pump' + my_pump].volume);
        log2laravel('info', my_pump +
            ': process_refund: filled (volume) = ' + filled);

        log2laravel('info', my_pump +
            ': process_refund: BEFORE payment_type detection: payment_type=' +
            pumpData['pump' + my_pump].payment_type);

        var is_slave = pumpData['pump' + my_pump].is_slave;
        var isAuth = pumpData['pump' + my_pump].isAuth;

        log2laravel('info', my_pump +
            ': process_refund: is_slave=' +
            is_slave + ', isAuth=' + isAuth);

        /* Refund detection: This is where you detect if there is a
        	refund condition. Refund only happens during PREPAID! */
        /*
        if (pumpData['pump'+my_pump].payment_type != "Postpaid" &&
        	pumpData['pump'+my_pump].is_slave != true &&
        	pumpData['pump'+my_pump].isAuth == true) {

        	log2laravel('info', my_pump +
        		': process_refund: BEFORE refund detection: dose='+
        		dose.toString()+
        		', filled='+filled.toString()+
        		', refund='+refund.toString());


        	if( filled < dose) {
        		log2laravel('info', my_pump +
        			': process_refund: Detected refund='+
        			refund.toString());

        		$('#dose').text(dose);
        		$('#change').text(refund.toString());
        		display_refund(my_pump, refund)
        	}
        }
         */
        if (pumpData['pump' + my_pump].payment_type != "Postpaid") {
            display_refund(my_pump, filled)
        }

        /* Finished processing refund, reset auth if paid */
        if (pumpData['pump' + my_pump].paymentStatus == "Paid") {
            pumpData['pump' + my_pump].isAuth = false;

            log2laravel('info', my_pump +
                ': process_refund: isAuth=' +
                pumpData['pump' + my_pump].isAuth);

            deleteTerminalSyncData(my_pump)
            reset['pump' + my_pump].reset = true;

            log2laravel("info", my_pump +
                ': process_refund: reset=' +
                reset['pump' + my_pump].reset);
        }
    }


    var pump_selected = function(pump_no, forced = false, remove_selected_info = true) {
        log2laravel('info', pump_no + ': BG1 ***** pump_selected() *****');
        log2laravel('info', pump_no + ': BG1 remove_selected_info=' + remove_selected_info);

        /* Early short circuit protection for pump_no==0 */
        if (pump_no == 0) {
            log2laravel('error', 'pump_selected: WTF? pump_no=' + pump_no);
            return;
        }
        var ft_authorized = localStorage.getItem('fulltank_authorized_pump_' + pump_no);
        if(pumpData['pump' + pump_no].paymentStatus != "Paid"){
            localStorage.setItem("pump_data_fuel_" + pump_no, "0.00");
            $('#total-fuel-pump-' + pump_no).text("0.00");
			$('#total-final-filled-' + pump_no).text("0.00");
			$('#total-final-litre-' + pump_no).text("0.00");
            console.log(fulltank_pump_data['pump' + pump_no]);
            if(!fulltank_pump_data['pump' + pump_no].is_slave &&
                (ft_authorized == null || ft_authorized == undefined || ft_authorized == '')){
            	$('#fulltank-button-' + pump_no).removeClass('fulltank-button-disabled');
            }
        }
        var wrapper_div = document.getElementById('cli');
		//console.log("is slave selected pump: " ,pumpData['pump' + pump_no]);
		if (!pumpData['pump' + pump_no].is_slave){
			for(i = 1; i <= {{env('MAX_PUMPS')}}; i++) {
                var overlay2 = document.getElementById('overlayover-' + i);
			    overlay2.hidden = true;
		        //console.log("is slave selected pump: " ,pumpData['pump' + i]);
			    //wrapper_div.setAttribute('style','');
			    //wrapper_div.style.paddingLeft = '15px';
            }
		}
        if (!fulltank_pump_data['pump' + pump_no].is_slave){
			for(i = 1; i <= {{env('MAX_PUMPS')}}; i++) {
                var ft_overlay = document.getElementById('ft-overlay-sa-' + i);
			    ft_overlay.hidden = true;
		        //console.log("is slave selected pump: " ,pumpData['pump' + i]);
			    //wrapper_div.setAttribute('style','');
			    //wrapper_div.style.paddingLeft = '15px';
            }
		}
        var pds_receipt = JSON.parse(localStorage.getItem('pump_receipt_data_' + pump_no));
        if(pds_receipt != null ||pds_receipt != undefined){
            localStorage.setItem('pump_receipt_info', pump_no);
        }
        var pds = JSON.parse(localStorage.getItem('pumpDataState'));
        var dis_payment__status = $('#payment-status-' + pump_no).text();
        var dis_filled__status = $('#total-final-filled-' + pump_no).text();
       /* if(pds == null || pds == undefined){
            dis_payment__status = $('#payment-status-' + pump_id).text();
        }else{
            if(pds_receipt != null ||pds_receipt != undefined){
                dis_payment__status = pds['pump'+ pump_id].paymentStatus;
            }else {
                dis_payment__status = $('#payment-status-' + pump_id).text();
            }

        }*/
        localStorage.setItem('pump_no', pump_no);

        log2laravel('info', pump_no + ': BG1 selected_pump=' + selected_pump + ', forced=' + forced);
        if (selected_pump == pump_no && forced == false)
            return;

        $('#pump-main-block-' + selected_pump).hide();
        $('#pump-main-block-' + pump_no).show();
        $('#button-cash-payment' + selected_pump).hide();
        $('#button-cash-payment' + pump_no).show();
        $('#button-card-payment' + selected_pump).hide();
        $('#button-card-payment' + pump_no).show();
        $(`#button-wallet${selected_pump}`).hide();
        $(`#button-wallet${pump_no}`).show();
        $(`#button-credit-ac${selected_pump}`).hide();
        $(`#button-credit-ac${pump_no}`).show();
        $('#custom_amount_input_' + selected_pump).hide();
        $('#custom_amount_input_' + pump_no).show();
        $('#custom_litre_input_' + selected_pump).hide();
        $('#custom_litre_input_' + pump_no).show();

        //$('#fulltank-button-' + pump_no).removeClass('fulltank-button-disabled');

        for (i = 1; i <= {{ env('MAX_PUMPS') }}; i++) {
            if(i != parseInt(pump_no)){
                if(!$('#fulltank-button-' + i).hasClass('fulltank-button-disabled')){
                     $('#fulltank-button-' + i).addClass('fulltank-button-disabled');
                }
            }
        }
        if (!$('.fuelproductimages-' + pump_no).hasClass("cursor-pointer")) {
            $('.fuelproductimages-' + pump_no).addClass('cursor-pointer');
        }

        $('#active-pump').text(pump_no);

        var prevpump = $('#previous-pumps').attr('pumpno');
        var prevprod = $('#previous-pumps').attr('prod');

        var gen_product = $('img.fuelproductimages-' + pump_no).attr('prod_name');
        var gen_thumb = $('img.fuelproductimages-' + pump_no).attr('prod_img');
        var gen_prod_id = $('img.fuelproductimages-' + pump_no).attr('prod_id');
        var isNozzleDown = localStorage.getItem("isNozzleDown" + pump_no);

        if (prevpump != undefined && prevpump != '' && prevprod != undefined && prevprod != '') {
            if ($('#fuel-grad-thumb-' + prevpump + '-option-' + prevprod).hasClass('selectedimag')) {
                $('#fuel-grad-thumb-' + prevpump + '-option-' + prevprod).removeClass('selectedimag');
            }
        }
        $('#previous-pumps').attr('pumpno', pump_no);
        $('#previous-pumps').attr('prod', gen_prod_id);

        confirmProductSelect(pump_no);

        var nozzle = getNozzleNo(pump_no, gen_prod_id, true);

        if (nozzle) {
            nozzle = JSON.stringify(nozzle.map((e) => e.nozzle_no));
            // product_info = initFuelProduct(pump_no, nozzle, 'yes').price;
        }
        var slcpump = localStorage.getItem('pump_receipt_info');
        if (pumpData) {
            if (pump_no != slcpump || slcpump === undefined) {
                pumpData['pump' + pump_no].dose = '0.00';
                pumpData['pump' + pump_no].paymentStatus = "Not Paid";
                localStorage.setItem("pump_data_fuel_" + pump_no, "0.00");
                $('#total-fuel-pump-' + pump_no).text("0.00");
			    $('#total-final-filled-' + pump_no).text("0.00");
			    $('#total-final-litre-' + pump_no).text("0.00");
                log2laravel('info', pump_no + ': BG1 pump_selected: dose is reset to ' +
                    pumpData['pump' + pump_no].dose);
            } else {
                log2laravel('error', pump_no + ': BG1 pump_selected: pumpData[pump' + pump_no +
                    '] is NULL!!');
            }
        } else {
            log2laravel('error', pump_no + ': BG1 pump_selected: pumpData is NULL!');
        }

        confirmActiveDeliveringPump(pump_no);

        var dis_pump_receipt = localStorage.getItem("pump_receipt_info");

        if (remove_selected_info === true && dis_payment__status !== 'Paid' && dis_pump_receipt === pump_no) {
            localStorage.removeItem("pump_receipt_info");
        }

        if (dis_payment__status == 'Paid' && (isNozzleDown !== 'yes' || isNozzleDown === undefined ||
                isNozzleDown === '')) {
            localStorage.setItem("pump_receipt_info", pump_no);
        }

        var reset_receipt_date = localStorage.getItem("pump_reset_data_" + pump_no);

        if (reset_receipt_date != undefined && reset_receipt_date != '' && remove_selected_info === true) {
            localStorage.removeItem("pump_receipt_data_" + pump_no);
            localStorage.removeItem("pump_reset_data_" + pump_no);
            localStorage.removeItem("isNozzleDown" + pump_no)
        }

        if (!$('#pump-button-' + pump_no).hasClass("poa-button-pump-delivering")) {
            //pump is not delivering
            // confirm pump should not be delivering
            var getPaidPump = $('#paid-pump').val();
            var newPaymentisAuthorized = true;
            var newPaymentOnTriggerAuthorized = true;
            var pumpIsActive = false;
            // localStorage.setItem('isNozzleDown')
            console.log("Get Paid Pumps: ", getPaidPump);
            if (getPaidPump.length > 0) {
                if (getPaidPump.includes(',')) {
                    var splited_paidPumps = getPaidPump.split(',');
                    // splited_paidPumps.push(5);
                    if (splited_paidPumps.includes(pump_no.toString())) {
                        if (newPaymentOnTriggerAuthorized == true) {
                            newPaymentisAuthorized = false;
                        }
                        pumpIsActive = true;
                    }

                } else {
                    if (getPaidPump == pump_no) {
                        if (newPaymentOnTriggerAuthorized == true) {
                            newPaymentisAuthorized = false;
                        }
                        pumpIsActive = true;
                    }
                }
            }
            if (newPaymentisAuthorized) {
                $('.button-number-amount').removeClass('selected_preset_button');
                $('#set-default-preset-button-' + pump_no + '-fifty').addClass('selected_preset_button');

                if (pumpIsActive === false &&
                    (dis_payment__status !== 'Paid' ||
                        isNozzleDown === 'yes')) {

                    $('#payment-status-' + pump_no).text('Not Paid');
                    pumpData['pump' + pump_no].paymentStatus = "Not Paid";
                    localStorage.setItem('pump_data_fuel_' + pump_no, '0.00');
                    var pdata = localStorage.getItem('pump_data_fuel_' + pump_no);
                    $('#total-fuel-pump-' + pump_no).text(pdata);

                    $('#total-final-filled-' + pump_no).text('0.00');
                    $('#total-final-litre-' + pump_no).text('0.00');

                    log2laravel('info', pump_no +
                        ': BG1 pump_selected: #total-fuel-pump-' +
                        pump_no + '=' + $('#total-fuel-pump-' + pump_no).text());

                    localStorage.removeItem("pump_receipt_id_" + pump_no);
                }

                if ($('button.poa-button-number').hasClass('poa-button-cash-selected-disabled')) {
                    $('button.poa-button-number').removeClass('poa-button-cash-selected-disabled');
                }

            } else {
                if (!$('button.poa-button-number').hasClass('poa-button-cash-selected-disabled')) {
                    $('button.poa-button-number').addClass('poa-button-cash-selected-disabled');
                    //console.log("else of new payment")
                }
            }

            if (pumpIsActive === true) {
                $('#payment-status-' + pump_no).text('Paid');
                pumpData['pump' + pump_no].paymentStatus = "Paid";
            }
        }

        var pds = JSON.parse(localStorage.getItem('pumpDataState'))['pump' + pump_no];
        //var dis_payment__status = "";


        /*if(pds){
            if(pds.is_slave == true){
                dis_payment__status = $('#payment-status-' + pump_no).text();
            }else{
                dis_payment__status = pds.paymentStatus;
            }
        }
        */
        if (pumpIsActive === true ||
			$('#pump-button-' + pump_no).hasClass("poa-button-pump-delivering")) {
            $('#payment-status-' + pump_no).text('Paid');
            pumpData['pump' + pump_no].paymentStatus = "Paid";
        }

        selected_pump = pump_no;


        if (selected_pump == 0) {
            return;
        }

        var status = pumpData['pump' + pump_no].status;
        $('#pump-number-main-' + pump_no).text(pump_no);
        $('#pump-status-main-' + pump_no).text(status);

        if (pumpData['pump' + pump_no].preset_type == 'Litre') {
            console.log(`PRESET_TYPE ${pumpData['pump'+pump_no].preset_type}`);
            $('#total_volume-main-' + pump_no).text(pumpData['pump' + pump_no].dose);
            price = pumpData['pump' + pump_no].price_liter;
            $('#total_amount-main-' + pump_no).text(parseFloat(pumpData['pump' + pump_no].dose * price).toFixed(2));
            display_litre_preset(true, pump_no)
        } else {
            console.log(`PRESET_TYPE ${pumpData['pump'+pump_no].preset_type}`);
            display_litre_preset(false, pump_no)
            $('#total_amount-main-' + pump_no).text((pumpData['pump' + pump_no].dose));
        }

        if (pumpData['pump' + pump_no].dose == 0 || status == 'Delivering' || true) {
            $('#authorize-button').attr('class', '');
            $('#authorize-button').addClass('btn poa-authorize-disabled');
        }

        if (reset['pump' + selected_pump].reset == true) {
            $('#authorize-button').attr('class', '');
            $('#authorize-button').addClass('btn poa-authorize-disabled');
            $('.button-number-amount').removeClass('poa-button-number-disabled');
            $('.button-number-amount').addClass('poa-button-number');
            $("#payment-type-message" + selected_pump).html('');
            $("#payment-type-amount" + selected_pump).html('');
            $("#payment-value" + selected_pump).html("Cash Received");
            $("#payment-value" + selected_pump).css("color", "#a0a0a0");
            $("#payment-div-cash-card" + selected_pump).hide();
            $(".payment-div-refund" + selected_pump).hide();
            $(".payment-div-card" + selected_pump).hide();
            $("#payment-div-cash" + selected_pump).show();
            $('.numpad-enter-payment' + selected_pump).removeClass('poa-button-number-payment-enter');
            $('.numpad-enter-payment' + selected_pump).addClass('poa-button-number-payment-enter-disabled');
            reset['pump' + selected_pump].reset = false;
            reset['pump' + selected_pump].status = "";
            /*
            //pumpData['pump'+selected_pump].dose = "0.00";
            //$('#total_amount-main-'+selected_pump).text(pumpData['pump'+pump_id].dose);
            //$("#product-select-pump-"+selected_pump).css('display','none');
            //$('#fuel-grad-name-' + selected_pump).text("");
            //$('#fuel-grad-thumb-' + selected_pump).css('display', 'none');
            //pumpData['pump' + selected_pump].product = "";
            */
            $("#payment-type-paid-right" + selected_pump).html("");
            $("#payment-type-amount" + selected_pump).html("");
            $("#payment-amount-card-amount" + selected_pump).html("");
            $("#payment-type-amount" + selected_pump).html("");
            pumpData['pump' + selected_pump].payment_type = "Prepaid";

            /*
            //pumpData['pump'+selected_pump].receipt_id = '';
            //display_litre_preset(false, selected_pump);
            //removePaymentState(selected_pump)
            */

            disable_payment_btns();

            /*
            //pumpData['pump'+selected_pump].preset_type = "{{ empty($company->currency->code) ? 'MYR' : $company->currency->code }}"
            */

            $(`#custom_litre_input_${selected_pump}`).removeAttr('disabled');
            $(`#custom_amount_input_${selected_pump}`).removeAttr('disabled');
            pumpData['pump' + selected_pump].isNozzleUp = false;
            pumpData['pump' + selected_pump].isAuth = false;
            console.log("RESET selected pump: true");
            //deleteTerminalSyncData(selected_pump);
        }

        log2laravel('info', selected_pump +
            ': pump_selected: RESET isAuth=' +
            pumpData['pump' + selected_pump].isAuth);

        log2laravel('info', selected_pump +
            ': pump_selected: RESET isNozzleUp=' +
            pumpData['pump' + selected_pump].isNozzleUp);

        log2laravel('info', selected_pump +
            ': pump_selected: RESET paymentStatus=' +
            pumpData['pump' + selected_pump].paymentStatus);

        if (status == 'Delivering' ||
            (pumpData['pump' + selected_pump].isAuth == true &&
                pumpData['pump' + selected_pump].isNozzleUp == false &&
                pumpData['pump' + selected_pump].paymentStatus == "Paid") ||
            (pumpData['pump' + selected_pump].isNozzleUp == true &&
                (pumpData['pump' + selected_pump].isAuth == true &&
                    pumpData['pump' + selected_pump].paymentStatus != "Not Paid"))) {
            $('.button-number-amount').removeClass('poa-button-number');
            $('.button-number-amount').addClass('poa-button-number-disabled');
            // $(`#custom_litre_input_${selected_pump}`).attr('disabled', true);
            // $(`#custom_amount_input_${selected_pump}`).attr('disabled', true);
            console.log("PS Preset disabled");

        } else {
            $('.button-number-amount').removeClass('poa-button-number-disabled');
            $('.button-number-amount').addClass('poa-button-number');
            $(`#custom_litre_input_${selected_pump}`).removeAttr('disabled');
            $(`#custom_amount_input_${selected_pump}`).removeAttr('disabled');
            console.log("PS preset enabled");
        }

        if (status != 'Delivering' && pumpData['pump' + selected_pump].isAuth == true &&
            pumpData['pump' + selected_pump].paymentStatus == "Not Paid" &&
            pumpData['pump' + selected_pump].product)
            //enable_payment_btns();

            if (pumpData['pump' + selected_pump].status == "Delivering") {
                $('.button-number-amount').removeClass('poa-button-number');
                $('.button-number-amount').addClass('poa-button-number-disabled');
                $('#authorize-button').attr('class', '');
                $('#authorize-button').addClass('btn poa-authorize-disabled');
                // $(`#custom_litre_input_${selected_pump}`).attr('disabled', true);
                // $(`#custom_amount_input_${selected_pump}`).attr('disabled', true);
            }

        $("#custom_amount_btn").addClass('custom-preset-disable');
        $("#custom_amount_btn").removeClass('poa-button-preset');
        $("#custom_litre_btn").addClass('custom-preset-disable');
        $("#custom_litre_btn").removeClass('poa-button-preset');

        $(`#custom_amount_input_${selected_pump}_buffer`).val('');
        $(`#custom_amount_input_${selected_pump}`).val('');
        $(`#custom_litre_input_${selected_pump}_buffer`).val('');
        $(`#custom_litre_input_${selected_pump}`).val('');


        if (pumpData['pump' + selected_pump].product) {
            $('#fuel-grad-name-' + selected_pump).text(pumpData['pump' + selected_pump].product);
            $('#fuel-grad-thumb-' + selected_pump).attr('src', pumpData['pump' + selected_pump].product_thumbnail);
            $('#fuel-grad-thumb-' + selected_pump).css('display', 'inline-flex');
        } else {
            $('#fuel-grad-name-' + selected_pump).text('');
            $('#fuel-grad-thumb-' + selected_pump).css('display', 'none');
        }

        if (status != 'Delivering' && pumpData['pump' + selected_pump].isAuth == true &&
            pumpData['pump' + selected_pump].paymentStatus == "Not Paid" &&
            !pumpData['pump' + selected_pump].product)
            $("#product-select-pump-" + selected_pump).css('display', 'flex');

        ///selecting very first product of the pump

        $('#product-select-pump-' + selected_pump + ' > span:first-child > img').trigger("click");

        //// end

        pump_data_update(selected_pump);
        // if (pumpIsActive === false) {
        // 	$(`#payment-status-${selected_pump}`).html(pumpData['pump'+selected_pump].paymentStatus)
        // }

        // var pump_receipt = localStorage.getItem("pump_receipt_info");

        // if (pump_receipt == selected_pump) {
        // 	$('#payment-status-'+selected_pump).text('Paid');
        // }
        pds_filled = "0.00";
        var sp_receipt = JSON.parse(localStorage.getItem('pump_receipt_data_' + selected_pump));
        if (sp_receipt == null || sp_receipt == undefined) {
            pds_filled = "0.00";
            // sppayStat = "Not Paid";
        } else {
            pds_filled = sp_receipt.amount;
        }
        if(dis_payment__status === "Paid"){
            if(!$('#fulltank-button-' + selected_pump).hasClass('fulltank-button-disabled')){
                $('#fulltank-button-' + selected_pump).addClass('fulltank-button-disabled');
            }
        }

        if (dis_payment__status === "Paid" && dis_filled__status == "0.00" && (isNozzleDown != 'yes')) {
            if ($('.cancel-pump-number-' + selected_pump).hasClass('poa-button-number-payment-cancel-disabled')) {
				// PDH Squidster: This is the cause of the Cancel during Delivery
                $('.cancel-pump-number-' + selected_pump).removeClass('poa-button-number-payment-cancel-disabled');
                $('.cancel-pump-number-' + selected_pump).addClass('poa-button-number-payment-cancel');
                $('button.poa-button-number').addClass('poa-button-cash-selected-disabled');
                $('button.poa-button-number').removeClass('poa-button-cash-selected');

                $('.give-me-left-' + selected_pump).addClass('left-7');
                $('.cancel-pump-number-' + selected_pump).addClass('btn-size-70');
            }

            if (!$('.button-number-amount').hasClass('poa-button-number-disabled')) {
                $('.button-number-amount').addClass('poa-button-number-disabled');
                $('.button-number-amount').removeClass('poa-button-number');
            }

            disable_payment_btns();


        } else {
            if (!$('.cancel-pump-number-' + selected_pump).hasClass('poa-button-number-payment-cancel-disabled')) {
                $('.cancel-pump-number-' + selected_pump).addClass('poa-button-number-payment-cancel-disabled');
                $('.give-me-left-' + selected_pump).removeClass('left-7');
                $('.cancel-pump-number-' + selected_pump).removeClass('btn-size-70');
                $('button.poa-button-number').removeClass('poa-button-cash-selected-disabled');
            }

            if ($('.button-number-amount').hasClass('poa-button-cash-selected-disabled')) {
                $('.button-number-amount').removeClass('poa-button-number-disabled');
                $('.button-number-amount').removeClass('poa-button-cash-selected-disabled');
                $('.button-number-amount').addClass('poa-button-number');
            }

            enable_payment_btns();
        }

        console.log(dis_pump_receipt, isNozzleDown, selected_pump);
        if (isNozzleDown == 'yes' && dis_pump_receipt == selected_pump) {
            deleteTerminalSyncData(selected_pump);
            localStorage.removeItem("pump_receipt_info");
            //console.log('removed');
        }
        var nozzle_down_selected_pump = localStorage.getItem('isNozzleDown' + selected_pump);
        if (nozzle_down_selected_pump == 'yes' && dis_payment__status == "Paid" && parseFloat(dis_filled__status).toFixed(2) > 0) {
            $('.cancel-pump-number-' + selected_pump).addClass('poa-button-number-payment-cancel-disabled');
            $('.give-me-left-' + selected_pump).removeClass('left-7');
            $('.cancel-pump-number-' + selected_pump).removeClass('btn-size-70');
            $('button.poa-button-number').removeClass('poa-button-cash-selected-disabled');
            $('.button-number-amount').removeClass('poa-button-number-disabled');
            $('.button-number-amount').removeClass('poa-button-cash-selected-disabled');
            $('.button-number-amount').addClass('poa-button-number');
            enable_payment_btns();
        }
        var nozzle_down_selected_pump1 = localStorage.getItem('isNozzleDown' + pump_no);

        if (dis_payment__status == "Not Paid" && dis_filled__status == "0.00" &&
            (nozzle_down_selected_pump1 == 'yes' || nozzle_down_selected_pump1 == null || nozzle_down_selected_pump1 == undefined)) {
            pumpData['pump' + pump_no].dose = '0.00';
            pumpData['pump' + pump_no].paymentStatus = "Not Paid";
            $('.cancel-pump-number-' + pump_no).addClass('poa-button-number-payment-cancel-disabled');
            $('.give-me-left-' + pump_no).removeClass('left-7');
            $('.cancel-pump-number-' + pump_no).removeClass('btn-size-70');
            $('button.poa-button-number').removeClass('poa-button-cash-selected-disabled');
            $('.button-number-amount').removeClass('poa-button-number-disabled');
            $('.button-number-amount').removeClass('poa-button-cash-selected-disabled');
            $('.button-number-amount').addClass('poa-button-number');
            enable_payment_btns();
        }
        // preset_button(6);
    }

    function confirmProductSelect(pump_id) {
        var activepump = $('span#active-pump').text();

        if (pump_id != activepump) {
            if ($('.fuelproductimages-' + pump_id).hasClass("cursor-pointer")) {
                $('.fuelproductimages-' + pump_id).removeClass('cursor-pointer');
            }
        } else {
            if (!$('.fuelproductimages-' + pump_id).hasClass("cursor-pointer")) {
                $('.fuelproductimages-' + pump_id).addClass('cursor-pointer');
            }
        }
    }

    function hideModal() {
        $('#modalMessage').modal('hide');
    }

    function voidPumpAndDeAuthorize(pump_no) {
        receipt_id = pumpData['pump' + pump_no].receipt_id;

		log2laravel('info',
			'***** voidPumpAndDeAuthorize: receipt_id=' + receipt_id);

        generate_voidPdf(pump_no);

        void_receipt(pump_no);
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
        // enable_payment_btns();
        $('.button-number-amount').removeClass('poa-button-number-disabled');
        $('.button-number-amount').removeClass('poa-button-cash-selected-disabled');
        $('.button-number-amount').addClass('poa-button-number');
        $('#payment-status-' + pump_no).text('Not Paid');
        pumpData['pump' + pump_no].paymentStatus = "Not Paid";

        // FP Fuel is being updated here
        localStorage.setItem('pump_data_fuel_' + pump_no, '0.00');
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

        disable_payment_btns()
        numberpad_disable(pump_no)
        // $(`#input-cash${pump_no}`).hide();
         $('#pump-number-main-'+ pump_no).html('');

        //effect of nozzle down and cancel on new ccover is similar
        update_ui_on_nozzle_down(pump_no)
    }


    function confirmActiveDeliveringPump(pump_id) {
        for (i = 1; i <= {{ env('MAX_PUMPS') }}; i++) { // this was hard coded. Needs revision later
            var gen_prod_id = $('img.fuelproductimages-' + i).attr('prod_id');

            if (!$('#pump-button-' + i).hasClass("poa-button-pump-delivering")) {
                $('#fuel-grad-thumb-' + i + '-option-' + gen_prod_id).removeClass('selectedimag');
            }

            if (pump_id == i) {
                $('#fuel-grad-thumb-' + i + '-option-' + gen_prod_id).addClass('selectedimag');
            } else {
                $('#fuel-grad-thumb-' + i + '-option-' + gen_prod_id).removeClass('selectedimag');
            }
        }

    }


    function set_amount(dose, id = 'fifty') {
    	console.log('**DOSE**', dose)
        log2laravel('info', selected_pump +
            ': ***** BG1 set_amount(' + dose + ') *****');

        dose = parseFloat(dose).toFixed(2);

        log2laravel('info', selected_pump + ': BG1 dose = ' + dose);

        $('.button-number-amount').removeClass('selected_preset_button');
        $('#set-default-preset-button-' + selected_pump + '-' + id).addClass('selected_preset_button');

        pumpData['pump' + selected_pump].preset_type =
            "{{ empty($company->currency->code) ? 'MYR' : $company->currency->code }}"

        pumpData['pump' + selected_pump].dose = dose;

        log2laravel('info', selected_pump + ': set_amount: BG1 dose=' +
            pumpData['pump' + selected_pump].dose);

        $('#total_amount-main-' + selected_pump).text(dose);

        log2laravel('info', selected_pump + ': set_amount: BG1 dose=' + dose);

        // FP Fuel is being updated here
        localStorage.setItem('pump_data_fuel_' + selected_pump, dose);
        var pdata = localStorage.getItem('pump_data_fuel_' + selected_pump);
        $('#total-fuel-pump-' + selected_pump).text(pdata);

        log2laravel('info', selected_pump + ': set_amount: BG1 total-fuel-pump-' +
            selected_pump + '=' + $('#total-fuel-pump-' + selected_pump).text());

        update_payment_table(selected_pump);
        enable_payment_btns()

    }


    var isClickPumpAuth = false;

	/* OBSOLETED
    function v3_pump_auth(pump_no, product_id) {
        log2laravel('info', pump_no + ': BG1 ***** v3_pump_auth: ' +
            pump_no + ', product_id=' + product_id);

        var nozzle = getNozzleNo(pump_no, product_id, true);
        var fuel_grade_id = getFuelGradeId(product_id)


        if (nozzle && fuel_grade_id) {
            var type = "Amount";
            var dose = pumpData['pump' + pump_no].dose;
            var ipaddr = "{{ env('PTS_IPADDR') }}";

            $.ajax({
                url: '/pump-authorize-fuel-grade/' + pump_no + '/' + type +
                    '/' + dose + '/' + ipaddr + '/null/' + fuel_grade_id,
                type: "GET",
                dataType: "JSON",
                success: function(response) {

                    log2laravel('info', pump_no +
                        ': ***** v3_pump_auth: SUCCESS from pump-authorize-fuel-grade *****');

                    store_txid(response, dose);

                    pumpData['pump' + pump_no].amount = "0.00";
                    isClickPumpAuth = false;

                },
                error: function(response) {
                    console.log(JSON.stringify(response));
                    log2laravel('error', pump_no +
                        ': ***** v3_pump_auth: ERROR: ' +
                        JSON.stringify(response));
                }
            });
        }
    }
	*/



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


    function select_cash() {
        show_select_pump_modal('select_cash()')
        if (selected_pump != 0) {
            $('#button-cash-payment' + selected_pump).addClass('selected_preset_button');
            numpad_enable();
            $('.finish-button-' + selected_pump).removeClass('opos-topup-button');
            $('.finish-button-' + selected_pump).addClass('poa-finish-button-disabled');
            $(`#input-cash${selected_pump}`).removeAttr('disabled');
            $(`#input-cash${selected_pump}`).show();
            $('#change-val-calculated-' + selected_pump).html('0.00');

            //$("#payment-div-cash"+selected_pump+" div").not(`#payment-div-cash${selected_pump} > div > div.col-md-12.mt-auto.w-100.mr-0.pr-1.pl-0 > div.d-flex.justify-content-end`).addClass("justify-content-center");

            dis_cash['pump' + selected_pump].payment_type = "cash";
            dis_cash['pump' + selected_pump].dis_cash = "";
            $(`#buffer-input-cash${selected_pump}`).val('');
            $(`#input-cash${selected_pump}`).val('');
            $("#payment-value" + selected_pump).html("Cash Received");
            $("#payment-value" + selected_pump).css("color", "#a0a0a0");
            $("#payment-type-paid-right" + selected_pump).html("");
            $("#payment-div-cash-card" + selected_pump).hide();
            $(".payment-div-refund" + selected_pump).hide();
            $(".payment-div-card" + selected_pump).hide();
            $("#payment-div-cash" + selected_pump).show();
            $("#payment-type-message" + selected_pump).html("");
            $("#payment-type-amount" + selected_pump).html("");
            $("#payment-amount-card-amount" + selected_pump).html("");
            $("#payment-type-amount" + selected_pump).html("");
            $('.numpad-enter-payment' + selected_pump).removeClass('poa-button-number-payment-enter');
            $('.numpad-enter-payment' + selected_pump).addClass('poa-button-number-payment-enter-disabled');


            let tocheck = $(`#prd-myr-${selected_pump}`).text();

            if (tocheck > 0) {
                $(`#input-cash${selected_pump}`).focus();
            } else {
                $(`#input-cash${selected_pump}`).prop('disabled', true)
            }

            $(`#input-cash${selected_pump}`).click();

            if (!pumpData['pump' + selected_pump].product) {
                $('#fuel-grad-name-' + selected_pump).text("");
                $('#fuel-grad-thumb-' + selected_pump).css('display', 'none');
                $("#product-select-pump-" + selected_pump).css('display', 'flex');
                if (pumpData['pump' + selected_pump].preset_type == 'Litre' && pumpData['pump' + selected_pump]
                    .payment_type == "Prepaid")
                    numpad_disable();
            }
        }
    }


    function select_credit_ac() {

        show_select_pump_modal('select_credit_ac()')
        if (selected_pump != 0) {

            $('#button-cash-payment' + selected_pump).removeClass('selected_preset_button');

            $('.finish-button-' + selected_pump).removeClass('poa-finish-button-disabled');
            $('.finish-button-' + selected_pump).addClass('opos-topup-button');
            $(`#buffer-input-cash${selected_pump}`).val('');
            $(`#input-cash${selected_pump}`).val('');
            // $(`#input-cash${selected_pump}`).hide();
            dis_cash['pump' + selected_pump].dis_cash = "";
            dis_cash['pump' + selected_pump].payment_type = "creditac";
            $(`#change-val-calculated-${selected_pump}`).text('0.00');
            //$("#payment-div-cash"+selected_pump).hide();
            $(".payment-div-refund" + selected_pump).hide();
            $("#payment-div-cash-card" + selected_pump).hide();
            check_enter()
        }
    }


	function pop_up_wallet_instruction() {
	}


	function scan_wallet_qrcode() {
	}

    function select_wallet() {

        if (selected_pump != 0) {
			/* Here you pop up the modal */
			pop_up_wallet_instruction();

			/* Here you scan QR code */
			scan_wallet_qrcode();


			/* Here you parse which wallet has been scanned */
            $('#button-cash-payment' + selected_pump).removeClass('selected_preset_button');
            $('.finish-button-' + selected_pump).removeClass('poa-finish-button-disabled');
            $('.finish-button-' + selected_pump).addClass('opos-topup-button');
            $(`#buffer-input-cash${selected_pump}`).val('');
            $(`#input-cash${selected_pump}`).val('');
            // $(`#input-cash${selected_pump}`).hide();
            dis_cash['pump' + selected_pump].dis_cash = "";
            dis_cash['pump' + selected_pump].payment_type = "wallet";
            $(`#change-val-calculated-${selected_pump}`).text('0.00');
            //$("#payment-div-cash"+selected_pump).hide();
            $(".payment-div-refund" + selected_pump).hide();
            $("#payment-div-cash-card" + selected_pump).hide();

            check_enter()
        }
    }

    function select_credit_card() {
        let prd_selected = show_select_pump_modal()

        if (prd_selected) {
            if (selected_pump != 0) {
                $('#button-cash-payment' + selected_pump).removeClass('selected_preset_button');
                numpad_enable();
                $('.finish-button-' + selected_pump).removeClass('poa-finish-button-disabled');
                $('.finish-button-' + selected_pump).addClass('opos-topup-button');
                // $(`#input-cash${selected_pump}`).hide();
                rev_cash = parseFloat(pumpData['pump' + selected_pump].dose)
                $(`#change-val-calculated-${selected_pump}`).text('0.00');
                $(`#buffer-input-cash${selected_pump}`).val('');
                $(`#input-cash${selected_pump}`).val('');
                dis_cash['pump' + selected_pump].dis_cash = "";
                dis_cash['pump' + selected_pump].payment_type = "card";
                $("#payment-value-card" + selected_pump).html("");
                //$("#payment-div-cash"+selected_pump).hide();
                $(".payment-div-refund" + selected_pump).hide();
                $("#payment-div-cash-card" + selected_pump).hide();
                $(".payment-div-card" + selected_pump).show();
                if (pumpData['pump' + selected_pump].payment_type == "Postpaid") {
                    var amount = pumpData['pump' + selected_pump].amount;
                    amount = ((5 * Math.round((amount * 100) / 5)) / 100).toFixed(2);

                } else {}

                $('.numpad-enter-payment' + selected_pump).removeClass('poa-button-number-payment-enter');
                $('.numpad-enter-payment' + selected_pump).addClass('poa-button-number-payment-enter-disabled');
                $("#payment-amount-card-amount" + selected_pump).show();

                if (!pumpData['pump' + selected_pump].product) {
                    $('#fuel-grad-name-' + selected_pump).text("");
                    $('#fuel-grad-thumb-' + selected_pump).css('display', 'none');
                    $("#product-select-pump-" + selected_pump).css('display', 'flex');
                    if (pumpData['pump' + selected_pump].preset_type == 'Litre')
                        numpad_disable();
                }

                check_enter()
            }
        }
    }


    function select_cash_card() {
        let prd_selected = show_select_pump_modal()

        if (prd_selected) {
            if (selected_pump != 0) {
                numpad_enable();
                dis_cash['pump' + selected_pump].dis_cash = "";
                dis_cash['pump' + selected_pump].payment_type = "card";
                $("#payment-div-cash" + selected_pump).hide();
                $(".payment-div-card" + selected_pump).hide();
                $(".payment-div-refund" + selected_pump).hide();
                $("#payment-div-cash-card" + selected_pump).show();
                $("#payment-type-message" + selected_pump).html("More Payment");
                $("#payment-type-amount" + selected_pump).html(pumpData['pump' + selected_pump].dose);
                $("#payment-amount-card-amount" + selected_pump).hide();
                $("#payment-type-amount" + selected_pump).html("");

                $('#fuel-grad-name-' + selected_pump).text("");
                $('#fuel-grad-thumb-' + selected_pump).css('display', 'none');
                $("#product-select-pump-" + selected_pump).css('display', 'flex');
                //}
            }
        }
    }


    function set_cash(amount) {
        console.log(`PT tx type: ${pumpData['pump'+selected_pump].payment_type}`);
        console.log(`PT payment type: ${dis_cash['pump'+selected_pump].payment_type}`);

        if (amount == "zero") {
            if (dis_cash['pump' + selected_pump].payment_type == "cash") {
                $('.numpad-number-payment' + selected_pump).removeClass('poa-button-number-payment-disabled');
                $('.numpad-number-payment' + selected_pump).addClass('poa-button-number-payment');
                //$("#payment-div-cash"+selected_pump+" div").addClass("justify-content-center");
                $("#payment-value" + selected_pump).css("color", "#a0a0a0");
                dis_cash['pump' + selected_pump].dis_cash = "";
                $("#payment-value" + selected_pump).html("Cash Received");
                $("#payment-type-message" + selected_pump).html("");
                $("#payment-type-amount" + selected_pump).html("");
                $("#payment-type-paid-right" + selected_pump).html("");
                $("#payment-amount-card-amount" + selected_pump).html("");
                $("#payment-type-amount" + selected_pump).html("");
                $('.numpad-enter-payment' + selected_pump).removeClass('poa-button-number-payment-enter');
                $('.numpad-enter-payment' + selected_pump).addClass('poa-button-number-payment-enter-disabled');
            } else if (dis_cash['pump' + selected_pump].payment_type == "card") {
                dis_cash['pump' + selected_pump].dis_cash = "";
                $("#payment-value-card" + selected_pump).html("");
                $('.numpad-enter-payment' + selected_pump).removeClass('poa-button-number-payment-enter');
                $('.numpad-enter-payment' + selected_pump).addClass('poa-button-number-payment-enter-disabled');
                $('.numpad-number-payment' + selected_pump).removeClass('poa-button-number-payment-disabled');
                $('.numpad-number-payment' + selected_pump).addClass('poa-button-number-payment');
            }

            $(`#buffer-input-cash${selected_pump}`).val('');
            $(`#input-cash${selected_pump}`).val('');
            $(`#input-cash${selected_pump}`).removeAttr('disabled');
            $('.finish-button-' + selected_pump).removeClass('opos-topup-button');
            $('.finish-button-' + selected_pump).addClass('poa-finish-button-disabled');
            $(`#change-val-calculated-${selected_pump}`).text('0.00');
        } else {
            if (dis_cash['pump' + selected_pump].payment_type == "cash") {
                $("#payment-div-cash" + selected_pump + " div").removeClass("justify-content-center");
                $("#payment-value" + selected_pump).css("color", "black");
                dis_cash['pump' + selected_pump].dis_cash = dis_cash['pump' + selected_pump].dis_cash + amount;
                $("#payment-value" + selected_pump).html((parseFloat(dis_cash['pump' + selected_pump].dis_cash) / 100)
                    .toFixed(2));
                calculate_change();

                dis_cash_ = (parseFloat(dis_cash['pump' + selected_pump].dis_cash) / 100).toFixed(2);
                if (pumpData['pump' + selected_pump].payment_type == "Postpaid") {
                    console.log("PT postpaid matched");
                    console.log(`PT amount ${pumpData['pump'+selected_pump].amount}`);
                    var amount_total = pumpData['pump' + selected_pump].amount;
                    amount_total = ((5 * Math.round((amount_total * 100) / 5)) / 100).toFixed(2);
                    if (dis_cash_ >= parseFloat(amount_total)) {
                        $('.numpad-number-payment' + selected_pump).
                        removeClass('poa-button-number-payment');
                        $('.numpad-number-payment' + selected_pump).
                        addClass('poa-button-number-payment-disabled');
                        if (pumpData['pump' + selected_pump].product) {
                            $('.numpad-enter-payment' + selected_pump).
                            removeClass('poa-button-number-payment-enter-disabled');
                            $('.numpad-enter-payment' + selected_pump).
                            addClass('poa-button-number-payment-enter');
                        }
                    }

                } else {

                    rev_cash = parseFloat(pumpData['pump' + selected_pump].dose)
                    if (dis_cash_ >= rev_cash) {
                        $('.numpad-number-payment' + selected_pump).
                        removeClass('poa-button-number-payment');
                        $('.numpad-number-payment' + selected_pump).
                        addClass('poa-button-number-payment-disabled');
                        if (pumpData['pump' + selected_pump].product) {
                            $('.numpad-enter-payment' + selected_pump).
                            removeClass('poa-button-number-payment-enter-disabled');
                            $('.numpad-enter-payment' + selected_pump).
                            addClass('poa-button-number-payment-enter');
                        }
                    }
                }

            } else if (dis_cash['pump' + selected_pump].payment_type == "card") {
                if (dis_cash['pump' + selected_pump].dis_cash.length < 4) {
                    dis_cash['pump' + selected_pump].dis_cash = dis_cash['pump' + selected_pump].dis_cash + amount;
                    $("#payment-value-card" + selected_pump).html(dis_cash['pump' + selected_pump].dis_cash);
                }

                if (dis_cash['pump' + selected_pump].dis_cash.length == 4) {
                    $('.numpad-number-payment' + selected_pump).removeClass('poa-button-number-payment');
                    $('.numpad-number-payment' + selected_pump).addClass('poa-button-number-payment-disabled');
                    if (pumpData['pump' + selected_pump].product) {
                        $('.numpad-enter-payment' + selected_pump).removeClass(
                            'poa-button-number-payment-enter-disabled');
                        $('.numpad-enter-payment' + selected_pump).addClass('poa-button-number-payment-enter');
                    }
                }
            }
        }
    }

    function show_select_pump_modal() {
        let screen_a_product = $('div[id *="table-PRODUCT-"]').text().replace(/\s+/g, " ");
        let screen_a_price = $('div[id *="table-PRICE-"]').text().replace(/\s+/g, " ");
        let screen_a_qty = $('div[id *="table-QTY-"]').text().replace(/\s+/g, " ");

        log2laravel('debug', '***show_select_pump_modal***');
        log2laravel('debug', 'show_select_pump_modal: screen_a_product ='+JSON.stringify(screen_a_product));
        log2laravel('debug', 'show_select_pump_modal: screen_a_price ='+JSON.stringify(screen_a_price));
        log2laravel('debug', 'show_select_pump_modal: screen_a_qty ='+JSON.stringify(screen_a_qty));

        if (screen_a_product == '' || screen_a_product == null || screen_a_product == undefined) {
            $("#select_product_modal").modal('show');
            setTimeout(function(){
                $("#select_product_modal").modal('hide');
            }, 3000)
            log2laravel('error', 'Product Not Selected: screen_a_product ='+screen_a_product);
            return false
        }
        return true
    }

    function check_enter() {
        if (dis_cash['pump' + selected_pump].payment_type == "cash") {
            dis_cash_ = (parseFloat(dis_cash['pump' + selected_pump].dis_cash) / 100).toFixed(2);
            if (pumpData['pump' + selected_pump].payment_type == "Postpaid") {
                var amount_total = pumpData['pump' + selected_pump].amount;
                amount_total = ((5 * Math.round((amount_total * 100) / 5)) / 100).toFixed(2);
                if (dis_cash_ >= parseFloat(amount_total)) {
                    if (pumpData['pump' + selected_pump].product) {
                        $('.numpad-enter-payment' + selected_pump).
                        removeClass('poa-button-number-payment-enter-disabled');

                        $('.numpad-enter-payment' + selected_pump).
                        addClass('poa-button-number-payment-enter');

                        $(`#input-cash${selected_pump}`).attr('disabled', true);
                    }
                }

            } else {

                if (pumpData['pump' + selected_pump].preset_type == 'Litre') {
                    rev_cash = parseFloat($("#total_amount-main-" + selected_pump).text())
                } else {
                    rev_cash = parseFloat(pumpData['pump' + selected_pump].dose)
                }
                if (dis_cash_ >= rev_cash) {
                    if (pumpData['pump' + selected_pump].product) {
                        $('.numpad-enter-payment' + selected_pump).
                        removeClass('poa-button-number-payment-enter-disabled');

                        $('.numpad-enter-payment' + selected_pump).
                        addClass('poa-button-number-payment-enter');

                        $(`#input-cash${selected_pump}`).attr('disabled', true);
                    }
                }
            }

        } else if (dis_cash['pump' + selected_pump].payment_type == "card" ||
            dis_cash['pump' + selected_pump].payment_type == "wallet" ||
            dis_cash['pump' + selected_pump].payment_type == "creditac") {
            if (pumpData['pump' + selected_pump].product) {
                $('.numpad-enter-payment' + selected_pump).
                removeClass('poa-button-number-payment-enter-disabled');

                $('.numpad-enter-payment' + selected_pump).
                addClass('poa-button-number-payment-enter');
            }
        }
    }

    var isClickProcessEnter = false;

    function void_receipt(pump_no) {
        receipt_id = pumpData['pump' + pump_no].receipt_id;
        console.log("fromVoid: ", receipt_id)
        if (receipt_id == '') {
            receipt_id = localStorage.getItem("pump_receipt_id_" + pump_no);
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
                localStorage.removeItem('update-screen-e-landing');
                localStorage.removeItem('pump_receipt_data_' + pump_no);
                //window.location.reload();
                //clear_local_storage();
            },
            error: function(e) {
                console.log('VR ' + JSON.stringify(e));
            }
        });
    }


    function fuel_generate_pdf(receipt_id){
        console.log('PR ');

        // Generate the receipt PDF
        $.ajax({
            url: "{{ route('receipt.generatepdf') }}",
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


    function generate_voidPdf(pump_no){
        // Generate the void PDF
        receipt_id = pumpData['pump' + pump_no].receipt_id;

        if (receipt_id == '' || receipt_id == null) {
            receipt_id = localStorage.getItem("pump_receipt_id_" + pump_no);
			log2laravel('generate_voidPdf: recovered receipt_id=' + receipt_id);
        }

        $.ajax({
            url: "{{ route('receipt.generatevoidpdf') }}",
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
    function print_receipt(receipt_id) {

        console.log('PR print_receipt()');
        console.log('PR receipt_id=' + JSON.stringify(receipt_id));

        $.ajax({
            url: "/fuel_print",
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
                console.log('PR ' + JSON.stringify(response));

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



    function calculate_change() {
        dis_cash_ = (parseFloat(dis_cash['pump' + selected_pump].dis_cash) / 100).toFixed(2);

        if (pumpData['pump' + selected_pump].payment_type == "Postpaid") {

            var amount_total = pumpData['pump' + selected_pump].amount;
            amount_total = ((5 * Math.round((amount_total * 100) / 5)) / 100).toFixed(2);
            var change_amount = dis_cash_ - amount_total;

        } else {

            var change_amount = dis_cash_ - parseFloat($('#total_amount-main-' + selected_pump).text().replace(/,/g,
                ''))
        }

        $(`#change-val-calculated-${selected_pump}`).text(change_amount.toFixed(2));

        if (change_amount >= 0) {
            $('.finish-button-' + selected_pump).removeClass('poa-finish-button-disabled');
            $('.finish-button-' + selected_pump).addClass('opos-topup-button');
        } else {
            $('.finish-button-' + selected_pump).removeClass('opos-topup-button');
            $('.finish-button-' + selected_pump).addClass('poa-finish-button-disabled');
        }

    }


    function numpad_enable() {
        $('.numpad-number-payment' + selected_pump).removeClass('poa-button-number-payment-disabled');
        $('.numpad-number-payment' + selected_pump).addClass('poa-button-number-payment');
        $('.numpad-zero-payment' + selected_pump).removeClass('poa-button-number-payment-zero-disabled');
        $('.numpad-zero-payment' + selected_pump).addClass('poa-button-number-payment-zero');

        $('.numpad-enter-payment' + selected_pump).removeClass('poa-button-number-payment-enter-disabled');
        $('.numpad-enter-payment' + selected_pump).addClass('poa-button-number-payment-enter');
        // $(`#input-cash${selected_pump}`).removeAttr('disabled');


    }


    function numpad_disable() {
        $('.numpad-number-payment' + selected_pump).removeClass('poa-button-number-payment');
        $('.numpad-number-payment' + selected_pump).addClass('poa-button-number-payment-disabled');
        $('.numpad-zero-payment' + selected_pump).removeClass('poa-button-number-payment-zero');
        $('.numpad-zero-payment' + selected_pump).addClass('poa-button-number-payment-zero-disabled');
        $('.numpad-refund-payment' + selected_pump).removeClass('poa-button-number-payment-refund');
        $('.numpad-refund-payment' + selected_pump).addClass('poa-button-number-payment-refund-disabled');
        $('.numpad-enter-payment' + selected_pump).removeClass('poa-button-number-payment-enter');
        $('.numpad-enter-payment' + selected_pump).addClass('poa-button-number-payment-enter-disabled');
        $(`#input-cash${selected_pump}`).attr('disabled', true);

    }


    function enable_payment_btns() {
        $('#button-cash-payment' + selected_pump).removeClass('poa-button-cash-disabled');
        $('#button-cash-payment' + selected_pump).addClass('poa-button-cash');
        $('#button-card-payment' + selected_pump).removeClass('poa-button-credit-card-disabled');
        $('#button-card-payment' + selected_pump).addClass('poa-button-credit-card');
        $('#button-cash-card-payment').removeClass('poa-button-cash-card-disabled');
        $('#button-cash-card-payment').addClass('poa-button-cash-card');
        $(`#button-wallet${selected_pump}`).removeClass('opos-button-wallet-disabled')

        $('#button-credit-ac' + selected_pump).removeClass('opos-button-credit-ac-disabled');
        $('#button-credit-ac' + selected_pump).addClass('opos-button-credit-ac');

        $(`#input-cash${selected_pump}`).show();
    }


    function disable_payment_btns() {
        $('#button-cash-payment' + selected_pump).addClass('poa-button-cash-disabled');
        $('#button-cash-payment' + selected_pump).removeClass('poa-button-cash');
        $('#button-card-payment' + selected_pump).addClass('poa-button-credit-card-disabled');
        $('#button-card-payment' + selected_pump).removeClass('poa-button-credit-card');
        $('#button-cash-card-payment').addClass('poa-button-cash-card-disabled');
        $('#button-cash-card-payment').removeClass('poa-button-cash-card');
        $(`#button-wallet${selected_pump}`).addClass('opos-button-wallet-disabled')

        $('#button-credit-ac' + selected_pump).removeClass('opos-button-credit-ac');
        $('#button-credit-ac' + selected_pump).addClass('opos-button-credit-ac-disabled');
        $('.finish-button-' + selected_pump).removeClass('opos-topup-button');
        $('.finish-button-' + selected_pump).addClass('poa-finish-button-disabled');
        numpad_disable();
    }


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
            url: "/pump-cancel-authorize/" + pumpNo + '/' + ipaddr,
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


    async function initFuelProduct(my_pump, nozzle, if_first = null) {
        var returnData;
        $.ajax({
            url: "/pump-product-by-nozzle/" + my_pump + '/' + nozzle,
            type: "GET",
            dataType: "JSON",
            success: function(response) {
                var product = response.product;
                var productid = response.pid;
                var thumb = response.thumbnail;
                var price = response.price;
                //log2laravel('info', 'getControllerPrice price:'+price+'');
                returnData = response;
                //$("#product-select-pump-"+my_pump).css('display','none');

                //selecting of the product //
                $('#fuel-product-price-' + my_pump).html(price.toFixed(2));

                $('#fuel-grad-thumb-' + my_pump).attr('src', thumb);
                $('#fuel-grad-thumb-' + my_pump).css('display', 'inline-flex');

                // Squidster:
                // Will occasionally overwrite dose in the middle of a cycle
                //pumpData['pump' + my_pump].dose ='50.00';

                pumpData['pump' + my_pump].product = product;
                pumpData['pump' + my_pump].product_id = productid;
                pumpData['pump' + my_pump].product_thumbnail = thumb;
                pumpData['pump' + my_pump].price = price;

                /*
                var newData = {
                	"price" : pumpData['pump' + my_pump].price,
                	"fuel" : pumpData['pump' + my_pump].dose,
                	"filled" : '0.00',
                	"receipt_id": null
                };

                localStorage.setItem("pump_receipt_data_"+my_pump,
                	JSON.stringify(newData));
                */


                log2laravel('info', my_pump + ': BG1 initFuelProduct: price = ' +
                    pumpData['pump' + my_pump].price);
                log2laravel('info', my_pump + ': BG1 initFuelProduct: dose = ' +
                    pumpData['pump' + my_pump].dose);


                //selectProduct(''+my_pump+'' , ''+productid+'' , ''+product+'' , ''+thumb+''  );
                // console.log(`PL preset_type ${pumpData['pump'+selected_pump].preset_type}`);

                if (pumpData['pump' + my_pump].preset_type == 'Litre') {
                    console.log(`PL price ${response.price}`);

                    pumpData['pump' + my_pump].price_liter = response.price;
                    pumpData['pump' + my_pump].dose = (response.price * pumpData['pump' + my_pump].dose)
                        .toFixed(2);

                    $("#total_amount-main-" + my_pump).
                    text(parseFloat(response.price * pumpData['pump' + my_pump].dose).toFixed(2))

                    if (pumpData['pump' + my_pump].payment_type == "Prepaid") {

                        if (pumpData['pump' + my_pump].status != "Delivering") {
                            if (dis_cash['pump' + my_pump].payment_type == "card") {
                                $("#payment-type-amount" + my_pump).
                                html($("#total_amount-main-" + my_pump).text());
                            }

                            console.log("NUM_PAD enable");
                            //numpad_enable();

                        } else {
                            $("#payment-type-amount" + my_pump).html('');
                        }

                        $('.numpad-enter-payment' + my_pump).
                        addClass('poa-button-number-payment-enter-disabled');

                        $('.numpad-enter-payment' + my_pump).
                        removeClass('poa-button-number-payment-enter');
                    }
                }

                if (if_first != null) {
                    if (!$('#pump-button-' + my_pump).hasClass("poa-button-pump-delivering")) {
                        //pump is not delivering
                        //this function should run on the first time only
                        $('#button-cash-payment' + my_pump).trigger('click');
                        //$('.fuelproductimages-'+my_pump).parent().removeClass('selectedimag');
                        $('span').removeClass('selectedimag');
                        $('.fuelproductimages-' + my_pump).parent().removeClass(
                            'product-disable-offline');
                        $('#fuel-grad-thumb-' + my_pump + '-option-' + productid).parent().removeClass(
                            'product-disable-offline');
                        $('#fuel-grad-thumb-' + my_pump + '-option-' + productid).parent().addClass(
                            'selectedimag');
                        log2laravel('info', '*****initFuelProduct: my_pump=' + my_pump);
                        update_payment_table(my_pump);

                    } else {
                        $('#fuel-grad-thumb-' + my_pump + '-option-' + productid).addClass(
                            'selectedimag');
                    }
                }
            },
            error: function(response) {
                console.log(JSON.stringify(response));
            }
        });

        return returnData;
    }


    function getNozzleNo(pump_no, product_id, isAlert = false) {
        @if (!empty($nozzleFuelData))
            const nozzleFuelData = Object.values(
            {!! json_encode($nozzleFuelData->toArray(), true) !!});

            let find_nozzle = nozzleFuelData.filter((nozzle) => {
            return nozzle.product_id == product_id && nozzle.pump_no == pump_no;
            });

            console.log("NN all nozzles=", JSON.stringify(find_nozzle));
            console.log(`NN nozzles found for product ${product_id}=`, JSON.stringify(find_nozzle));
            if (find_nozzle.length == 0 || find_nozzle == undefined) {
            if (isAlert) {
            messageModal("No nozzle assigned for this fuel");
            }

            return false;
            } else {
            return find_nozzle;
            }
        @else
            return false;
        @endif
    }


    function getFuelGradeId(ogfuel_id) {

		log2laravel('info','getFuelGradeId: ogfuel_id=' + ogfuel_id);

        @if (!empty($fuel_grade_string))

			log2laravel('info','getFuelGradeId: fuel_grade_string=' +
				{!! json_encode($fuel_grade_string) !!});

            const fuelGradeData = Object.values(
            {!! json_encode($fuel_grade_string, true) !!});


			log2laravel('info','getFuelGradeId: fuelGradeData=' +
				JSON.stringify(fuelGradeData));

            let find_product = fuelGradeData.find( (e) => e.og_f_id == ogfuel_id);

			log2laravel('info','getFuelGradeId: find_product=' +
				JSON.stringify(find_product));

            if (find_product) {
            return find_product.Id
            } else {
            return false;
            }

        @else
            return false;
        @endif
    }



    function selectProduct(pump_no, ogfuel_id, product_id, product, thumb, product_price) {
        // set the product price on the dom.
        $(`#myr_amount_input_${pump_no}`).val(product_price);
        // end of the setting

        build_pump_env(pump_no)
        var nozzle = getNozzleNo(pump_no, product_id, true);
        var fuel_grade_id = getFuelGradeId(ogfuel_id);

		log2laravel('info','selectProduct: ogfuel_id    ='+ogfuel_id);
		log2laravel('info','selectProduct: product_id   ='+product_id);
		log2laravel('info','selectProduct: fuel_grade_id='+fuel_grade_id);

        var activepump = $('span#active-pump').text();

        if (nozzle && fuel_grade_id && (activepump == pump_no)) {

            var prevpump = $('#previous-pumps').attr('pumpno');
            var prevprod = $('#previous-pumps').attr('prod');

            if (prevpump != undefined && prevpump != '' && prevprod != undefined && prevprod != '') {
                if ($('#fuel-grad-thumb-' + prevpump + '-option-' + prevprod).hasClass('selectedimag')) {
                    $('#fuel-grad-thumb-' + prevpump + '-option-' + prevprod).removeClass('selectedimag');
                    $('#fuel-grad-thumb-' + pump_no + '-option-' + product_id).addClass('selectedimag');
                    $('#previous-pumps').attr('pumpno', pump_no);
                    $('#previous-pumps').attr('prod', product_id);
                }
            }

            pumpData['pump' + pump_no].product = product;
            pumpData['pump' + pump_no].product_id = product_id;
            pumpData['pump' + pump_no].ogfuel_id = ogfuel_id;
            pumpData['pump' + pump_no].product_thumbnail = thumb;
            check_enter();

            pumpData['pump' + pump_no].price = $('img.fuelproductimages-' + pump_no).attr('prod_price');   ;

            nozzle = JSON.stringify(nozzle.map((e) => e.nozzle_no));

            // $('#authorize-button').attr('class', '');
            // $('#authorize-button').addClass('btn poa-authorize');
            // $("#authorize-button").click(pump_authorize);

            product_info = initFuelProduct(pump_no, nozzle, 'yes').price;

            var dis_payment__status = $('#payment-status-' + pump_no).text();

            if (dis_payment__status !== 'Paid') {
                enable_payment_btns();
            }

        } else {
            if ($('.fuelproductimages-' + pump_no).hasClass('cursor-pointer')) {
                $('.fuelproductimages-' + pump_no).removeClass('cursor-pointer')
            }
        }
        disable_payment_btns()
        numberpad_enable(pump_no)
        // $(`#input-cash${pump_no}`).hide();

        console.log('**PRD***', JSON.stringify(product));

		$('#prd-name-' + pump_no).text(product);

        let prd_price = $('#fuel-grad-thumb-' + pump_no + '-option-' + product_id).attr('prod_price');
        $('#prd-myr-' + pump_no + '-price').text(prd_price);

        $("#custom_amount_input_" + pump_no).val('');
        $("#custom_amount_input_" + pump_no + '_buffer').val('');

        $("#custom_litre_input_" + pump_no).val('');
        $("#custom_litre_input_" + pump_no + '_buffer').val('');

		$('#prd-overlay-' + pump_no).attr("hidden", false);
    }


    /* Function to store transaction ID after getting
       authorization confirmation */
    function store_txid(resp, dose) {
        log2laravel('info', 'store_txid:' +
            JSON.stringify(resp) + ', dose=' + dose);

        if (resp != null && typeof resp != 'undefined') {
            resp = resp.data;
            if (typeof resp.response != 'undefined' &&
                resp.response != null) {
                var response = resp.response;

                log2laravel('info', 'store_txid 1: ' +
                    JSON.stringify(response));

                /*
                log2laravel('info', 'store_txid 1.1: '+
                	(typeof response.Packets));

                log2laravel('info', 'store_txid 1.2: '+
                	(typeof response.Packets[0]));

                if ((typeof response.Packets != 'undefined') &&
                	(response.Packets != null) &&
                	(response.Packets[0] != null) &&
                	(typeof response.Packets[0] != 'undefined')) {

                	log2laravel('info', 'store_txid 1.3: YAY!');
                }
                */

                if ((typeof response.Packets != 'undefined') &&
                    (response.Packets != null) &&
                    (typeof response.Packets[0] != 'undefined') &&
                    (response.Packets[0] != null)) {

                    log2laravel('info', 'store_txid 2: YAY!');

                    var packet = response.Packets[0];
                    var my_pump = packet.Data.Pump;
                    var transactionid = parseInt(packet.Data.Transaction);

                    log2laravel('info', 'store_txid 2.1: packet=' +
                        JSON.stringify(packet));

                    log2laravel('info', 'store_txid 2.2: my_pump=' +
                        my_pump);

                    log2laravel('info', 'store_txid 2.3: transactionid=' +
                        transactionid);

                    if (transactionid != '' &&
                        transactionid != null &&
                        transactionid > 0) {

                        log2laravel('info', 'store_txid 3: YAY!');

                        authorizeData['pump' + my_pump].transactionid = transactionid;
                        authorizeData['pump' + my_pump].amount = dose;

                        // Squidster: assign new dose to pumpData
                        //pumpData['pump'+my_pump].amount = dose;

                        log2laravel('info', 'store_txid 3.1: ' +
                            JSON.stringify(authorizeData['pump' + my_pump]));
                    }
                }
            }
        }
    }


    function refund_enter() {
        $("#payment-type-message" + selected_pump).html("");
        $("#payment-amount-card-amount" + selected_pump).html("");
        $("#payment-type-amount" + selected_pump).html("");
        $(".payment-div-refund" + selected_pump).show();
        $("#payment-type-amount" + selected_pump).html("");
        $("#payment-amount-card-amount" + selected_pump).html("");
        $("#payment-div-refund-amount-bl-message" + selected_pump).html("Refunded");
        reset['pump' + selected_pump].reset = true;
        $("#payment-type-amount" + selected_pump).html("");
        $("#payment-type-paid-right" + selected_pump).html("");
        $('.numpad-refund-payment' + selected_pump).removeClass('poa-button-number-payment-refund');
        $('.numpad-refund-payment' + selected_pump).addClass('poa-button-number-payment-refund-disabled');

    }



    function display_refund(my_pump, filled) {
        amt = pumpData[`pump${my_pump}`].amount;
        log2laravel('info', my_pump +
            ': display_refund: filled_volume=' + filled +
            ', filled_amount=' + amt);

        var r_id = pumpData['pump' + my_pump].receipt_id;

        // Protect receipt_id for being empty
        if (r_id == '' || r_id == undefined) {
            log2laravel('info', my_pump +
                ': display_refund: r_id IS BLANK! ABORTING!');
            return;
        }

        var data = {
            'receipt_id': r_id,
            'filled': filled
        }

        log2laravel('info',
            'display_refund: data=' + JSON.stringify(data));

        $.ajax({
            url: "{{ route('local_cabinet.nozzle.down.refund') }}",
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: data,
            dataType: 'json',
            success: function(response) {
                console.log('PR local_cabinet.nozzle.down.refund:');
                console.log('PR ***** SUCCESS *****');
                console.log('response=' + JSON.stringify(response));
                data.receipt_id = response;
                console.log('data=' + JSON.stringify(data));
            },
            error: function(response) {
                console.error('PR local_cabinet.nozzle.down.refund:');
                console.error('PR ***** ERROR *****');
                console.error(JSON.stringify(response));
            }
        });
    }


    function restoreOldState() {

        console.log("OLDSTATE => init");
        for (i = 1; i <= {{ env('MAX_PUMPS') }}; i++) {
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
                receipt_id: '',
                preset_type: 'amount',
                paymentStatus: 'Not Paid',
                payment_type: 'Prepaid',
                isNozzleUp: false,
                isAuth: false,
                is_slave: false
            };
            var old_unfinished_tx = JSON.parse(localStorage.getItem('pump_receipt_data_' + i));
            if(old_unfinished_tx != null || old_unfinished_tx != undefined){
                pumpData['pump' + i].paymentStatus = "Paid";
                pumpData['pump' + i].dose = old_unfinished_tx.dose;
                var pump_paid_for = $('#paid-pump').val();;
                if (pump_paid_for.length > 0) {
                    if (pump_paid_for.includes(',')) {
                        var splited_pumps = pump_paid_for.split(',');
                        splited_pumps.push(i);
                        var final_active_pump = splited_pumps.join(',');
                        $('#paid-pump').val(final_active_pump);
                    } else {
                        $('#paid-pump').val(pump_paid_for + ',' + i);
                    }
                } else {
                    $('#paid-pump').val(i);
                }
            }

        }
        localStorage.setItem('pumpDataState', JSON.stringify(pumpData));
        //var oldPumpData = JSON.parse(localStorage.getItem('pumpDataState'));

        if (localStorage.getItem('pumpDataState') != undefined) {
            var pd = JSON.parse(localStorage.getItem('pumpDataState'));
            pumpData = JSON.parse(localStorage.getItem('pumpDataState'));
            /* var pumpReceiptInfo = localStorage.getItem('pump_receipt_info');
             console.log("pump rec info: ", pumpReceiptInfo)
             var txnExists = false;
             if (pumpReceiptInfo != undefined || pumpReceiptInfo != null) {
                 txnExists = true;
             }
             if (txnExists) {
                 pumpData['pump' + pumpReceiptInfo].dose = localStorage.getItem('pump_data_fuel_' + pumpReceiptInfo);
             }
             pd['pump'+pumpReceiptInfo].dose = localStorage.getItem('pump_data_fuel_' + pumpReceiptInfo);
             localStorage.setItem('pumpDataState', pd);
             */
            /* This is the cause of the rounding bug */
        }
        if (localStorage.getItem('reset') != undefined)
            reset = JSON.parse(localStorage.getItem('reset'));

        if (localStorage.getItem('authorizeData') != undefined)
            authorizeData = JSON.parse(localStorage.getItem('authorizeData'));
    }


    var pumpStateInterval;


    function select_custom_amount() {

        log2laravel('info', 'select_custom_amount(): pumpData__='+ JSON.stringify(pumpData['pump'+selected_pump]));

        log2laravel('info', selected_pump + ': ***** select_custom_amount() *****');
        if (debounce_pump_auth(selected_pump))
            return

        display_litre_preset(false, selected_pump)
        amount = parseFloat($("#custom_amount_input_" + selected_pump).val());

        log2laravel('info', selected_pump + ': BG1 select_custom_amount: amount=' + amount);

        if (amount > 0) {
            set_amount(amount);
            $("#custom_amount_input_" + selected_pump).val('');
            $("#custom_amount_input_" + selected_pump + '_buffer').val('');
        }

        $("#custom_amount_btn").addClass('custom-preset-disable');
        $("#custom_amount_btn").removeClass('poa-button-preset');
        $("#custom_litre_btn").addClass('custom-preset-disable');
        $("#custom_litre_btn").removeClass('poa-button-preset');
    }


    function select_custom_litre() {

        if (debounce_pump_auth(selected_pump))
            return

        litre = parseFloat($("#custom_litre_input_" + selected_pump).val());

        if (litre > 0) {
            set_litre(litre);
            $("#custom_amount_input_" + selected_pump).val('');
        }

        $("#custom_amount_input_" + selected_pump + '_buffer').val('');
        $("#custom_amount_input_" + selected_pump).val('');

        $("#custom_amount_btn").addClass('custom-preset-disable');
        $("#custom_amount_btn").removeClass('poa-button-preset');
        $("#custom_litre_btn").addClass('custom-preset-disable');
        $("#custom_litre_btn").removeClass('poa-button-preset');
    }


    function set_litre(litre) {
        $("#productsModal").modal('show');
    }


    function display_litre_preset(disp, pump_no) {
        var preset_type = pumpData['pump' + pump_no].preset_type;

        if (disp == true)
            $(`#disp_ltr-${pump_no}`).css('display', 'block')
        else
            $(`#disp_ltr-${pump_no}`).css('display', 'none')

    }

    function reset_previous_tx_history() {
        // reset old tx state
        pumpData['pump' + selected_pump].volume = "0.00";
        pumpData['pump' + selected_pump].amount = "0.00";
        pumpData['pump' + selected_pump].price = "0.00";

        pumpData['pump' + selected_pump].paymentStatus = "Not Paid";
        pumpData['pump' + selected_pump].receipt_id = ''

        pumpData['pump' + selected_pump].product_id = "";
        pumpData['pump' + selected_pump].product = "";
        pumpData['pump' + selected_pump].product_thumbnail = "";

        //$("#product-select-pump-"+selected_pump).css('display','none');
        $('#fuel-grad-name-' + selected_pump).text("");
        $('#fuel-grad-thumb-' + selected_pump).css('display', 'none');
        $(`#total_amount-main-${selected_pump}`).text('0.00');
		/*
        $("#amount-myr-" + selected_pump).sevenSeg("destroy");
        $("#volume-liter-" + selected_pump).sevenSeg("destroy");
        $("#price-meter-" + selected_pump).sevenSeg("destroy");
		*/


        dis_cash['pump' + selected_pump] = {
            dis_cash: '',
            payment_type: '',
        };

		/*
        var volume = '0.00';
        $("#volume-liter-" + selected_pump).sevenSeg({
            digits: 7,
            value: volume,
            colorOff: colorScheme.colorOff,
            colorOn: colorScheme.colorOn,
            colorBackground: colorScheme.colorBackground,
            slant: colorScheme.slant,
            decimalPlaces: colorScheme.decimalPlaces
        });

        var price = '0.00';
        $("#price-meter-" + selected_pump).sevenSeg({
            digits: 7,
            value: price,
            colorOff: colorScheme.colorOff,
            colorOn: colorScheme.colorOn,
            colorBackground: colorScheme.colorBackground,
            slant: colorScheme.slant,
            decimalPlaces: colorScheme.decimalPlaces
        });

        var amount = '0.00';
        $("#amount-myr-" + selected_pump).sevenSeg({
            digits: 7,
            value: amount,
            colorOff: colorScheme.colorOff,
            colorOn: colorScheme.colorOn,
            colorBackground: colorScheme.colorBackground,
            slant: colorScheme.slant,
            decimalPlaces: colorScheme.decimalPlaces
        });
		*/

        disable_payment_btns();
    }


    @for ($i = 1; $i <= env('MAX_PUMPS'); $i++)
        filter_price("#custom_litre_input_{{ $i }}","#custom_litre_input_{{ $i }}_buffer");
        filter_price("#custom_amount_input_{{ $i }}","#custom_amount_input_{{ $i }}_buffer");
        filter_price("#input-cash{{ $i }}","#buffer-input-cash{{ $i }}");

        $("#input-cash{{ $i }}").on("keyup", (e) => {
			console.log("field", $(`#buffer-input-cash${selected_pump}`).val());
			dis_cash['pump'+selected_pump].dis_cash =
				$(`#buffer-input-cash${selected_pump}`).val() != '' ?
				$(`#buffer-input-cash${selected_pump}`).val():0;
			calculate_change();
			check_enter();
        });

        $("#custom_amount_input_{{ $i }}").on("keyup", (e) => {
			val = $("#custom_amount_input_{{ $i }}").val();

			$("#custom_litre_input_{{ $i }}_buffer").val('');
			$("#custom_litre_input_{{ $i }}").val('');
			$("#custom_litre_btn").addClass('custom-preset-disable');
			$("#custom_litre_btn").removeClass('poa-button-preset');

			if (val == '') {
				$("#custom_amount_btn").addClass('custom-preset-disable');
				$("#custom_amount_btn").removeClass('poa-button-preset');
                $("#custom_amount_btn_{{ $i }}").addClass('cover-myr-disable');
                $("#custom_amount_btn_{{ $i }}").removeClass('cover-myr-enable');
			} else {
				$("#custom_amount_btn").removeClass('custom-preset-disable');
				$("#custom_amount_btn").addClass('poa-button-preset');

                let buffer_check = $('#custom_amount_input_{{$i}}_buffer').val()

                if(buffer_check > 0) {
                    $("#custom_amount_btn_{{ $i }}").removeClass('cover-myr-disable');
                    $("#custom_amount_btn_{{ $i }}").addClass('cover-myr-enable');
                }

			}
        });

        $("#custom_litre_input_{{ $i }}").on("keyup", (e) => {
			val = $("#custom_litre_input_{{ $i }}").val();

			$("#custom_amount_input_{{ $i }}_buffer").val('');
			$("#custom_amount_input_{{ $i }}").val('');
			$("#custom_amount_btn").addClass('custom-preset-disable');
			$("#custom_amount_btn").removeClass('poa-button-preset');

			if (val == '') {
				$("#custom_litre_btn").addClass('custom-preset-disable');
				$("#custom_litre_btn").removeClass('poa-button-preset');
                $("#custom_litre_btn_{{ $i }}").addClass('cover-myr-disable');
                $("#custom_litre_btn_{{ $i }}").removeClass('cover-myr-enable');
			} else {
				$("#custom_litre_btn").removeClass('custom-preset-disable');
				$("#custom_litre_btn").addClass('poa-button-preset');
                $("#custom_litre_btn_{{ $i }}").removeClass('cover-myr-disable');
                $("#custom_litre_btn_{{ $i }}").addClass('cover-myr-enable');
			}
        });
    @endfor

    ///////////////////////////////
    function filter_price(target_field, buffer_in) {
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
                    $(target_field).val(atm_money(parseInt($(buffer_in).val())))
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
            $(target_field).val(atm_money(parseInt($(buffer_in).val())))
        });
    }


    function atm_money(num) {
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


    function resetAuth_flag(pump_no) {
        pumpData['pump' + pump_no].isAuth = false;
        deleteTerminalSyncData = (pump_no);
        pump_selected(pump_no);
    }


    function clear_local_storage() {
        clearInterval(pumpStateInterval);
        localStorage.removeItem('reset');
        localStorage.removeItem('pumpDataState');
        localStorage.removeItem('authorizeData');
        window.location.reload();
    }

    function suspend_tab() {
        clearInterval(pumpStateInterval)
        isVisible = false;
    }

    function activate_tab() {
        log2laravel('activate_tab: BEFORE restoreOldstate:');
        log2laravel(JSON.stringify(pumpData));

        restoreOldState();
        log2laravel('activate_tab: AFTER  restoreOldstate:');
        log2laravel(JSON.stringify(pumpData));

        pumpStateInterval = setInterval(() => {
            localStorage.setItem('pumpDataState', JSON.stringify(pumpData));
            localStorage.setItem('reset', JSON.stringify(reset));
            localStorage.setItem('authorizeData', JSON.stringify(authorizeData));
        }, 250);

        if (selected_pump != 0) {
            pump_selected(selected_pump, true);
        }

        isVisible = true;
    }

    var isVisible;
    $(document).ready(function() {
        if(selected_pump == 0){
                $('.button-number-amount').addClass('poa-button-number-disabled');
                $('.button-number-amount').removeClass('poa-button-number');
            disable_payment_btns();
        }
        var hidden, visibilityState, visibilityChange;
        if (typeof document.hidden !== "undefined") {
            hidden = "hidden";
            visibilityChange = "visibilitychange";
            ovisibilityState = "visibilityState";
        } else if (typeof document.msHidden !== "undefined") {
            hidden = "msHidden";
            visibilityChange = "msvisibilitychange";
            visibilityState = "msVisibilityState";
        }
        var document_hidden = document[hidden];
        document.addEventListener(visibilityChange, function() {
            /*if(document_hidden != document[hidden]) {
            	if(document[hidden]) {
            	// Document hidden
            		console.log("NM Tab suspended");
            		console.log("NM pumpdata", pumpData)
            		suspend_tab();
            	} else {
            	// Document shown
            		//
            			activate_tab();
            		if( selected_pump != 0) {
            			if(pumpData['pump'+selected_pump].status != "Delivering") {
            				try {
            					$("#amount-myr-"+selected_pump).sevenSeg("destroy");
            					$("#volume-liter-"+selected_pump).sevenSeg("destroy");
            					$("#price-meter-"+selected_pump).sevenSeg("destroy");
            				} catch {}

            				getPumpStatus(selected_pump, true);
            			}

            			pump_selected(0)
            		}
            	//	window.location.reload();
            	}
            	document_hidden = document[hidden];
            }*/
        });
        $("img.product-disable-offline").removeAttr("onclick");
    });

    activate_tab();


    function truncateToDecimals(num, dec = 2) {
        return num;
    }


    var isTerminalSyncData = false;

    function terminalSyncData(pump_no) {

        if (isVisible == false)
            return;

        data = {};
        //console.log(pumpData[`pump${pump_no}`]);
        isTerminalSyncData = true;
        if (pumpData[`pump${pump_no}`].product_id) {
            data['product_id'] = pumpData[`pump${pump_no}`].product_id;
        }

        data['pump_no'] = pump_no;
        data['payment_status'] = pumpData[`pump${pump_no}`].paymentStatus;
        data['dose'] = pumpData[`pump${pump_no}`].dose;
        data['price'] = pumpData[`pump${pump_no}`].price;
        data['receipt_id'] = pumpData[`pump${pump_no}`].receipt_id;
        data['name'] = pumpData[`pump${pump_no}`].product;
        data['product_thumbnail'] = pumpData[`pump${pump_no}`].product_thumbnail;
        data['transactionid'] = authorizeData[`pump${pump_no}`].transactionid;

        if (pumpData[`pump${pump_no}`].preset_type == "Litre")
            data['litre'] = 1;
        else
            data['litre'] = 0; //store_last_Filled


        //localStorage.setItem("update-screen-e-landing", JSON.stringify(data));

        $.post('{{ route('sync_data') }}', data).
        done(() => isTerminalSyncData = false).
        fail((e) => console.log(e));
    }

/*
    getTerminalSyncData = debounce(function() {
        if (isTerminalSyncData)
            return;

        $.post('{{ route('get_sync_data') }}').done((res) => {
            if (!res)
                return;

            //console.log("getTerminalSyncData: res => " + JSON.stringify(res))
            terminal_id = {{ $terminal->id }};

            for (i = 1; i <= {{ env('MAX_PUMPS') }}; i++) {
                find_record = res.find((d) => d.pump_no == i);
                old_is_slave = pumpData[`pump${i}`].is_slave

                // Slave detection
                if (find_record) {
                    if (find_record.master_terminal_id == terminal_id)
                        pumpData[`pump${i}`].is_slave = false;
                    else
                        pumpData[`pump${i}`].is_slave = true;
                } else {
                    //pumpData[`pump${i}`].is_slave = false;
                }

                if (find_record && pumpData[`pump${i}`].is_slave == true) {
                    dose = find_record.dose;
                    price = find_record.price;
                    litre = find_record.litre;
                    pump_no = find_record.pump_no;

                    //	console.log("getTerminalSyncData: pump_no => " + pump_no )
                    //	console.log("getTerminalSyncData: is_slave => " + pumpData[`pump${pump_no}`].is_slave )

                    if (litre == 1) {
                        pumpData[`pump${pump_no}`].preset_type = "Litre";
                        if (old_is_slave == false) {
                            $(`#total_amount-main-${pump_no}`).text((price * dose).toFixed(2));
                        }
                        $('#total_volume-main-' + pump_no).text(dose.toFixed(2));

                        //console.log( "getTerminalSyncData: amount => (type: ltr) " + (price * dose).toFixed(2) )
                        //	console.log("getTerminalSyncData: dose => (type: ltr)" + dose.toFixed(2) )
                        display_litre_preset(true, pump_no)
                    } else {
                        pumpData[`pump${pump_no}`].preset_type = "amount";
                        $(`#total_amount-main-${pump_no}`).text(dose.toFixed(2))

                        display_litre_preset(false, pump_no)
                        //	console.log("getTerminalSyncData: dose (type: AMT) => " + dose.toFixed(2) )
                    }

                    if (old_is_slave == false) {
                        $("#amount-myr-" + pump_no).sevenSeg("destroy");
                        $("#volume-liter-" + pump_no).sevenSeg("destroy");
                        $("#price-meter-" + pump_no).sevenSeg("destroy");

                        image =
                            `/images/product/${find_record.psystemid}/thumb/${find_record.thumbnail_1}`;
                        pumpData[`pump${pump_no}`].price = price.toFixed(2);
                        pumpData[`pump${pump_no}`].price_liter = price.toFixed(2);
                        pumpData[`pump${pump_no}`].dose = dose.toFixed(2);
                        pumpData[`pump${pump_no}`].product_thumbnail = image;
                        pumpData[`pump${pump_no}`].product_id = find_record.product_id;
                        pumpData[`pump${pump_no}`].product = find_record.pname;

                    }
                    pumpData[`pump${pump_no}`].paymentStatus = find_record.payment_status;
                }
            }
        });
    }, 100);-*/


    var deleteTerminalSyncData = debounce(function(pump_no) {
        console.log("deleteTerminalSyncData");
        $.post('{{ route('delete_sync_data') }}', {
            pump_no: pump_no
        }).
        fail((e) => console.log(e));
    }, 300);


    var debounce_pump_auth = (pump_no) => {
        if (pumpData[`pump${pump_no}`].is_slave == true) {
            return true
        } else {
            return false;
        }
    }


    function calculate_fuel_price(price) {
        liter = $(`#custom_litre_input_${selected_pump}`).val();
        amount = liter * price;
        $(`#custom_litre_input_${selected_pump}`).val('')

        if (amount > 0) {
            $("#custom_amount_btn").removeClass('custom-preset-disable');
            $("#custom_amount_btn").addClass('poa-button-preset');
        }

        amount = amount.toFixed(2);
        $(`#custom_amount_input_${selected_pump}`).val(amount);
        $("#productsModal").modal('hide');
    }
</script>

<!-- FUEL PAGE CODE ENDS -->
