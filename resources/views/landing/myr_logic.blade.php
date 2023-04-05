<!-- myr_logic BEGINS -->
<script>
// Implement ATM Number formatting.
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
/*
function format_key_input(evt)
{
    let id = '#' + evt.target.id;
	let old_value = "";
    let type_evt_not_use = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];

    let keyed_value = $(id).val()
    keyed_value = parseFloat(keyed_value);
    if(keyed_value < 0 || keyed_value >= 99999.99)
    {
        console.log('invalid amount entered. :(');
        const val = $(id).val();
        var new_value = val.slice(0, -1);
        $(id).val(atm_money(parseInt(new_value)));
        return;
    }

    if (evt.type === "keypress") {
        let value = $(id).val();
        old_value = parseInt(value.replace('.', ''));
        console.log(old_value);
        $(id).val(old_value == '' ? 0 : old_value);
    }
	else {
        if (evt.key === "Backspace") {
            let value = $(id).val();
            old_value = parseInt(value.replace('.', ''));
            $(id).val(old_value);
        }

		old_value = parseInt((isNaN($(id).val()) == false ? $(id).val() : 0));
        let nan = isNaN(old_value);

        if (old_value !== "" && nan == false) {
			console.log("up-value", old_value);
            $(id).val(atm_money(parseInt(old_value)));
        } else {
            disable_payment_btns();
            $(id).val("0.00");
        }
    }
}
*/
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

        if(old_val.length >= 6)
        {
            console.log(old_val);
            $(buffer_in).val(old_val.splice(0, -1));
            console.log('fresh', $(buffer_in).val());
        }else {

            if (old_val === '0.00') {
                $(buffer_in).val('')
                $(target_field).val('')
                old_val = ''
            }
            $(buffer_in).val('' + old_val + input)
            $(target_field).val(atm_money(parseInt($(buffer_in).val())))
        }
    });
}

for(var i=1; i<={{env('MAX_PUMPS')}}; i++) {
    filter_price(`#myr_amount_input_${i}`,`#myr_amount_input_${i}_buffer`);
    filter_price(`#litre_amount_input_${i}`,`#litre_amount_input_${i}_buffer`);
}

// Implement MYR Onclick btn
function process_custom_amt(pump_no) {
    var product = $(`#prd-name-${pump_no}`).text()
    var myr_amount = $(`#myr_amount_input_${pump_no}`).val();
    if(myr_amount != undefined && myr_amount != null){
        var price = myr_amount;
        $(`#prd-myr-${pump_no}`).text(price);
        $(`#myr_amount_input_${pump_no}`).val('');
        $(`#myr_amount_input_${pump_no}_buffer`).val('');

        let f_price = parseFloat(price);
        if(f_price > 0){
            let price_litre = $(`#fuel-product-price-${pump_no}`).text();
            pumpData['pump' + pump_no].product = product;
            pumpData['pump' + pump_no].price_liter = parseFloat(price_litre);
            pumpData['pump' + pump_no].amount = price;
            let dose = parseFloat(price).toFixed(2);
            pumpData['pump' + pump_no].dose = parseFloat(price).toFixed(2);
            pumpData['pump' + pump_no].preset_type =
            "{{ empty($company->currency->code) ? 'MYR' : $company->currency->code }}";
            $('#total_amount-main-' + pump_no).text(dose);

            // FP Fuel is being updated here
            localStorage.setItem('pump_data_fuel_' + pump_no, dose);
            var pdata = localStorage.getItem('pump_data_fuel_' + pump_no);
            $('#total-fuel-pump-' + pump_no).text(pdata);


            console.log(pumpData);
            update_payment_table(pump_no);
            enable_payment_btns();
        }else {
            disable_payment_btns();
        }
    }
}

</script>
<!-- myr_logic ENDS -->
