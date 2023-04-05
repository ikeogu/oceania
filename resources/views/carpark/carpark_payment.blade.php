<script>
filter_price("#cash_received_input","#cash_received_input_normal");

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

$("#cash_received_input").on("keyup keypress", function (evt) {

	var dis_cash	= $("#cash_received_input").val();
	var total		= total_save;

	if( parseFloat(dis_cash) > parseFloat(total) ){
	
		$(this).attr('disabled',true);
	}

	if (pay_as_click) {

		if( $("#cash_received_input_normal").val().trim() == "" ){
			return;
		}

		if (evt.type === "keyup") {
			cash_value = ($("#cash_received_input_normal").val() != "" ? parseInt($("#cash_received_input_normal").val()) : 0);

			if (cash_value < parseInt(amount_pay)) {
				$("#enter-btn").addClass("poa-button-number-payment-enter-cp-disabled");
				$("#enter-btn").removeClass("poa-button-number-payment-enter-cp-active");
				console.log("juju");

			} else {
				$("#enter-btn").removeClass("poa-button-number-payment-enter-cp-disabled");
				$("#enter-btn").addClass("poa-button-number-payment-enter-cp-active");
				console.log("juju2");

			}

		}
		payment_type = "cash";
		var cash_change = $("#cash_received_input").val() - total_save;
		$('#change').text(cash_change.toFixed(2));

	} else {
		$("#cash_received_input").val(atm_money(parseInt(0)));
	}

});


function set_cash() {
	$('#change').text('0.00');
	$("#cash_received_input_normal").val('');
	$("#cash_received_input").val('');
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

function process_finish() {

	$.ajax({
		method: "GET",
		url: "{{route('ev-receipt-id')}}",
	}).done((data) => {
		
		let ev_receipt_id = data.ev_receipt_id;
		ev_receipt_date = data.ev_receipt_date;
		var ev_receipt_data = {
			"No": "1",
			"ev_receipt_date": ev_receipt_date,
			"receipt_id": ev_receipt_id,
			"amount": $('#total').text(),
		};

		localStorage.removeItem('add_ev_receipt_list')
		localStorage.setItem("add_ev_receipt_list", JSON.stringify(ev_receipt_data));
		$("#enter-btn").removeClass("poa-button-number-payment-enter-cp-active");
		$("#enter-btn").addClass("poa-button-number-payment-enter-cp-disabled");
		var data = {
			ev_receipt_id: ev_receipt_id,
			service_tax: service_tax,
			payment_type: payment_type,
			change_amount: $('#change').text(),
			round: round,
			carparkoper_id: carparkoper_id,
			description: description_save,
			hours: hours_save,
			rate: Number(rate_save).toFixed(2),
			kwh: kwh_save,
			myr: myr_save,
			cal_rounding: round,
			itemAmount: itemAmount_save.toFixed(2),
			sst: tax_save.toFixed(2),
			total: number_format(total_save, 2),
			carparklot_id: carparklot_id,
			cash_received: $("#cash_received_input").val()
		};

		$.ajax({
			method: "post",
			url: "{{route('create-ev-list')}}",
			data: data,
		}).done((response) => {
			
			reload_carparklot_table(response);
			
			$("#rounding").text("0.00");
			$("#total").text("0.00");
			$("#description").text("Electric Charge");
			$("#hours").text("0.00");
			$("#rate").text("0.00");
			$("#amount").text("0.00");
			$("#itemAmount").text("0.00");
			$("#tax").text("0.00");
			$('#change').text("0.00");
			$("#row_detail").css("opacity", "0");

			$("#cash_received_input").css("opacity", "1");
			
			$(".poa-button-cash").addClass("poa-button-cash-disabled");
			$(".poa-button-credit-card").addClass("poa-button-credit-card-disabled");
			$(".opos-button-wallet").addClass("opos-button-wallet-disabled");
			
			set_cash();

			if( payment_type == 'creditcard' || payment_type == 'wallet' ){
				$("#cash_received_input").css("opacity", "0");
			}else{
				$("#cash_received_input").css("opacity", "1");
			}

			pay_as_click = false;
			localStorage.removeItem('reload_ev_receipt_list')
			localStorage.setItem("reload_ev_receipt_list", "yes");

		}).fail((data) => {
			console.log("data", data)
		});
	});
}

function reload_carparklot_table(response){
	$("#table_content").html(response);
}
</script>
