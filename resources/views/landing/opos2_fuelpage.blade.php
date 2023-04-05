<script>

function build_pump_env(pump_no, forced = false, remove_selected_info = true) {
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
            // var overlay2 = document.getElementById('overlayover-' + i);
            // overlay2.hidden = true;
            //console.log("is slave selected pump: " ,pumpData['pump' + i]);
            //wrapper_div.setAttribute('style','');
            //wrapper_div.style.paddingLeft = '15px';
        }
    }
    if (!fulltank_pump_data['pump' + pump_no].is_slave){
        for(i = 1; i <= {{env('MAX_PUMPS')}}; i++) {
            // var ft_overlay = document.getElementById('ft-overlay-sa-' + i);
            // ft_overlay.hidden = true;
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
        product_info = initFuelProduct(pump_no, nozzle, 'yes').price;
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
            log2laravel('error', pump_no + ': BG1 pump_selected: pumpData[pump' +
				pump_no + '] is NULL!!');
        }
    } else {
        log2laravel('error', pump_no + ': BG1 pump_selected: pumpData is NULL!');
    }

    confirmActiveDeliveringPump(pump_no);

    var dis_pump_receipt = localStorage.getItem("pump_receipt_info");

    if (remove_selected_info === true && dis_payment__status !== 'Paid' &&
		dis_pump_receipt === pump_no) {
    }

    if (dis_payment__status == 'Paid' && (isNozzleDown !== 'yes' ||
		isNozzleDown === undefined || isNozzleDown === '')) {
        localStorage.setItem("pump_receipt_info", pump_no);
    }

    var reset_receipt_date = localStorage.getItem("pump_reset_data_" + pump_no);

    if (reset_receipt_date != undefined && reset_receipt_date != '' &&
		remove_selected_info === true) {
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

        /*
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
        */
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
            //$('.cancel-pump-number-' + selected_pump).addClass('poa-button-number-payment-cancel');
            $('button.poa-button-number').addClass('poa-button-cash-selected-disabled');
            $('button.poa-button-number').removeClass('poa-button-cash-selected');

            $('.give-me-left-' + selected_pump).addClass('left-7');
            //$('.cancel-pump-number-' + selected_pump).addClass('btn-size-70');
        }

        if (!$('.button-number-amount').hasClass('poa-button-number-disabled')) {
            $('.button-number-amount').addClass('poa-button-number-disabled');
            $('.button-number-amount').removeClass('poa-button-number');
        }

        disable_payment_btns();


    } else {
        if (!$('.cancel-pump-number-' + selected_pump).hasClass('poa-button-number-payment-cancel-disabled')) {
            //$('.cancel-pump-number-' + selected_pump).addClass('poa-button-number-payment-cancel-disabled');
            $('.give-me-left-' + selected_pump).removeClass('left-7');
            //$('.cancel-pump-number-' + selected_pump).removeClass('btn-size-70');
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
        // $('.cancel-pump-number-' + selected_pump).addClass('poa-button-number-payment-cancel-disabled');
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
        //$('.cancel-pump-number-' + pump_no).addClass('poa-button-number-payment-cancel-disabled');
        $('.give-me-left-' + pump_no).removeClass('left-7');
        $('.cancel-pump-number-' + pump_no).removeClass('btn-size-70');
        $('button.poa-button-number').removeClass('poa-button-cash-selected-disabled');
        $('.button-number-amount').removeClass('poa-button-number-disabled');
        $('.button-number-amount').removeClass('poa-button-cash-selected-disabled');
        $('.button-number-amount').addClass('poa-button-number');
        enable_payment_btns();
    }
    // preset_button(6);
    
    // Close all windows
    // $("div[id^='prd-overlay-']").attr("hidden", false);

    $("#custom_amount_input_" + pump_no).val('');
    $("#custom_amount_input_" + pump_no + "_buffer").val('');
    $("#custom_amount_input_" + pump_no).attr("disabled", false);

    $("#custom_litre_input_" + pump_no).val('');
    $("#custom_litre_input_" + pump_no + "_buffer").val('');
    $("#custom_litre_input_" + pump_no).attr("disabled", false);

    $("#input-cash"+pump_no).val('');
    $("#buffer-input-cash"+pump_no).val('');

    $("#input-cash"+pump_no).show();
    $("#input-cash"+pump_no).prop('disabled', true);

    
    
    $('#prd-overlay-' + pump_no).attr("hidden", false);

}

function cover_process_finish(pump_no) {
	process_finish(pump_no)
	$('#prd-overlay-' + pump_no).attr("hidden", true);

    $("#custom_litre_btn_" + pump_no).removeClass('custom-preset-disable');
    $("#custom_litre_btn_" + pump_no).removeClass('poa-button-preset');
}

function show_txn_info_modal(pmpno) {

    $('#m-table-PRODUCT').text($('#table-PRODUCT-'+pmpno).text());
    $('#m-table-PRICE').text($('#table-PRICE-'+pmpno).text());
    $('#m-table-QTY').text($('#table-QTY-'+pmpno).text());
    $('#m-table-MYR').text($('#table-MYR-'+pmpno).text());

    $('#m-item-amount-calculated').text($('#item-amount-calculated-'+pmpno).text());
    $('#m-sst-val-calculated').text($('#sst-val-calculated-'+pmpno).text());
    $('#m-rounding-val-calculated').text($('#rounding-val-calculated-'+pmpno).text());
    $('#m-grand-total-val-calculated').text($('#grand-total-val-calculated-'+pmpno).text());
    $('#m-change-val-calculated').text($('#change-val-calculated-'+pmpno).text());

    $("#txn_info_modal").modal('show');
}

function detect_reserved_pump(pmpno) {
    disable_payment_buttons(pmpno)
    numberpad_disable(pmpno)
}

function disable_payment_buttons(pmpno) {
	$('#button-cash-payment' + pmpno).addClass('poa-button-cash-disabled');
	$('#button-cash-payment' + pmpno).removeClass('poa-button-cash');
	$('#button-card-payment' + pmpno).addClass('poa-button-credit-card-disabled');
	$('#button-card-payment' + pmpno).removeClass('poa-button-credit-card');
	$('#button-cash-card-payment').addClass('poa-button-cash-card-disabled');
	$('#button-cash-card-payment').removeClass('poa-button-cash-card');
	$(`#button-wallet${pmpno}`).addClass('opos-button-wallet-disabled')

	$('#button-credit-ac' + pmpno).removeClass('opos-button-credit-ac');
	$('#button-credit-ac' + pmpno).addClass('opos-button-credit-disabled');
	$('.finish-button-' + pmpno).removeClass('opos-topup-button');
	$('.finish-button-' + pmpno).addClass('poa-finish-button-disabled');
	// numberpad_disable();

    	//$(`#input-cash${pmpno}`).hide();
}


function numberpad_enable(pmpno) {

    $('#set-default-preset-button-' + pmpno + '-eighthund').removeClass('poa-button-cash-selected-disabled');
    $('#set-default-preset-button-' + pmpno + '-onefifty').removeClass('poa-button-cash-selected-disabled');
    $('#set-default-preset-button-' + pmpno + '-hund').removeClass('poa-button-cash-selected-disabled');
    $('#set-default-preset-button-' + pmpno + '-fifty').removeClass('poa-button-cash-selected-disabled');
    $('#set-default-preset-button-' + pmpno + '-twenty').removeClass('poa-button-cash-selected-disabled');
    $('#set-default-preset-button-' + pmpno + '-ten').removeClass('poa-button-cash-selected-disabled');
    $('#set-default-preset-button-' + pmpno + '-five').removeClass('poa-button-cash-selected-disabled');
    $('#set-default-preset-button-' + pmpno + '-two').removeClass('poa-button-cash-selected-disabled');
    $('#set-default-preset-button-' + pmpno + '-eighthund').removeClass('poa-button-number-disabled');
    $('#set-default-preset-button-' + pmpno + '-onefifty').removeClass('poa-button-number-disabled');
    $('#set-default-preset-button-' + pmpno + '-hund').removeClass('poa-button-number-disabled');
    $('#set-default-preset-button-' + pmpno + '-fifty').removeClass('poa-button-number-disabled');
    $('#set-default-preset-button-' + pmpno + '-twenty').removeClass('poa-button-number-disabled');
    $('#set-default-preset-button-' + pmpno + '-ten').removeClass('poa-button-number-disabled');
    $('#set-default-preset-button-' + pmpno + '-five').removeClass('poa-button-number-disabled');
    $('#set-default-preset-button-' + pmpno + '-two').removeClass('poa-button-number-disabled');

    $('#set-default-preset-button-' + pmpno + '-eighthund').addClass('poa-button-number');
    $('#set-default-preset-button-' + pmpno + '-onefifty').addClass('poa-button-number');
    $('#set-default-preset-button-' + pmpno + '-hund').addClass('poa-button-number');
    $('#set-default-preset-button-' + pmpno + '-fifty').addClass('poa-button-number');
    $('#set-default-preset-button-' + pmpno + '-twenty').addClass('poa-button-number');
    $('#set-default-preset-button-' + pmpno + '-ten').addClass('poa-button-number');
    $('#set-default-preset-button-' + pmpno + '-five').addClass('poa-button-number');
    $('#set-default-preset-button-' + pmpno + '-two').addClass('poa-button-number');

	// $(`#input-cash${pmpno}`).hide();

}

function numberpad_disable(pmpno) {

	$('.numpad-number-payment' + pmpno).addClass('poa-button-number-payment');
	$('.numpad-number-payment' + pmpno).removeClass('poa-button-number-payment-disabled');
	$('.numpad-zero-payment' + pmpno).addClass('poa-button-number-payment-zero');
	$('.numpad-zero-payment' + pmpno).removeClass('poa-button-number-payment-zero-disabled');
	$('.numpad-refund-payment' + pmpno).addClass('poa-button-number-payment-refund');
	$('.numpad-refund-payment' + pmpno).removeClass('poa-button-number-payment-refund-disabled');
	$('.numpad-enter-payment' + pmpno).addClass('poa-button-number-payment-enter');
	$('.numpad-enter-payment' + pmpno).removeClass('poa-button-number-payment-enter-disabled');

    
    $('#set-default-preset-button-' + pmpno + '-eighthund').addClass('poa-button-cash-selected-disabled');
    $('#set-default-preset-button-' + pmpno + '-onefifty').addClass('poa-button-cash-selected-disabled');
    $('#set-default-preset-button-' + pmpno + '-hund').addClass('poa-button-cash-selected-disabled');
    $('#set-default-preset-button-' + pmpno + '-fifty').addClass('poa-button-cash-selected-disabled');
    $('#set-default-preset-button-' + pmpno + '-twenty').addClass('poa-button-cash-selected-disabled');
    $('#set-default-preset-button-' + pmpno + '-ten').addClass('poa-button-cash-selected-disabled');
    $('#set-default-preset-button-' + pmpno + '-five').addClass('poa-button-cash-selected-disabled');
    $('#set-default-preset-button-' + pmpno + '-two').addClass('poa-button-cash-selected-disabled');
    $('#set-default-preset-button-' + pmpno + '-eighthund').addClass('poa-button-number-disabled');
    $('#set-default-preset-button-' + pmpno + '-onefifty').addClass('poa-button-number-disabled');
    $('#set-default-preset-button-' + pmpno + '-hund').addClass('poa-button-number-disabled');
    $('#set-default-preset-button-' + pmpno + '-fifty').addClass('poa-button-number-disabled');
    $('#set-default-preset-button-' + pmpno + '-twenty').addClass('poa-button-number-disabled');
    $('#set-default-preset-button-' + pmpno + '-ten').addClass('poa-button-number-disabled');
    $('#set-default-preset-button-' + pmpno + '-five').addClass('poa-button-number-disabled');
    $('#set-default-preset-button-' + pmpno + '-two').addClass('poa-button-number-disabled');
    

    /*
      When page is reloaded thee buttons loose their pump
      attachment. This is to make sure that when page is
      reloaded and cancel button is clicked the
      numpad buttons have an inactive appearance
    */
    // $('div > button.numpad-btn').addClass('poa-button-number-disabled')
    // $('div > button.numpad-btn').removeClass('poa-button-number')

    // $('#custom_amount_input_' + pmpno).attr('disabled', true);
    // $('#custom_litre_input_' + pmpno).attr('disabled', true);

	// $(`#input-cash${pmpno}`).hide();

}

// Pumps that will have a grey cover---
function update_pumps_for_grey_cover(pmpno) {

    let current_grey_pumps = JSON.parse(localStorage.getItem('scover-grey-pumps'));

    if (
        current_grey_pumps != null &&
        Array.isArray(current_grey_pumps) &&
        current_grey_pumps.length &&
        !current_grey_pumps.includes(pmpno)
    ) {
        current_grey_pumps.push(pmpno)
    } else {
        current_grey_pumps = [];
        current_grey_pumps.push(pmpno)
    }

    localStorage.setItem('scover-grey-pumps', JSON.stringify(current_grey_pumps))

}

// Setting product Windo On select
function btns_modal(pmpno,prd_price){


    $('#prd_price_' + pmpno).val(prd_price);
    $('#preset_amt_pump_' + pmpno).text('0.00');
    disable_ltr_myr(pmpno);

    // Enable preset buttons
    numberpad_enable(pmpno)

    // Enable Litre Input
    $("#custom_litre_input_" + pmpno + '_buffer').val('');
    $("#custom_litre_input_" + pmpno).val('');
    $("#custom_litre_input_" + pmpno).prop("disabled", false);
    $("#custom_litre_input_" + pmpno).removeAttr('disabled');

    $("#custom_litre_btn_"+pmpno).addClass('custom-preset-disable');
    $("#custom_litre_btn_"+pmpno).removeClass('poa-button-preset');

    // Enable MYR input
    $("#custom_amount_input_" + pmpno + '_buffer').val('');
    $("#custom_amount_input_" + pmpno).val('');
    $("#custom_amount_input_" + pmpno).prop("disabled", false);
    $("#custom_amount_input_" + pmpno).removeAttr('disabled');

    $("#custom_amount_btn_"+pmpno).addClass('custom-preset-disable');
    $("#custom_amount_btn_"+pmpno).removeClass('poa-button-preset');

    // Disable Payment Buttons
    disable_payment_buttons(pmpno)

    // Disable Cash Received Input Field
    $("#input-cash"+pmpno).val('');
    $("#buffer-input-cash"+pmpno).val('');
    $("#input-cash"+pmpno).show();
    $("#input-cash"+pmpno).prop("disabled", true);

    localStorage.setItem('prd_window_pump', pmpno)

    $('#prd-overlay-' + pmpno).attr("hidden", false);

    $("button.numpad-btn.cover-btn").removeClass('poa-button-preset');
    $("button.numpad-btn.cover-btn").removeClass('custom-preset-disable');
}

function close_prd_window(pmpno) {

    disable_ltr_myr(pmpno);

    $("#custom_litre_input_" + pmpno + '_buffer').val('');
    $("#custom_litre_input_" + pmpno).val('');

    $("#custom_amount_input_" + pmpno + '_buffer').val('');
    $("#custom_amount_input_" + pmpno).val('');

    $("#input-cash"+pmpno).val('');
    $("#buffer-input-cash"+pmpno).val('');
    
    $("button.numpad-btn.cover-btn").removeClass('poa-button-preset');
    $("button.numpad-btn.cover-btn").removeClass('custom-preset-disable');

    $('#prd-overlay-' + pmpno).attr("hidden", false);
}

function select_pump_zero() {
    build_pump_env(0)
}

function remove_start_cover(next_pump) {

    // Close all windows
    $("div[id^='prd-overlay-']").attr("hidden", true);


    // select_pump_zero();
    // This is the previous pump
    let pump_to_close = localStorage.getItem('scover_pump_no');

    console.log('remove start cover(): pump_to_close=', pump_to_close)

    if (
        (pump_to_close != null) &&
        (pump_to_close != undefined) &&
        (pump_to_close != '')
    ) {
            console.log('**Setting product, price and qty***')
            // Reset  values on screen a
            $('#table-PRODUCT-' + pump_to_close).text('');
            $('#table-PRICE-' + pump_to_close).text('');
            $('#table-QTY-' + pump_to_close).text('');
            $('#table-MYR-' + pump_to_close).text('0.00');
            $('#item-amount-calculated-' + pump_to_close).text('0.00');
            $('#sst-val-calculated-' + pump_to_close).text('0.00');
            $('#rounding-val-calculated-' + pump_to_close).text('0.00');
            $('#grand-total-val-calculated-' + pump_to_close).text('0.00');
            $('#change-val-calculated-' + pump_to_close).text('0.00');

            console.log('**disable numpad***')
            // Disable number pad
            // numberpad_disable(pump_to_close)

            console.log('**Disable payment***')
            // Disable payment buttons
           //  disable_payment_buttons(pump_to_close)

           console.log('**Deselect pump***')
            // Remove pump number
           $('#pump-number-main-'+ pump_to_close).html('');


        console.log('**Cover initial pump***')
        // cover previous pump
        $('#scover-' + pump_to_close).attr('hidden', false);
        //$('#scover-grey-' + pump_to_close).attr('hidden', true);
        $('#scover-' + pump_to_close).css( "cursor", "default" );
        $('img.fuelproductimages-'+pump_to_close).css( "cursor", "default" );

    }
    console.log('**Remove cover on new pump***')
    // remove the cover on the clicked pump
    $('#scover-' + next_pump).attr('hidden', true);
    //$('#scover-grey-' + next_pump).attr('hidden', true);
    $('#scover-' + next_pump).css( "cursor", "pointer" );
    $('img.fuelproductimages-'+ next_pump).css( "cursor", "pointer" );

    console.log('**Set new pump to open ***')
    localStorage.setItem('scover_pump_no',  next_pump);

    $("img.fuelproductimages-" + next_pump).each(function (index) {
        $(this).attr("onclick", $(this).attr("bakclick"));
    });

    console.log('**Clear local storage***')
    //zerorise values on the clicked pump
     clear_storage(next_pump)
}

function clear_storage(pmpno) {
	pumpData['pump'+pmpno] = {
		amount: "0.00",
		dose: "0.00",
		isAuth: false,
		isNozzleUp: false,
		is_slave: false,
		nozzle: "",
		paymentStatus: "Not Paid",
		payment_type: "Prepaid",
		preset_type: "amount",
		price: "0.00",
		price_liter: "0.00",
		product: "",
		product_id: "",
		product_thumbnail: "",
		receipt_id: "",
		status: "Offline",
		volume: "0.00"
	};


	authorizeData['pump'+pmpno] = {
		amount: "0.00",
		nozzle: "",
		paymentStatus: "Not Paid",
		price: "0.00",
		product: "",
		product_thumbnail: "",
		transactionid: "",
		volume: "0.00"
	};

    localStorage.removeItem('pump_data_fuel_'+pmpno);
    localStorage.removeItem('pump_receipt_data_'+pmpno);
    localStorage.removeItem('pump_receipt_id_'+pmpno);
    localStorage.removeItem('pump_reset_data_'+pmpno);
    localStorage.removeItem('isNozzleDown'+pmpno);
    localStorage.removeItem('show-screen-e-fuel-modal');
    localStorage.setItem('isNozzleDown', 'yes');
    localStorage.removeItem('authorizeData');


    $('#total-fuel-pump-' + pmpno).text('0.00');
    $('#total-final-filled-' + pmpno).text('0.00');
    $('#total-final-litre-' + pmpno).text('0.00');
    $('#fuel-product-price-' + pmpno).text('0.00');
    $('#payment-status-' + pmpno).text('Not Paid');
}

// Additional function when pump is idle
function pump_idle_status_additions(pmpno){

    $('img[id^="fuel-grad-thumb-'+pmpno+'-option-"]').each(function (index) {
         $(this).removeClass("product-disable-offline");
         $(this).css( "cursor", "pointer");
    });

    // Pumps that should be covered grey
    let grey_cover = localStorage.getItem('scover_grey_' + pmpno);

    if (
        grey_cover != undefined &&
        grey_cover != null &&
        grey_cover != ''
    ) {
        // Pump is authorised. Add grey cover
        pump_delivering_status_additions(pmpno)

    } else {
        let open_pmp = localStorage.getItem('scover_pump_no');

        // Check if pump is selected
        if (open_pmp == pmpno) {
            // Pump is selected so dont cover
            $('#scover-' + pmpno).attr('hidden', true);
            $('#scover-grey-' + pmpno).attr('hidden', true);

            //Update cursor
            $('img.fuelproductimages-'+pmpno).css( "cursor", "pointer" );
        } else {
            // Pump not selected so cover in blue
            $('#scover-' + pmpno).attr('hidden', false);
            $('#scover-grey-' + pmpno).attr('hidden', true);
        }

        // Add the onclick functionality, and set cursor icon
        $("img.fuelproductimages-" + pmpno).each(function (index) {
            $(this).attr("onclick", $(this).attr("bakclick"));
        });
    }

    $("#custom_amount_input_" + pmpno).attr("disabled", false);
}

function update_ui_on_nozzle_down(pmpno) {
    localStorage.removeItem('scover_grey_' + pmpno);

    //clear the cover when page is reloaded so that all pumps are covered
    let current_open_pump = localStorage.getItem('scover_pump_no');

    // If the current open pump is this pump, remove the record
    if (current_open_pump == pmpno) {
        localStorage.removeItem('scover_pump_no');
    }

    // Remove grey cover from UI
    $('#scover-grey-' + pmpno).attr('hidden', true);

    // Add blue cover to buttons
    $('#scover-' + pmpno).attr('hidden', false);

    // Add onclick attribute to buttons
    $("img.fuelproductimages-" + pmpno).each(function (index) {
        $(this).attr("onclick", $(this).attr("bakclick"));
    });

}

//additional function when pump is delivering
function pump_delivering_status_additions(pmpno, enable_cancel=false){

    // Ensure grey cover is set
    localStorage.setItem('scover_grey_' + pmpno, pmpno)

    $('#scover-' + pmpno).attr('hidden', true);

    // Update cursor to show arrow
    $('img.fuelproductimages-'+pmpno).css( "cursor", "default" );

    $('#scover-grey-' + pmpno).attr('hidden', false);
    $('#scover-grey-' + pmpno).css({"opacity": "0.8"});
    $('#scover-grey-' + pmpno).css( "cursor", "default" );

    if (enable_cancel) {
        // Set local storage button for this cancel
        localStorage.setItem('scover_grey_cancel_' + pmpno, pmpno)
        $('#scover-grey-btn-' + pmpno).attr('hidden', false);
        $('#scover-grey-btn-' + pmpno).css("cursor", "pointer");

        $('#scover-grey-' + pmpno).css({"opacity": "1"});
    }
}

//additional function when pump is offline
function pump_offline_status_additions(pmpno){

    //make offline pumps unclickable, by removing onclick attribute
    $("img.product-disable-offline").removeAttr("onclick");

    // remove cover if pump offline--
    $('#scover-' + pmpno).attr('hidden', true);
    $('#scover-grey-' + pmpno).attr('hidden', true);
 
}


$(document).ready(function(){

    //clear the cover when page is reloaded so that all pumps are covered
    localStorage.removeItem('scover_pump_no');

    // Check which pumps need grey cover and apply it
    for(i = 1; i <= {{env('MAX_PUMPS')}}; i++) {
        let grey_cover = localStorage.getItem('scover_grey_' + i);

        if (
            grey_cover != undefined &&
            grey_cover != null &&
            grey_cover != ''
        ) {

            // NOTE: Remeber to hide blue cover when grey is open
            // Add grey cover
            $('#scover-grey-' + i).attr('hidden', false);
            $('#scover-grey-' + i).css("cursor", "default");

            // Remove Blue cover
            $('#scover-' + i).attr('hidden', true);

            console.log('DocReady: Blue Cover=', i)
            let grey_cancel = localStorage.getItem('scover_grey_cancel_' + i)

            if (
                grey_cancel != undefined &&
                grey_cancel != null &&
                grey_cancel != ''
            ) {
                $('#scover-grey-btn-' + i).attr('hidden', false);
                $('#scover-grey-btn-' + i).css("cursor", "pointer");
            }
        } else {
            // Apply blue cover
            $('#scover-' + i).attr('hidden', false);
            $('#scover-' + i).css('cursor', 'pointer')

            // Remove grey
            $('#scover-grey-' + i).attr('hidden', true);

            // Remove cancel
            $('#scover-grey-btn-' + i).attr('hidden', true);

        }
    }

});

function custom_litre_to_amt(pmpno) {

    let litre = parseFloat($("#custom_litre_input_" + pmpno).val());
    let prd_price = $('#prd_price_' + pmpno).val();

    if (litre > 0) {

        amount = litre * prd_price;

        if (amount > 0) {
            set_amt(amount, id = 'fifty', pmpno, true);
            let amt = amount.toFixed(2);
            $("#custom_amount_input_" + pmpno).val(amt);
            $("#custom_amount_input_" + pmpno + '_buffer').val(amt);
            // $("#preset_amt-" + pmpno).val(amount);
        } else {
            $("#custom_amount_input_" + pmpno + '_buffer').val('');
            $("#custom_amount_input_" + pmpno).val('');
        }
    }
    $("#custom_litre_input_" + pmpno).val('');
    $("#custom_litre_input_" + pmpno + '_buffer').val('');

    $("#custom_amount_btn_" + pmpno).removeClass('custom-preset-disable');
    // $("#custom_amount_btn_" + pmpno).addClass('poa-button-preset');
    // $("#custom_litre_btn_" + pmpno).addClass('custom-preset-disable');
    $("#custom_litre_btn_" + pmpno).removeClass('poa-button-preset');
}

function calculate_custom_fuel_price(pmpno,price) {
    let litre = $(`#custom_litre_input_${pmpno}`).val();
    amount = liter * price;
    $(`#custom_litre_input_${pmpno}`).val('')

    if (amount > 0) {
        $("#custom_amount_btn_"+pmpno).removeClass('custom-preset-disable');
        $("#custom_amount_btn_"+pmpno).addClass('poa-button-preset');
    }

    amount = amount.toFixed(2);
    $(`#custom_amount_input_${pmpno}`).val(amount);
    // $("#productsModal").modal('hide');
}

function litre_to_myr_custom_amount(pmpno) {
    log2laravel('info', pmpno + ': ***** litre_to_myr_custom_amount() *****');
    if (debounce_pump_auth(pmpno))
        return

    display_litre_preset(false, pmpno)
    amount = parseFloat($("#custom_amount_input_" + pmpno).val());

    log2laravel('info', selected_pump + ': BG1 select_custom_amount: amount=' + amount);

    if (amount > 0) {
        set_amount(amount, );
        $("#custom_amount_input_" + selected_pump).val('');
        $("#custom_amount_input_" + selected_pump + '_buffer').val('');
    }

    $("#custom_amount_btn").addClass('custom-preset-disable');
    $("#custom_amount_btn").removeClass('poa-button-preset');
    $("#custom_litre_btn").addClass('custom-preset-disable');
    $("#custom_litre_btn").removeClass('poa-button-preset');
}

function display_litre_myr_preset(disp, pmpno) {
    var preset_type = pumpData['pump' + pmpno].preset_type;

    if (disp == true)
        $(`#disp_ltr-${pmpno}`).css('display', 'block')
    else
        $(`#disp_ltr-${pmpno}`).css('display', 'none')

}

function set_amt(dose, id = 'fifty', pmpno=0, from_ltr=false) {

    log2laravel('info', pmpno +
        ': ***** BG1 set_amount(' + dose + ') *****');

    dose = parseFloat(dose).toFixed(2);

    log2laravel('info', pmpno + ': BG1 dose = ' + dose);

    $('.button-number-amount').removeClass('selected_preset_button');
    $('#set-default-preset-button-' + pmpno + '-' + id).addClass('selected_preset_button');

    pumpData['pump' + pmpno].preset_type =
        "{{ empty($company->currency->code) ? 'MYR' : $company->currency->code }}"

    pumpData['pump' + pmpno].dose = dose;

    log2laravel('info', pmpno + ': set_amount: BG1 dose=' +
        pumpData['pump' + pmpno].dose);

    $('#total_amount-main-' + pmpno).text(dose);

    log2laravel('info', pmpno + ': set_amount: BG1 dose=' + dose);

    // FP Fuel is being updated here
    localStorage.setItem('pump_data_fuel_' + pmpno, dose);
    var pdata = localStorage.getItem('pump_data_fuel_' + pmpno);
    $('#total-fuel-pump-' + pmpno).text(pdata);

    log2laravel('info', pmpno + ': set_amount: BG1 total-fuel-pump-' +
        pmpno + '=' + $('#total-fuel-pump-' + pmpno).text());

        if (from_ltr) {
            //$('#preset_amt_pump_' + selected_pump).text(dose);
        } else {
            $('#preset_amt_pump_' + selected_pump).text(dose);
            enable_payment_btns()
        }

    update_payment_table(pmpno);
    //
}

function convert_litres_to_myr(pmpno) {
    let litre = parseFloat($("#custom_litre_input_" + pmpno).val());
    let prd_price = $('#prd_price_' + pmpno).val();

    if (litre > 0) {

        amount = litre * prd_price;

        if (amount > 0) {
            set_amt(amount, id = 'fifty', pmpno, true);
            let amt = amount.toFixed(2);
            $("#custom_amount_input_" + pmpno).val(amt);
            $("#custom_amount_input_" + pmpno + '_buffer').val(amt);
            // $("#preset_amt-" + pmpno).val(amount);
        } else {
            $("#custom_amount_input_" + pmpno + '_buffer').val('');
            $("#custom_amount_input_" + pmpno).val('');
        }
    }

    $("#custom_litre_input_" + pmpno).val('');

    $("#custom_amount_btn_" + pmpno).removeClass('custom-preset-disable');
    $("#custom_amount_btn_" + pmpno).addClass('poa-button-preset');
    $("#custom_litre_btn_" + pmpno).addClass('custom-preset-disable');
    $("#custom_litre_btn_" + pmpno).removeClass('poa-button-preset');
}


function disable_ltr_myr(pmpno) {
    $("#custom_amount_btn_" + pmpno).addClass('custom-preset-disable');
    $("#custom_amount_btn_" + pmpno).removeClass('poa-button-preset');
    $("#custom_litre_btn_" + pmpno).addClass('custom-preset-disable');
    $("#custom_litre_btn_" + pmpno).removeClass('poa-button-preset');
}






</script>
