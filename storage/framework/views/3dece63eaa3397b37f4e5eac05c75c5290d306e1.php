<?php echo $__env->make('common.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->startSection('styles'); ?>
<style>
.modal-inside .row {
	padding: 0px;
	margin: 0px;
	color: #000;
}

.modal-body {
	position: relative;
	flex: 1 1 auto;
}
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_processing,
.dataTables_wrapper .dataTables_paginate {
	color: black;
}

th, td {
	vertical-align: middle !important;
	text-align: center
}

label, .dataTables_info,
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_processing,
.dataTables_wrapper .dataTables_paginate {
	color: #000 !important;
}

.active_button {
	color: #ccc;
	border: 1px #ccc solid;
}

.active_button:hover, .active_button:active,
.active_button_activated {
	background: transparent !important;
	color: #34dabb !important;
	border: 1px #34dabb solid !important;
	font-weight: bold;
}

.active_button_activated {
	background: transparent;
	color: #34dabb;
	border: 1px #34dabb solid;
	font-weight: bold;
}

.slim-cell {
	padding-top: 2px !important;
	padding-bottom: 2px !important;
}

.pd_column {
	padding-top: 10px;
}

tr {
	height: 40px
}
/* Redundant. This is internal
.modal-content {
    position: relative;
    -ms-flex: 1 1 auto;
    flex: 1 1 auto;
    padding: 1rem;
}
*/
.bg-modal {
    color: white;
    border-color: rgba(0,0,255,0.8);
    background-color: rgba(0,0,255,0.8);
}


</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('common.menubuttons', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->startSection('content'); ?>
<?php
if ($data) {
	$creditactId = $data[0]['creditact_id'];
    $company_name = $data[0]['company_name'];
}else{
	$creditactId = '';
    $company_name = '';
}

?>
<div class="container-fluid">
	<div class="d-flex mt-0 mb-0 p-0"
		 style="height:75px;margin-bottom:5px !important">
		<div style="" class="p-0 align-self-center col-sm-4">
			<h2 class="mb-0">Credit Account Ledger</h2>
		</div>
        <div style="" class="p-0 align-self-center col-sm-7">
			<h3 class="mb-0"><?php echo e($company_name); ?></h3>
		</div>
		<div style="" class="col-sm-1 p-0 align-self-center text-right">
			<button onclick="openPaymentModal(<?php echo e($creditactId); ?>)"
				class="btn btn-success sellerbutton mb-0 mr-0"
				style="padding:0px;float:right;margin-left:5px;
				border-radius:10px" id="stockin_btn">Payment
			</button>
		</div>
	</div>

	<div class="col-sm-12 pl-0 pr-0" style="">
		<table class="table table-bordered boxhead" id="creditact_table">
			<thead class="thead-dark">
			<tr style="">
				<th class="text-center" style="width:30px;">No</th>
				<th class="text-left" >Document&nbsp;No</th>
				<th class="text-center"   style="width:200px;">Last&nbsp;Update</th>
				<th class="text-right" style="width:100px;">Amount</th>
			</tr>
			</thead>
			<tbody id="">

			</tbody>
		</table>
	</div>
</div>
<div class="modal fade" id="evReceiptDetailModal" tabindex="-1" role="dialog">
	<div class="modal-dialog  modal-dialog-centered"
		style="width: 366px; margin-top: 0!important;margin-bottom: 0!important;">

		<!-- Modal content-->
		<div class="modal-content  modal-inside detail_view" id="d">
		</div>
	</div>
</div>

<div class="modal fade" id="paymentModal" tabindex="-1"
	 role="dialog" aria-labelledby="staffNameLabel" aria-hidden="true">
	 <div class="modal-dialog modal-dialog-centered"
		 role="document" style="width: 300px">
		<div class="modal-content bg-modal"  style=""	>

			<div class="modal-body bg-purplelobster" style="">
				<div class='text-right ' style="margin:auto">

						<div class="typewriter"
						id="retail_price_normal_fk_text"
						style="padding: 6px 12px 6px 12px;
						background-color: white; color: #0c0c0c; ">0.00
					</div>

					<input type="text" id="retail_price_normal_fk"
						style="text-align:right; margin-top: -14%; opacity: 0"
						class="form-control"
						placeholder='0.00' value="0"/>

					<input type="hidden" id="retail_price_normal_fk_buffer"
						style="text-align:right; " class="form-control"
						placeholder='' value="0"/>

				   <input type="hidden" id='retail_price_normal'/>
                    <input type="hidden" id="pro_id"
                        style="text-align:right" value=""
                        class="form-control" placeholder='0'/>


				</div>
			</div>
		</div>
	</div>
</div>

<style>
.btn {
	color: #fff !Important;
}

.form-control:disabled, .form-control[readonly] {
	background-color: #e9ecef !important;
	opacity: 1;
}

#void_stamp {
	font-size: 100px;
	color: red;
	position: absolute;
	z-index: 2;
	font-weight: 500;
	margin-top: 130px;
	margin-left: 10%;
	transform: rotate(45deg);
	display: none;
}
</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
<script>

var table = $('#creditact_table').DataTable({
	"processing": true,
  	"serverSide": true,
  	"autoWidth": false,
  	"ajax": {
  		"url": "<?php echo e(route('creditacountledger.datatable')); ?>",
  		"type": "POST",
  		data: {
			  'id': "<?php echo e($id); ?>"
  		},
  		'headers': {
  			'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
  		},
  	},

	columns: [{
		data: 'DT_RowIndex',
		name: 'DT_RowIndex'
		},		{
			data: 'sysid',
			name: 'sysid',
			render: function (data, type, row, meta) {
				url = row['receipt_id'];
				src = '"'+row['source']+'"';
				if (row['voided']) {

					switch (row['source']) {
						case 'fulltank':
						    return "<a href='javascript:void(0)'  style='text-decoration:none;color:blue' onclick='getFtFuelReceiptlist("+url+","+src+")' >"+row['sysid']+"</a>";
						    break;

						case 'fuel':
						    return "<a href='javascript:void(0)'  style='text-decoration:none;color:blue' onclick='getFuelReceiptlist("+url+","+src+")' >"+row['sysid']+"</a>";
						    break;
						}

				} else {
					if (row['sysid'] == 'Payment') {
						return ""+row['sysid']+"";
					}else{

						switch (row['source']) {
							case 'fulltank':
							    return "<a href='javascript:void(0)'  style='text-decoration:none;color:blue' onclick='getFtFuelReceiptlist("+url+","+src+")' >"+row['sysid']+"</a>";
							    break;

							case 'fuel':
							    return "<a href='javascript:void(0)'  style='text-decoration:none;' onclick='getFuelReceiptlist("+url+","+src+")' >"+row['sysid']+"</a>"
							    break;

							case 'refunded':
							    return "<a href='javascript:void(0)'  style='text-decoration:none;' onclick='getFuelReceiptlist("+url+","+src+")' >"+row['sysid']+"</a>"
								break;
							}
					}
				}
			}
		}, {
			data: 'date',
			name: 'date',
		},	{
			data: 'amount',
			name: 'amount',
			}],
			columnDefs: [
		{
			targets: 1,
			className: 'text-left'
		},
		{
			targets: 3,
			className: 'text-right'
		}
  	],
	"rowCallback": function(row, data, index){
		if(data['voided'] != null){
			$(row).find('td:eq(1)').css('color', 'white');
			$(row).find('td:eq(1)').css('text-decoration', 'none');

			$(row).find('td:eq(1)').css('background-color', 'red');
		} else if (data.refunded) {
			$(row).find('td:eq(1)').css('color', 'white');
			$(row).find('td:eq(1)').css('text-decoration', 'none');

			$(row).find('td:eq(1)').css('background-color', 'rgb(255, 126, 48)');
		}
	},

});


$.ajaxSetup({
 	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
});


function getFuelReceiptlist(fid,src) {
	$.ajax({
		method: "post",
		url: "<?php echo e(route('fuel.envReceipt')); ?>",
		data: {
			id: fid,
			source:src
		}
	}).done((data) => {
		$(".detail_view").html(data);
		$("#evReceiptDetailModal").modal('show');
	})
	.fail((data) => {
		console.log("data", data)
	});
}


function getFtFuelReceiptlist(data) {
	$.ajax({
			method: "post",
			url: "<?php echo e(route('fulltank.envReceipt')); ?>",
			data: {
				id: data
			}
		}).done((data) => {
			$(".detail_view").html(data);
			$("#evReceiptDetailModal").modal('show');
		})
		.fail((data) => {
			console.log("data", data)
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


let old_value = "";

function openPaymentModal(id) {
	$("#pro_id").attr("value", id);

	if (parseInt(old_value) > 0) {
		$("#retail_price_normal_fk").val(atm_money(old_value));
		$("#retail_price_normal_fk_text").text(atm_money(old_value));
		console.log('op')
	} else {
		$("#retail_price_normal_fk").val('');
		$("#retail_price_normal_fk_text").text("0.00");
	}


	$("#retail_price_normal, #retail_price_normal_fk").val(0);
	$("#retail_price_normal_fk_text").text('0.00');

	$("#paymentModal").modal("show");
}


$("#retail_price_normal_fk").on("keyup keypress", function (evt) {
	let type_evt_not_use = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];

	if (evt.type === "keypress") {
		let value = $("#retail_price_normal_fk").val();
		console.log("value", value);
		old_value = parseInt(value.replace('.', ''));
		$("#retail_price_normal").val(old_value == '' ? 0 : old_value);

	} else {
		if (evt.key === "Backspace") {
			let value = $("#retail_price_normal_fk").val();
			console.log("value", value);
			old_value = parseInt(value.replace('.', ''));
			$("#retail_price_normal").val(old_value);
		}
		let use_key = "";
		if (type_evt_not_use.includes(evt.key)) {
			use_key = evt.key;
			console.log(evt.key);
		}

		old_value = parseInt((isNaN($("#retail_price_normal").val()) == false ? $("#retail_price_normal").val() : 0) + "" + use_key);
		let nan = isNaN(old_value);
		console.log("up", old_value);

		if (old_value !== "" && nan == false) {
			$("#retail_price_normal_fk").val(atm_money(parseInt(old_value)));
			$("#retail_price_normal_fk_text").text(atm_money(parseInt(old_value)));
			$("#retail_price_normal").val(parseInt(old_value));
		} else {
			$("#retail_price_normal_fk").val(0);
			$("#retail_price_normal_fk_text").text("0.00");
			$("#retail_price_normal").val(0);
		}
	}
});


$('#paymentModal').on('hidden.bs.modal', function (e) {
	let source = "payment";
	let value = $("#retail_price_normal").val();
	let creditact_id = $("#pro_id").val();
				let src_url = "<?php echo e(Request::url()); ?>"

	$.ajax({
		method: "post",
		url: "<?php echo e(route('creditact.newPayment')); ?>",
		data: {source: source, value: value, creditact_id: creditact_id, src_url: src_url}

	})
	.done((data) => {

		console.log("data", data.error);
		table.ajax.reload();
		$("#retail_price_normal, #retail_price_normal_fk").val(0);
		$("#retail_price_normal_fk_text").text('0.00');
		console.log('G')
		return


	})
	.fail((data) => {
		console.log("data", data)
	});
});


</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('common.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php echo $__env->make('common.web', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/emmanuel/oceania/trunk/oceania/resources/views/credit_ac/credit_account_ledger.blade.php ENDPATH**/ ?>