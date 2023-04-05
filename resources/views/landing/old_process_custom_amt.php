function process_custom_amt(pmpno, prd_id) {

    let input_amt = $('#custom_amount_input_'+pmpno+'_buffer').val()
    let display_input_amt = $('#custom_amount_input_'+pmpno).val()

    $('#prd-myr-' + pmpno).text(display_input_amt)

    let prd = $("#prd-name-"+pmpno).text()
    let prd_price = $("#prd-myr-"+pmpno+"-price").text()

    pumpData['pump' + pmpno].product = prd
    pumpData['pump' + pmpno].price_liter = prd_price

    $("#custom_amount_btn_"+pmpno).removeClass('cover-myr-enable');
    $("#custom_amount_btn_"+pmpno).removeClass('poa-button-preset');
    $("#custom_amount_btn_"+pmpno).addClass('cover-myr-disable');

    $('#custom_amount_input_'+pmpno+'_buffer').val('')
    $('#custom_amount_input_'+pmpno).val('')
    
}

