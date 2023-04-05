function litre_to_amt(pmpno) {

    if (debounce_pump_auth(pmpno))
        return

    litre = parseFloat($("#custom_litre_input_" + pmpno).val());

    if (litre > 0) {
        let liter = litre

        let price = $("#prd-myr-" + pmpno + '-price').text()

        amount = liter * price;

        $(`#custom_litre_input_${selected_pump}`).val('')

        if (amount > 0) {
            // $("#custom_amount_btn_"+pmpno).removeClass('custom-preset-disable');
            // $("#custom_amount_btn_"+pmpno).addClass('poa-button-preset');

            $("#custom_amount_btn_"+pmpno).removeClass('cover-myr-disable');
            $("#custom_amount_btn_"+pmpno).addClass('cover-myr-enable');
        }

        amount = amount.toFixed(2);

        $("#custom_amount_input_"+pmpno).val(amount);
        $("#productsModal").modal('hide');

    }

    $("#custom_litre_input_"+pmpno+"_buffer").val('');
    $("#custom_litre_input_"+pmpno).val('');


    $("#custom_amount_btn_"+pmpno).removeClass('cover-myr-disable');
    $("#custom_amount_btn_"+pmpno).addClass('cover-myr-enable');

    $("#custom_litre_btn_"+pmpno).removeClass('cover-myr-enable');
    $("#custom_litre_btn_"+pmpno).addClass('cover-myr-disable');
}

